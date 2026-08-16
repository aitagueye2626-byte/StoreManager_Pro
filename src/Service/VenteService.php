<?php

use App\Core\Database;

class StockInsuffisantException extends \Exception
{
}

class LimiteCreditDepasseeException extends \Exception
{
}

class VenteService
{
    private PDO $pdo;
    private ClientRepository $clientRepository;
    private ProduitRepository $produitRepository;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnexion();
        $this->clientRepository = new ClientRepository();
        $this->produitRepository = new ProduitRepository();
    }

    public function validerVente(
        int $clientId,
        int $utilisateurId,
        array $panier,
        float $montantVerse,
        string $modePaiement
    ): array {

        if (empty($panier)) {
            throw new \InvalidArgumentException(
                'Le panier ne peut pas être vide.'
            );
        }

        $lignesCalculees = [];
        $montantTotal = 0.0;

        foreach ($panier as $item) {

            $produit = $this->produitRepository->findById(
                (int) $item['produit_id']
            );

            if ($produit === null) {
                throw new \InvalidArgumentException(
                    "Produit introuvable : id {$item['produit_id']}"
                );
            }

            $quantite = (int) $item['quantite'];

            $sousTotal = $produit->getPrix() * $quantite;

            $lignesCalculees[] = [
                'produit_id'    => $produit->getId(),
                'quantite'      => $quantite,
                'prix_unitaire' => $produit->getPrix(),
                'sous_total'    => $sousTotal,
            ];

            $montantTotal += $sousTotal;
        }

        $resteAPayer = $montantTotal - $montantVerse;

        if ($resteAPayer > 0) {

            $client = $this->clientRepository->findById($clientId);

            if ($client === null) {
                throw new \InvalidArgumentException(
                    "Client introuvable : id {$clientId}"
                );
            }

            $detteActiveActuelle =
                $this->getTotalDettesActives($clientId);

            $detteApresCetteVente =
                $detteActiveActuelle + $resteAPayer;

            if ($detteApresCetteVente > $client->getLimiteCredit()) {

                throw new LimiteCreditDepasseeException(
                    "Limite de crédit dépassée pour {$client->getNom()} : "
                    . "dette actuelle {$detteActiveActuelle} F + "
                    . "{$resteAPayer} F "
                    . "dépasserait la limite de "
                    . "{$client->getLimiteCredit()} F."
                );
            }
        }

        $this->pdo->beginTransaction();

        try {
            $stmtVente = $this->pdo->prepare(
                'INSERT INTO vente (
                    client_id,
                    utilisateur_id,
                    montant_total,
                    statut
                )
                VALUES (
                    :client_id,
                    :utilisateur_id,
                    :montant_total,
                    :statut
                )
                RETURNING id'
            );

            $stmtVente->execute([
                'client_id'      => $clientId,
                'utilisateur_id' => $utilisateurId,
                'montant_total'  => $montantTotal,
                'statut'         => 'VALIDEE',
            ]);

            $venteId = (int) $stmtVente->fetchColumn();


            foreach ($lignesCalculees as $ligne) {

                $stockOk = $this->produitRepository->decrementerStock(
                    $ligne['produit_id'],
                    $ligne['quantite']
                );

                if (!$stockOk) {

                    throw new StockInsuffisantException(
                        "Stock insuffisant pour le produit id "
                        . "{$ligne['produit_id']}."
                    );
                }

                $stmtLigne = $this->pdo->prepare(
                    'INSERT INTO ligne_vente (
                        vente_id,
                        produit_id,
                        quantite,
                        prix_unitaire
                    )
                    VALUES (
                        :vente_id,
                        :produit_id,
                        :quantite,
                        :prix_unitaire
                    )'
                );

                $stmtLigne->execute([
                    'vente_id'      => $venteId,
                    'produit_id'    => $ligne['produit_id'],
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                ]);
            }


            if ($montantVerse > 0) {

                $stmtPaiement = $this->pdo->prepare(
                    'INSERT INTO paiement (
                        vente_id,
                        montant,
                        mode_paiement,
                        statut
                    )
                    VALUES (
                        :vente_id,
                        :montant,
                        :mode_paiement,
                        :statut
                    )'
                );

                $stmtPaiement->execute([
                    'vente_id'      => $venteId,
                    'montant'       => $montantVerse,
                    'mode_paiement' => $modePaiement,
                    'statut'        => 'VALIDE',
                ]);
            }


            if ($resteAPayer > 0) {

                $stmtDette = $this->pdo->prepare(
                    'INSERT INTO dette (
                        vente_id,
                        client_id,
                        montant,
                        montant_restant,
                        statut
                    )
                    VALUES (
                        :vente_id,
                        :client_id,
                        :montant,
                        :montant_restant,
                        :statut
                    )'
                );

                $stmtDette->execute([
                    'vente_id'        => $venteId,
                    'client_id'       => $clientId,
                    'montant'         => $montantTotal,
                    'montant_restant' => $resteAPayer,
                    'statut'          => 'NON SOLDEE',
                ]);
            }


            $this->pdo->commit();

        } catch (\Throwable $e) {

            $this->pdo->rollBack();

            throw $e;
        }


        return [
            'vente_id'      => $venteId,
            'montant_total' => $montantTotal,
            'reste_du'      => $resteAPayer,
        ];
    }


    private function getTotalDettesActives(int $clientId): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(montant_restant), 0)
             FROM dette
             WHERE client_id = :client_id
             AND statut = 'NON SOLDEE'"
        );

        $stmt->execute([
            'client_id' => $clientId
        ]);

        return (float) $stmt->fetchColumn();
    }
}
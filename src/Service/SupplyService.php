<?php

use App\Core\Database;
use PDO;


class SupplyService
{
    private PDO $pdo;
    private ProduitRepository $produitRepository;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnexion();
        $this->produitRepository = new ProduitRepository();
    }

    public function listerEnAttente(): array
    {
        return $this->listerParStatut('EN ATTENTE');
    }

    public function listerReceptionnes(): array
    {
        return $this->listerParStatut('RECEPTIONNE');
    }

    private function listerParStatut(string $statut): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.*, f.nom AS fournisseur_nom, f.telephone AS fournisseur_telephone
             FROM approvisionnement a
             JOIN fournisseur f ON f.id = a.fournisseur_id
             WHERE a.statut = :statut
             ORDER BY a.date DESC"
        );
        $stmt->execute(['statut' => $statut]);

        $resultat = [];
        foreach ($stmt->fetchAll() as $ligne) {
            $resultat[] = [
                'approvisionnement'    => $this->hydraterApprovisionnement($ligne),
                'fournisseur_nom'      => $ligne['fournisseur_nom'],
                'fournisseur_telephone' => $ligne['fournisseur_telephone'],
                'lignes'               => $this->findLignesParApprovisionnement((int) $ligne['id']),
            ];
        }

        return $resultat;
    }

    private function hydraterApprovisionnement(array $ligne): Approvisionnement
    {
        return new Approvisionnement(
            (int) $ligne['id'],
            (int) $ligne['fournisseur_id'],
            (int) $ligne['utilisateur_id'],
            $ligne['date'],
            (float) $ligne['montant_total'],
            $ligne['statut']
        );
    }

    private function findLignesParApprovisionnement(int $approvisionnementId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT la.*, p.nom AS produit_nom
             FROM ligne_approvisionnement la
             JOIN produit p ON p.id = la.produit_id
             WHERE la.approvisionnement_id = :id'
        );
        $stmt->execute(['id' => $approvisionnementId]);

        $resultat = [];
        foreach ($stmt->fetchAll() as $ligne) {
            $resultat[] = [
                'ligne' => new LigneApprovisionnement(
                    (int) $ligne['id'],
                    (int) $ligne['approvisionnement_id'],
                    (int) $ligne['produit_id'],
                    (int) $ligne['quantite'],
                    (float) $ligne['prix_unitaire'],
                    (float) $ligne['sous_total']
                ),
                'produit_nom' => $ligne['produit_nom'],
            ];
        }

        return $resultat;
    }

    public function receptionner(int $approvisionnementId): array
    {
        $lignes = $this->findLignesParApprovisionnement($approvisionnementId);

        if (empty($lignes)) {
            throw new \InvalidArgumentException("Bon de livraison introuvable ou sans lignes : id {$approvisionnementId}");
        }

        $this->pdo->beginTransaction();

        try {
            foreach ($lignes as $item) {
                $ligne = $item['ligne'];
                $this->produitRepository->incrementerStock($ligne->getProduitId(), $ligne->getQuantite());
            }

            $stmt = $this->pdo->prepare(
                "UPDATE approvisionnement SET statut = 'RECEPTIONNE' WHERE id = :id"
            );
            $stmt->execute(['id' => $approvisionnementId]);

            $this->pdo->commit();

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return [
            'approvisionnement_id' => $approvisionnementId,
            'nombre_lignes'        => count($lignes),
            'statut'               => 'RECEPTIONNE',
        ];
    }
}
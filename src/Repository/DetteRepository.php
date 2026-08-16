<?php

use App\Core\Database;
use PDO;


class DetteRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnexion();
    }

    private function hydrater(array $ligne): Dette
    {
        return new Dette(
            (int) $ligne['id'],
            (int) $ligne['vente_id'],
            (int) $ligne['client_id'],
            (float) $ligne['montant'],
            (float) $ligne['montant_restant'],
            $ligne['statut'],
            $ligne['date']
        );
    }

    private function hydraterRemboursement(array $ligne): Remboursement
    {
        return new Remboursement(
            (int) $ligne['id'],
            (int) $ligne['dette_id'],
            (float) $ligne['montant'],
            $ligne['mode_paiement'],
            $ligne['date']
        );
    }

    public function findAllActivesAvecClient(): array
    {
        $stmt = $this->pdo->query(
            "SELECT d.*, c.nom AS client_nom, c.prenom AS client_prenom, c.telephone AS client_telephone
             FROM dette d
             JOIN client c ON c.id = d.client_id
             WHERE d.statut = 'NON SOLDEE'
             ORDER BY d.date DESC"
        );

        $resultat = [];
        foreach ($stmt->fetchAll() as $ligne) {
            $resultat[] = [
                'dette'            => $this->hydrater($ligne),
                'client_nom'       => $ligne['client_nom'],
                'client_prenom'    => $ligne['client_prenom'],
                'client_telephone' => $ligne['client_telephone'],
            ];
        }

        return $resultat;
    }

    public function findById(int $id): ?Dette
    {
        $stmt = $this->pdo->prepare('SELECT * FROM dette WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $ligne = $stmt->fetch();

        return $ligne ? $this->hydrater($ligne) : null;
    }

    public function findRemboursementsParDette(int $detteId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM remboursement WHERE dette_id = :dette_id ORDER BY date ASC'
        );
        $stmt->execute(['dette_id' => $detteId]);

        return array_map(
            fn (array $ligne) => $this->hydraterRemboursement($ligne),
            $stmt->fetchAll()
        );
    }

    public function findLignesVenteParDette(int $venteId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT lv.quantite, lv.prix_unitaire, lv.sous_total, p.nom AS produit_nom
             FROM ligne_vente lv
             JOIN produit p ON p.id = lv.produit_id
             WHERE lv.vente_id = :vente_id'
        );
        $stmt->execute(['vente_id' => $venteId]);

        return $stmt->fetchAll();
    }

    public function enregistrerRemboursement(int $detteId, float $montant, string $modePaiement): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO remboursement (dette_id, montant, mode_paiement)
             VALUES (:dette_id, :montant, :mode_paiement)'
        );
        $stmt->execute([
            'dette_id'      => $detteId,
            'montant'       => $montant,
            'mode_paiement' => $modePaiement,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function mettreAJourSolde(int $detteId, float $nouveauMontantRestant, string $nouveauStatut): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE dette SET montant_restant = :montant_restant, statut = :statut WHERE id = :id'
        );
        $stmt->execute([
            'montant_restant' => $nouveauMontantRestant,
            'statut'          => $nouveauStatut,
            'id'              => $detteId,
        ]);
    }

    public function getStatsGlobales(): array
    {
        $creancesActives = (float) $this->pdo->query(
            "SELECT COALESCE(SUM(montant_restant), 0) FROM dette WHERE statut = 'NON SOLDEE'"
        )->fetchColumn();

        $clientsDebiteurs = (int) $this->pdo->query(
            "SELECT COUNT(DISTINCT client_id) FROM dette WHERE statut = 'NON SOLDEE'"
        )->fetchColumn();

        $totalRecouvrements = (float) $this->pdo->query(
            'SELECT COALESCE(SUM(montant), 0) FROM remboursement'
        )->fetchColumn();

        return [
            'creances_actives'    => $creancesActives,
            'clients_debiteurs'   => $clientsDebiteurs,
            'total_recouvrements' => $totalRecouvrements,
        ];
    }
}

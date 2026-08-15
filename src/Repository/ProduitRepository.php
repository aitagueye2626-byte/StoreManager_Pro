<?php

use App\Core\Database;
use PDO;

class ProduitRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnexion();
    }

    private function hydrater(array $ligne): Produit
    {
        return new Produit(
            (int) $ligne['id'],
            $ligne['nom'],
            $ligne['description'] ?? null,
            $ligne['categorie'] ?? null,
            (float) $ligne['prix'],
            (int) $ligne['seuil_alerte']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM produit ORDER BY nom ASC');

        return array_map(fn (array $ligne) => $this->hydrater($ligne), $stmt->fetchAll());
    }

    public function findById(int $id): ?Produit
    {
        $stmt = $this->pdo->prepare('SELECT * FROM produit WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $ligne = $stmt->fetch();

        return $ligne ? $this->hydrater($ligne) : null;
    }

    public function findAllAvecStock(): array
    {
        $stmt = $this->pdo->query(
            'SELECT p.*, s.quantite_disponible
             FROM produit p
             JOIN stock s ON s.produit_id = p.id
             ORDER BY p.nom ASC'
        );

        $resultat = [];
        foreach ($stmt->fetchAll() as $ligne) {
            $resultat[] = [
                'produit'  => $this->hydrater($ligne),
                'quantite' => (int) $ligne['quantite_disponible'],
            ];
        }

        return $resultat;
    }

    public function getQuantiteDisponible(int $produitId): int
    {
        $stmt = $this->pdo->prepare('SELECT quantite_disponible FROM stock WHERE produit_id = :produit_id');
        $stmt->execute(['produit_id' => $produitId]);

        $ligne = $stmt->fetch();

        return $ligne ? (int) $ligne['quantite_disponible'] : 0;
    }

    public function decrementerStock(int $produitId, int $quantite): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE stock
             SET quantite_disponible = quantite_disponible - :quantite,
                 date_mise_a_jour = CURRENT_TIMESTAMP
             WHERE produit_id = :produit_id AND quantite_disponible >= :quantite'
        );
        $stmt->execute(['quantite' => $quantite, 'produit_id' => $produitId]);

        return $stmt->rowCount() > 0;
    }

    public function incrementerStock(int $produitId, int $quantite): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE stock
             SET quantite_disponible = quantite_disponible + :quantite,
                 date_mise_a_jour = CURRENT_TIMESTAMP
             WHERE produit_id = :produit_id'
        );
        $stmt->execute(['quantite' => $quantite, 'produit_id' => $produitId]);
    }

    public function creer(
        string $nom,
        float $prix,
        ?string $description = null,
        ?string $categorie = null,
        int $seuilAlerte = 5
    ): Produit {
        $stmt = $this->pdo->prepare(
            'INSERT INTO produit (nom, description, categorie, prix, seuil_alerte)
             VALUES (:nom, :description, :categorie, :prix, :seuil_alerte)'
        );
        $stmt->execute([
            'nom'          => $nom,
            'description'  => $description,
            'categorie'    => $categorie,
            'prix'         => $prix,
            'seuil_alerte' => $seuilAlerte,
        ]);

        $nouvelId = (int) $this->pdo->lastInsertId();

        $stmtStock = $this->pdo->prepare('INSERT INTO stock (produit_id, quantite_disponible) VALUES (:produit_id, 0)');
        $stmtStock->execute(['produit_id' => $nouvelId]);

        return new Produit($nouvelId, $nom, $description, $categorie, $prix, $seuilAlerte);
    }

    public function mettreAJourPrix(Produit $produit): void
    {
        $stmt = $this->pdo->prepare('UPDATE produit SET prix = :prix WHERE id = :id');
        $stmt->execute([
            'prix' => $produit->getPrix(),
            'id'   => $produit->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM produit WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}

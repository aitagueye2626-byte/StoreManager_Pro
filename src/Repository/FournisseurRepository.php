<?php

use App\Core\Database;
use PDO;

class FournisseurRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnexion();
    }

    private function hydrater(array $ligne): Fournisseur
    {
        return new Fournisseur(
            (int) $ligne['id'],
            $ligne['nom'],
            $ligne['telephone'] ?? null,
            $ligne['email'] ?? null,
            $ligne['adresse'] ?? null
        );
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM fournisseur ORDER BY nom ASC');

        return array_map(fn (array $ligne) => $this->hydrater($ligne), $stmt->fetchAll());
    }

    public function findById(int $id): ?Fournisseur
    {
        $stmt = $this->pdo->prepare('SELECT * FROM fournisseur WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $ligne = $stmt->fetch();

        return $ligne ? $this->hydrater($ligne) : null;
    }

    public function creer(
        string $nom,
        ?string $telephone = null,
        ?string $email = null,
        ?string $adresse = null
    ): Fournisseur {
        $stmt = $this->pdo->prepare(
            'INSERT INTO fournisseur (nom, telephone, email, adresse)
             VALUES (:nom, :telephone, :email, :adresse)'
        );
        $stmt->execute([
            'nom'       => $nom,
            'telephone' => $telephone,
            'email'     => $email,
            'adresse'   => $adresse,
        ]);

        $nouvelId = (int) $this->pdo->lastInsertId();

        return new Fournisseur($nouvelId, $nom, $telephone, $email, $adresse);
    }

    public function mettreAJour(Fournisseur $fournisseur): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE fournisseur
             SET telephone = :telephone, email = :email, adresse = :adresse
             WHERE id = :id'
        );
        $stmt->execute([
            'telephone' => $fournisseur->getTelephone(),
            'email'     => $fournisseur->getEmail(),
            'adresse'   => $fournisseur->getAdresse(),
            'id'        => $fournisseur->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM fournisseur WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}

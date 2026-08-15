<?php

use App\Core\Database;
use PDO;

class ClientRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnexion();
    }

    private function hydrater(array $ligne): Client
    {
        return new Client(
            (int) $ligne['id'],
            $ligne['nom'],
            $ligne['prenom'] ?? null,
            $ligne['telephone'],
            $ligne['email'] ?? null,
            $ligne['adresse'] ?? null
        );
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM client ORDER BY nom ASC');

        return array_map(fn (array $ligne) => $this->hydrater($ligne), $stmt->fetchAll());
    }

    public function findById(int $id): ?Client
    {
        $stmt = $this->pdo->prepare('SELECT * FROM client WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $ligne = $stmt->fetch();

        return $ligne ? $this->hydrater($ligne) : null;
    }

    public function rechercherParNom(string $terme): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM client WHERE nom LIKE :terme OR telephone LIKE :terme ORDER BY nom ASC'
        );
        $stmt->execute(['terme' => '%' . $terme . '%']);

        return array_map(fn (array $ligne) => $this->hydrater($ligne), $stmt->fetchAll());
    }

    public function creer(
        string $nom,
        string $telephone,
        ?string $prenom = null,
        ?string $email = null,
        ?string $adresse = null
    ): Client {
        $stmt = $this->pdo->prepare(
            'INSERT INTO client (nom, prenom, telephone, email, adresse)
             VALUES (:nom, :prenom, :telephone, :email, :adresse)'
        );
        $stmt->execute([
            'nom'       => $nom,
            'prenom'    => $prenom,
            'telephone' => $telephone,
            'email'     => $email,
            'adresse'   => $adresse,
        ]);

        $nouvelId = (int) $this->pdo->lastInsertId();

        return new Client($nouvelId, $nom, $prenom, $telephone, $email, $adresse);
    }

    public function mettreAJour(Client $client): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE client
             SET telephone = :telephone, email = :email, adresse = :adresse
             WHERE id = :id'
        );
        $stmt->execute([
            'telephone' => $client->getTelephone(),
            'email'     => $client->getEmail(),
            'adresse'   => $client->getAdresse(),
            'id'        => $client->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM client WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}

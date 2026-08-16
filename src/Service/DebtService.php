<?php

use App\Core\Database;
use PDO;


class MontantRemboursementInvalideException extends \Exception
{
}


class DebtService
{
    private PDO $pdo;
    private DetteRepository $detteRepository;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnexion();
        $this->detteRepository = new DetteRepository();
    }

    public function rembourser(int $detteId, float $montant, string $modePaiement): array
    {
        $dette = $this->detteRepository->findById($detteId);

        if ($dette === null) {
            throw new \InvalidArgumentException("Dette introuvable : id {$detteId}");
        }

        if ($montant <= 0) {
            throw new MontantRemboursementInvalideException('Le montant du versement doit être positif.');
        }

        if ($montant > $dette->getMontantRestant()) {
            throw new MontantRemboursementInvalideException(
                sprintf(
                    'Le montant versé (%s F) dépasse le solde restant dû (%s F).',
                    number_format($montant, 0, ',', ' '),
                    number_format($dette->getMontantRestant(), 0, ',', ' ')
                )
            );
        }

        $this->pdo->beginTransaction();

        try {
            $this->detteRepository->enregistrerRemboursement($detteId, $montant, $modePaiement);

            $nouveauMontantRestant = $dette->getMontantRestant() - $montant;
            $nouveauStatut = $nouveauMontantRestant <= 0 ? 'SOLDEE' : 'NON SOLDEE';

            $this->detteRepository->mettreAJourSolde($detteId, max(0, $nouveauMontantRestant), $nouveauStatut);

            $this->pdo->commit();

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return [
            'dette_id'        => $detteId,
            'montant_restant' => max(0, $nouveauMontantRestant),
            'statut'          => $nouveauStatut,
        ];
    }
}

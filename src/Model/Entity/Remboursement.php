<?php

class Remboursement
{
    private int $id;
    private int $detteId;
    private float $montant;
    private string $modePaiement;
    private string $dateRemboursement;

    public function __construct(
        int $id,
        int $detteId,
        float $montant,
        string $modePaiement,
        string $dateRemboursement
    ) {
        $this->id = $id;
        $this->detteId = $detteId;
        $this->montant = $montant;
        $this->modePaiement = $modePaiement;
        $this->dateRemboursement = $dateRemboursement;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDetteId(): int
    {
        return $this->detteId;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getModePaiement(): string
    {
        return $this->modePaiement;
    }

    public function getDateRemboursement(): string
    {
        return $this->dateRemboursement;
    }

    public function setMontant(float $montant): void
    {
        $this->montant = $montant;
    }

    public function setModePaiement(string $modePaiement): void
    {
        $this->modePaiement = $modePaiement;
    }
}
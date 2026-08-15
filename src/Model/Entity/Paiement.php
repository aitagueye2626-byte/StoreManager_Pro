<?php

class Paiement
{
    private int $id;
    private int $venteId;
    private float $montant;
    private string $modePaiement;
    private string $datePaiement;

    public function __construct(
        int $id,
        int $venteId,
        float $montant,
        string $modePaiement,
        string $datePaiement
    ) {
        $this->id = $id;
        $this->venteId = $venteId;
        $this->montant = $montant;
        $this->modePaiement = $modePaiement;
        $this->datePaiement = $datePaiement;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVenteId(): int
    {
        return $this->venteId;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getModePaiement(): string
    {
        return $this->modePaiement;
    }

    public function getDatePaiement(): string
    {
        return $this->datePaiement;
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
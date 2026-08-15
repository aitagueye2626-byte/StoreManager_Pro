<?php

class Dette
{
    private int $id;
    private int $venteId;
    private int $clientId;
    private float $montantInitial;
    private float $montantRestant;
    private string $statut;
    private string $dateCreation;

    public function __construct(
        int $id,
        int $venteId,
        int $clientId,
        float $montantInitial,
        float $montantRestant,
        string $statut,
        string $dateCreation
    ) {
        $this->id = $id;
        $this->venteId = $venteId;
        $this->clientId = $clientId;
        $this->montantInitial = $montantInitial;
        $this->montantRestant = $montantRestant;
        $this->statut = $statut;
        $this->dateCreation = $dateCreation;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVenteId(): int
    {
        return $this->venteId;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getMontantInitial(): float
    {
        return $this->montantInitial;
    }

    public function getMontantRestant(): float
    {
        return $this->montantRestant;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    public function setMontantRestant(float $montantRestant): void
    {
        $this->montantRestant = $montantRestant;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function estSoldee(): bool
    {
        return $this->montantRestant <= 0;
    }
}
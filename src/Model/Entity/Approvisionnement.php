<?php

class Approvisionnement
{
    private int $id;
    private int $fournisseurId;
    private int $utilisateurId;
    private string $dateApprovisionnement;
    private float $total;
    private string $statut;

    public function __construct(
        int $id,
        int $fournisseurId,
        int $utilisateurId,
        string $dateApprovisionnement,
        float $total,
        string $statut
    ) {
        $this->id = $id;
        $this->fournisseurId = $fournisseurId;
        $this->utilisateurId = $utilisateurId;
        $this->dateApprovisionnement = $dateApprovisionnement;
        $this->total = $total;
        $this->statut = $statut;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFournisseurId(): int
    {
        return $this->fournisseurId;
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateurId;
    }

    public function getDateApprovisionnement(): string
    {
        return $this->dateApprovisionnement;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }
}
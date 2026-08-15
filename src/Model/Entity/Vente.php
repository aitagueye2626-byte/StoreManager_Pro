<?php

class Vente
{
    private int $id;
    private ?int $clientId;
    private int $utilisateurId;
    private string $dateVente;
    private float $total;
    private float $montantPaye;
    private string $statut;

    public function __construct(
        int $id,
        ?int $clientId,
        int $utilisateurId,
        string $dateVente,
        float $total,
        float $montantPaye,
        string $statut
    ) {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->utilisateurId = $utilisateurId;
        $this->dateVente = $dateVente;
        $this->total = $total;
        $this->montantPaye = $montantPaye;
        $this->statut = $statut;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateurId;
    }

    public function getDateVente(): string
    {
        return $this->dateVente;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getMontantPaye(): float
    {
        return $this->montantPaye;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setMontantPaye(float $montantPaye): void
    {
        $this->montantPaye = $montantPaye;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function getMontantRestant(): float
    {
        return $this->total - $this->montantPaye;
    }
}
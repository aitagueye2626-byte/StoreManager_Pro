<?php

class Inventaire
{
    private int $id;
    private int $utilisateurId;
    private string $dateInventaire;
    private string $statut;

    public function __construct(
        int $id,
        int $utilisateurId,
        string $dateInventaire,
        string $statut
    ) {
        $this->id = $id;
        $this->utilisateurId = $utilisateurId;
        $this->dateInventaire = $dateInventaire;
        $this->statut = $statut;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateurId;
    }

    public function getDateInventaire(): string
    {
        return $this->dateInventaire;
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
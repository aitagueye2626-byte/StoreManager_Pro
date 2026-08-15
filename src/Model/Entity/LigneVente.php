<?php

class LigneVente
{
    private int $id;
    private int $venteId;
    private int $produitId;
    private int $quantite;
    private float $prixUnitaire;
    private float $sousTotal;

    public function __construct(
        int $id,
        int $venteId,
        int $produitId,
        int $quantite,
        float $prixUnitaire,
        float $sousTotal
    ) {
        $this->id = $id;
        $this->venteId = $venteId;
        $this->produitId = $produitId;
        $this->quantite = $quantite;
        $this->prixUnitaire = $prixUnitaire;
        $this->sousTotal = $sousTotal;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVenteId(): int
    {
        return $this->venteId;
    }

    public function getProduitId(): int
    {
        return $this->produitId;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function getPrixUnitaire(): float
    {
        return $this->prixUnitaire;
    }

    public function getSousTotal(): float
    {
        return $this->sousTotal;
    }

    public function calculerSousTotal(): float
    {
        return $this->quantite * $this->prixUnitaire;
    }
}
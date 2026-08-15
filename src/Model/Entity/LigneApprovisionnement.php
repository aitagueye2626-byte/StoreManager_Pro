<?php

class LigneApprovisionnement
{
    private int $id;
    private int $approvisionnementId;
    private int $produitId;
    private int $quantite;
    private float $prixAchat;
    private float $sousTotal;

    public function __construct(
        int $id,
        int $approvisionnementId,
        int $produitId,
        int $quantite,
        float $prixAchat,
        float $sousTotal
    ) {
        $this->id = $id;
        $this->approvisionnementId = $approvisionnementId;
        $this->produitId = $produitId;
        $this->quantite = $quantite;
        $this->prixAchat = $prixAchat;
        $this->sousTotal = $sousTotal;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getApprovisionnementId(): int
    {
        return $this->approvisionnementId;
    }

    public function getProduitId(): int
    {
        return $this->produitId;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function getPrixAchat(): float
    {
        return $this->prixAchat;
    }

    public function getSousTotal(): float
    {
        return $this->sousTotal;
    }

    public function calculerSousTotal(): float
    {
        return $this->quantite * $this->prixAchat;
    }
}
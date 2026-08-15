<?php

class Stock
{
    private int $id;
    private int $produitId;
    private int $quantite;

    public function __construct(
        int $id,
        int $produitId,
        int $quantite
    ) {
        $this->id = $id;
        $this->produitId = $produitId;
        $this->quantite = $quantite;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduitId(): int
    {
        return $this->produitId;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): void
    {
        $this->quantite = $quantite;
    }

    public function ajouter(int $quantite): void
    {
        $this->quantite += $quantite;
    }

    public function retirer(int $quantite): void
    {
        $this->quantite -= $quantite;
    }
}
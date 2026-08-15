<?php

class LigneInventaire
{
    private int $id;
    private int $inventaireId;
    private int $produitId;
    private int $quantiteTheorique;
    private int $quantiteReelle;
    private int $ecart;

    public function __construct(
        int $id,
        int $inventaireId,
        int $produitId,
        int $quantiteTheorique,
        int $quantiteReelle,
        int $ecart
    ) {
        $this->id = $id;
        $this->inventaireId = $inventaireId;
        $this->produitId = $produitId;
        $this->quantiteTheorique = $quantiteTheorique;
        $this->quantiteReelle = $quantiteReelle;
        $this->ecart = $ecart;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getInventaireId(): int
    {
        return $this->inventaireId;
    }

    public function getProduitId(): int
    {
        return $this->produitId;
    }

    public function getQuantiteTheorique(): int
    {
        return $this->quantiteTheorique;
    }

    public function getQuantiteReelle(): int
    {
        return $this->quantiteReelle;
    }

    public function getEcart(): int
    {
        return $this->ecart;
    }

    public function calculerEcart(): int
    {
        return $this->quantiteReelle - $this->quantiteTheorique;
    }
}
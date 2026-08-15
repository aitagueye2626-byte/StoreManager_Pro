<?php

class Produit
{
    private int $id;
    private string $nom;
    private ?string $description;
    private ?string $categorie;
    private float $prix;
    private int $seuilAlerte;

    public function __construct(
        int $id,
        string $nom,
        ?string $description,
        ?string $categorie,
        float $prix,
        int $seuilAlerte
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->categorie = $categorie;
        $this->prix = $prix;
        $this->seuilAlerte = $seuilAlerte;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): void
    {
        $this->prix = $prix;
    }
}
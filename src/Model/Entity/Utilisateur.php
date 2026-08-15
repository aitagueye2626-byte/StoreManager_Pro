<?php

class Utilisateur
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $motDePasse;
    private string $role;
    private bool $actif;

    public function __construct(
        int $id,
        string $nom,
        string $prenom,
        string $email,
        string $motDePasse,
        string $role,
        bool $actif
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
        $this->actif = $actif;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function setActif(bool $actif): void
    {
        $this->actif = $actif;
    }
}
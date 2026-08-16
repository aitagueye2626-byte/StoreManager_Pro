<?php

class Client
{
    private int $id;
    private string $nom;
    private ?string $prenom;
    private string $telephone;
    private ?string $email;
    private ?string $adresse;
    private float $limiteCredit;

    public function __construct(
        int $id,
        string $nom,
        ?string $prenom,
        string $telephone,
        ?string $email,
        ?string $adresse,
        float $limiteCredit = 150000
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->adresse = $adresse;
        $this->limiteCredit = $limiteCredit;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function getLimiteCredit(): float
    {
        return $this->limiteCredit;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }
}
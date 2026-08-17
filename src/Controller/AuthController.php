<?php

namespace App\Controllers;

use App\Services\AuthManager;

class AuthController
{
    private AuthManager $authManager;

    public function __construct()
    {
        $this->authManager = new AuthManager();
    }

    public function afficherConnexion(): void
    {
        if ($this->authManager->estConnecte()) {

            $role =
                $this->authManager->getRoleConnecte();

            $vue =
                $this->authManager->getVueParDefaut($role);

            header('Location: /' . $vue);
            exit;
        }

        $messageErreur =
            $_SESSION['connexion_erreur'] ?? null;

        unset($_SESSION['connexion_erreur']);

        require dirname(__DIR__, 2)
            . '/views/auth/login.php';
    }

    public function traiterConnexion(): void
    {
        $email = trim(
            $_POST['email'] ?? ''
        );

        $motDePasse =
            $_POST['mot_de_passe'] ?? '';

        if ($email === '' || $motDePasse === '') {

            $_SESSION['connexion_erreur'] =
                'Veuillez remplir tous les champs.';

            header('Location: /connexion');
            exit;
        }

        if (!filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )) {

            $_SESSION['connexion_erreur'] =
                'Adresse email invalide.';

            header('Location: /connexion');
            exit;
        }

        $succes =
            $this->authManager->connecter(
                $email,
                $motDePasse
            );

        if (!$succes) {

            $_SESSION['connexion_erreur'] =
                'Email ou mot de passe incorrect.';

            header('Location: /connexion');
            exit;
        }

        $role =
            $this->authManager->getRoleConnecte();

        $vue =
            $this->authManager->getVueParDefaut($role);

        header('Location: /' . $vue);
        exit;
    }

    
    public function deconnecter(): void
    {
        $this->authManager->deconnecter();

        header('Location: /connexion');
        exit;
    }
}
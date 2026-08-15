<?php

namespace App\Core;

use PDO;
use PDOException;


class Database
{
    private static ?Database $instance = null;

    private PDO $connexion;

    private string $driverActif;

    private function __construct()
    {
        try {
            $host   = 'localhost';
            $port   = '5432';
            $dbname = 'approvisionnement';
            $user   = 'postgres';
            $pass   = '1234'; 

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

            $this->connexion = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $this->driverActif = 'pgsql';

        } catch (PDOException $e) {

            error_log('Connexion PostgreSQL impossible, bascule sur SQLite : ' . $e->getMessage());

        
            $racineProjet = dirname(__DIR__, 2);
            $cheminSqlite = $racineProjet . '/database/storemanager.db';

            $this->connexion = new PDO('sqlite:' . $cheminSqlite, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $this->connexion->exec('PRAGMA foreign_keys = ON;');

            $this->driverActif = 'sqlite';
        }
    }

   
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

   
    public function getConnexion(): PDO
    {
        return $this->connexion;
    }

    public function getDriverActif(): string
    {
        return $this->driverActif;
    }

    
    private function __clone(): void
    {
    }
}
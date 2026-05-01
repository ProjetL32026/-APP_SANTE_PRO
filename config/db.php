<?php
// config/db.php

class Database
{
    private static $instance = null;

    public static function getConnection()
    {
        if (self::$instance === null) {
            try {
                // J'ai mis le port 3308 pour que ça marche sur ton Wamp
                self::$instance = new PDO('mysql:host=localhost;port=3308;dbname=sante_pro_db;charset=utf8', 'root', '', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (Exception $e) {
                die('Erreur Connexion : ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}

// Pour que TON code (qui utilise $pdo) continue de fonctionner sans tout réécrire :
$pdo = Database::getConnection();
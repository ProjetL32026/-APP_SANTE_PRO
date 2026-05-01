<?php
<<<<<<< HEAD
// ============================================
// config/connexion.php
// Connexion à la base de données MySQL
// Inclus dans TOUS les Models
// ============================================

$host = 'localhost';
$port = '3306';
$dbname = 'sante_pro_db';
$user = 'root';
$pass = '';          // vide sur WAMP par défaut
$charset = 'utf8mb4';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );
    // Affiche les erreurs SQL clairement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Retourne les résultats en tableau associatif (nom des colonnes)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
=======
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
>>>>>>> f124a6fabef9c44f489313cba4a6c6f7834b23b2

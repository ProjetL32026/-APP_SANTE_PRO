<?php
// ============================================
// config/connexion.php
// Connexion à la base de données MySQL
// Inclus dans TOUS les Models
// ============================================
 
$host   = 'localhost';
 $port = '3308';
$dbname = 'sante_pro_db';
$user   = 'root';
$pass   = '';          // vide sur WAMP par défaut
$charset = 'utf8mb4';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
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
<?php
$host = 'localhost';
$port = '3306'; // Ou 3308 selon ta config Wamp
$dbname = 'sante_pro_db';
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
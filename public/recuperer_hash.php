<?php
/* // COMmente tout le code avec
require_once '../config/db.php'; // Vérifie bien le chemin vers ta config

$database = new Database();
$db = $database->getConnection();

$pseudo = "admin";
$pass_clair = "admin123"; // C'est ce que tu taperas dans le formulaire
$role = "admin";

// Génération du mot de passe haché (C'est ce qui va dans la DB)
$pass_crypte = password_hash($pass_clair, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO utilisateur (username, mot_de_passe, role) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$pseudo, $pass_crypte, $role]);
    
    echo "<h3>Succès !</h3>";
    echo "L'utilisateur <b>$pseudo</b> a été créé avec le mot de passe <b>$pass_clair</b>.<br>";
    echo "Tu peux maintenant supprimer ce fichier et tester le Login.";
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}*/ // <--- IL FAUT ABSOLUMENT CETTE FERMETURE ICI

echo "Le script de création d'utilisateur est désactivé pour des raisons de sécurité.";
?>
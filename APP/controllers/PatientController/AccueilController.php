<?php
//Ce fichier fait le pont : il récupère les données et charge la page.
// controllers/AccueilController.php

require_once ROOT . '/APP/models/Pmodel/PatientModel.php';

// --- PARTIE STATISTIQUES (Point 3) ---
// On récupère la connexion PDO globale
global $pdo;

// --- INSTANCIATION DE LA CLASSE ---
$patientModel = new PatientModel($pdo);

// On récupère les stats
$nbMedecins = $pdo->query("SELECT COUNT(*) FROM medecin")->fetchColumn();
$nbSpecialites = $pdo->query("SELECT COUNT(*) FROM specialite")->fetchColumn();
$rdvJourMax = 30;

// 1. On appelle la méthode de la classe pour récupérer les spécialités
// Cette fonction (qu'on a créée dans PatientModel) récupère le nom, l'id et le nombre de médecins
$specialites = $patientModel->getAllSpecialites();

// 2. On vérifie si l'utilisateur a cliqué sur une spécialité (via l'URL ?spec=ID)
$medecins = [];
$specSelectionnee = $_GET['spec'] ?? null;
$recherche = $_GET['search'] ?? '';

if ($specSelectionnee) {
    $medecins = $patientModel->getMedecinsBySpec($specSelectionnee);
} elseif ($recherche) {
    $medecins = $patientModel->rechercherMedecins($recherche);
}

// 2. On charge la vue Accueil.php
// Comme $specialites est définie ici, elle sera accessible directement dans le fichier Accueil.php

require_once ROOT . '/APP/views/patient/Accueil.php';
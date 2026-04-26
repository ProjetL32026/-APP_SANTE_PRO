<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../models/admin_models/Statistiques.php';


try {
    $database = new Database();
    $anneeSelectionnee = $_GET['annee'] ?? date('Y');
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("La connexion à la base de données a échoué.");
    }

    $statsModel = new Statistiques($db);

    // --- 1. Performance par Spécialité ---
$dataSpec = $statsModel->getRdvParSpecialite($anneeSelectionnee) ?: [];
$labelsSpec = array_column($dataSpec, 'label');   // Modifié : PHP pur
$valeursSpec = array_column($dataSpec, 'valeur'); // Modifié : PHP pur

// --- 2. Statut des Consultations ---
$dataConsul = $statsModel->getStatutConsultations($anneeSelectionnee) ?: [];
$labelsConsul = array_column($dataConsul, 'label');
$valeursConsul = array_column($dataConsul, 'valeur');

// --- 3. Affluence Hebdomadaire ---
$dataAffluence = $statsModel->getAffluenceHebdomadaire($anneeSelectionnee) ?: [];
$labelsAffluence = array_column($dataAffluence, 'label');
$valeursAffluence = array_column($dataAffluence, 'valeur');

// --- 4. Disponibilité des Équipes ---
$dataDispo = $statsModel->getDisponibiliteEquipes() ?: [];
$labelsDispo = array_column($dataDispo, 'label');
$valeursDispo = array_column($dataDispo, 'valeur');

    // Une fois les données prêtes, on charge la vue
   // include __DIR__ . '/../views/admin/statistique.php';
    require_once __DIR__ . '/../../views/admin/statistique.php';

} catch (Exception $e) {
    // En cas d'erreur, on affiche un message propre au lieu d'une page blanche
    die("Erreur lors du chargement des statistiques : " . $e->getMessage());
}
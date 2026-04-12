<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/Statistiques.php';

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
    $labelsSpec = json_encode(array_column($dataSpec, 'label'));
    $valeursSpec = json_encode(array_column($dataSpec, 'valeur'));

    // --- 2. Statut des Consultations ---
    $dataConsul = $statsModel->getStatutConsultations($anneeSelectionnee) ?: [];
    $labelsConsul = json_encode(array_column($dataConsul, 'label'));
    $valeursConsul = json_encode(array_column($dataConsul, 'valeur'));

    // --- 3. Affluence Hebdomadaire ---
    $dataAffluence = $statsModel->getAffluenceHebdomadaire($anneeSelectionnee) ?: [];
    $labelsAffluence = json_encode(array_column($dataAffluence, 'label'));
    $valeursAffluence = json_encode(array_column($dataAffluence, 'valeur'));

    // --- 4. Disponibilité des Équipes ---
    $dataDispo = $statsModel->getDisponibiliteEquipes() ?: [];
    $labelsDispo = json_encode(array_column($dataDispo, 'label'));
    $valeursDispo = json_encode(array_column($dataDispo, 'valeur'));

    // Une fois les données prêtes, on charge la vue
    include __DIR__ . '/../views/admin/statistique.php';

} catch (Exception $e) {
    // En cas d'erreur, on affiche un message propre au lieu d'une page blanche
    die("Erreur lors du chargement des statistiques : " . $e->getMessage());
}
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/Statistiques.php';

$database = new Database();
$db = $database->getConnection();
$statsModel = new Statistiques($db);

// Récupération des données
$dataSpec = $statsModel->getRdvParSpecialite();
$dataConsul = $statsModel->getStatutConsultations();

// On prépare les labels et les chiffres pour Chart.js
$labelsSpec = json_encode(array_column($dataSpec, 'label'));
$valeursSpec = json_encode(array_column($dataSpec, 'valeur'));

$labelsConsul = json_encode(array_column($dataConsul, 'label'));
$valeursConsul = json_encode(array_column($dataConsul, 'valeur'));
// Récupération de l'affluence
$dataAffluence = $statsModel->getAffluenceHebdomadaire();

$labelsAffluence = json_encode(array_column($dataAffluence, 'label'));
$valeursAffluence = json_encode(array_column($dataAffluence, 'valeur'));
// Récupération de la disponibilité
$dataDispo = $statsModel->getDisponibiliteEquipes();

$labelsDispo = json_encode(array_column($dataDispo, 'label'));
$valeursDispo = json_encode(array_column($dataDispo, 'valeur'));
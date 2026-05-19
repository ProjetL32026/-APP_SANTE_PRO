<?php
require_once ROOT . '/APP/models/Pmodel/PatientModel.php';

global $pdo;
$patientModel = new PatientModel($pdo);

if (!isset($_SESSION['patient_id'])) {
    header('Location: index.php?page=inscription');
    exit();
}

// 1. Récupération dynamique du profil
$patient = $patientModel->recupererPatientParId($_SESSION['patient_id']);

if (!$patient) {
    $patient = ['nom' => 'Client', 'prenom' => '', 'email' => '', 'telephone' => ''];
}

// 2. Récupération dynamique des rendez-vous
$rendezVous = $patientModel->getRendezVousByPatient($_SESSION['patient_id']);

// 3. Transformation en JSON pour Historique.js
$rdvJson = json_encode($rendezVous);

// 4. AJAX : voir ordonnance ← AJOUTÉ ICI
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    isset($_GET['action']) && 
    $_GET['action'] === 'voir_ordonnance'
) {
    $id  = intval($_GET['id']);
    $rdv = $patientModel->getRdvById($id);
    include ROOT . '/APP/views/medecin/ordonnance.php';
    exit;  // ← TRÈS IMPORTANT
}

require_once ROOT . '/APP/views/patient/historique.php';
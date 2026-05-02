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

// Sécurité : si le patient n'existe pas en BDD
if (!$patient) {
    $patient = ['nom' => 'Client', 'prenom' => '', 'email' => '', 'telephone' => ''];
}

// 2. Récupération dynamique des rendez-vous
$rendezVous = $patientModel->getRendezVousByPatient($_SESSION['patient_id']);



// 3. Transformation en JSON pour Historique.js
$rdvJson = json_encode($rendezVous);

require_once ROOT . '/APP/views/patient/historique.php';
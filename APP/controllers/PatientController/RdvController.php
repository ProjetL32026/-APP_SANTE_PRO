<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once ROOT . '/APP/models/Pmodel/PatientModel.php';

// 1. INITIALISATION DES VARIABLES
$erreur = null;
$idMedecin = $_GET['idMedecin'] ?? ($_POST['id_medecin'] ?? null);
$medecin = null;
$patientId = $_SESSION['patient_id'] ?? null;

// 2. LE GARDIEN : Vérification d'authentification
if (!isset($_SESSION['patient_id']) || empty($_SESSION['patient_id'])) {
    header("Location: index.php?page=inscription&idMedecin=" . $idMedecin);
    exit();
}

// 3. VÉRIFICATION DU PATIENT
$patient = recupererPatientParId($patientId);
if ($patient && $patient['is_verified'] == 0) {
    header("Location: index.php?page=verification");
    exit();
}

// 4. RÉCUPÉRATION INFOS MÉDECIN
$rdvDejaExistants = [];
if ($idMedecin) {
    $medecin = getMedecinById($idMedecin);
    if ($medecin) {
        $rdvDejaExistants = countRdvByMedecin($idMedecin);
    } else {
        $erreur = "Erreur : Aucun médecin trouvé pour l'ID " . htmlspecialchars($idMedecin);
    }
}

// 5. TRAITEMENT DU FORMULAIRE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_medecin'])) {

    // ✅ BUG CORRIGÉ : La clé 'date_rdv' correspond au name="date_rdv" dans le formulaire
    $data = [
        'id_parent'      => $_SESSION['patient_id'],
        'id_medecin'     => $_POST['id_medecin'],
        'nom_patient'    => $_POST['nom']            ?? null,
        'prenom_patient' => $_POST['prenom']         ?? null,
        'ddn_patient'    => $_POST['date_naissance'] ?? null,
        'date_rdv'       => $_POST['date_rdv']       ?? null,   
        'periode'        => $_POST['periode']        ?? null
    ];

    // ✅ BUG CORRIGÉ : On vérifie 'date_rdv' et non 'date'
    if ($data['nom_patient'] && $data['date_rdv']) {
        if (saveSimpleRendezVous($data)) {
            header('Location: index.php?page=historique&status=success');
            exit();
        } else {
            $erreur = "Erreur technique lors de l'enregistrement.";
        }
    } else {
        // DEBUG TEMPORAIRE — supprime ces lignes après que ça marche
        $erreur  = "Données reçues : ";
        $erreur .= "nom=" . ($data['nom_patient'] ?? 'VIDE') . " | ";
        $erreur .= "date_rdv=" . ($data['date_rdv'] ?? 'VIDE') . " | ";
        $erreur .= "periode=" . ($data['periode'] ?? 'VIDE');
    }
}

$estConnecte = isset($_SESSION['patient_id']) && !empty($_SESSION['patient_id']);

if (!$estConnecte) {
    header("Location: index.php?page=inscription&idMedecin=" . $idMedecin);
    exit();
} else {
    require_once ROOT . '/APP/views/patient/rdv.php';
}
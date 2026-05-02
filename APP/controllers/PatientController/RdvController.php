<?php
// controllers/RdvController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once ROOT . '/APP/models/Pmodel/PatientModel.php';

// --- INSTANCIATION DE LA CLASSE ---
global $pdo;
$patientModel = new PatientModel($pdo);

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

// 3. VÉRIFICATION DU PATIENT (Utilisation de la méthode de classe)
$patient = $patientModel->recupererPatientParId($patientId);
if ($patient && $patient['is_verified'] == 0) {
    header("Location: index.php?page=verification");
    exit();
}

// 4. RÉCUPÉRATION INFOS MÉDECIN
$rdvDejaExistants = [];
if ($idMedecin) {
    // Utilisation de la méthode de classe
    $medecin = $patientModel->getMedecinById($idMedecin);
    if ($medecin) {
        // Utilisation de la méthode de classe
        $rdvDejaExistants = $patientModel->countRdvByMedecin($idMedecin);
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
    // verifier  que  un seul rendez-vous par personne par 24h
    if ($data['nom_patient'] && $data['date_rdv']) {
        
        // 1. ON AJOUTE LA VÉRIFICATION ICI (Utilisation de la méthode de classe)
        // On vérifie si ce patient (nom + prénom) existe déjà dans la table pour cette date
        if ($patientModel->aDejaUnRdvLeMemeJour($data['nom_patient'], $data['prenom_patient'], $data['date_rdv'])) {
            
            // Si oui, on bloque et on prépare le message d'erreur
            $erreur = "Désolé, " . htmlspecialchars($data['nom_patient']) . " a déjà un rendez-vous prévu pour cette date. Un seul rendez-vous par personne est autorisé par 24h.";
            
        } else {
            // 2. SI PAS DE DOUBLON, ON CONTINUE LA LOGIQUE HABITUELLE (Utilisation de la méthode de classe)
            if ($patientModel->saveSimpleRendezVous($data)) {
                header('Location: index.php?page=historique&status=success');
                exit();
            } else {
                $erreur = "Erreur technique lors de l'enregistrement.";
            }
        }

    } else {
        // Bloc de debug
        $erreur  = "Données reçues : ";
        $erreur .= "nom=" . ($data['nom_patient'] ?? 'VIDE') . " | ";
        $erreur .= "date_rdv=" . ($data['date_rdv'] ?? 'VIDE');
    }
}

$estConnecte = isset($_SESSION['patient_id']) && !empty($_SESSION['patient_id']);

if (!$estConnecte) {
    header("Location: index.php?page=inscription&idMedecin=" . $idMedecin);
    exit();
} else {
    require_once ROOT . '/APP/views/patient/rdv.php';
}
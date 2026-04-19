<?php
//role du controller RdvController :
//Afficher le formulaire : Récupérer les infos du médecin sélectionné pour les afficher sur la page.
//Traiter l'envoi : Récupérer les données du formulaire ($_POST), appeler la fonction du modèle et rediriger le patient vers son historique une fois que c'est enregistré.
// controllers/RdvController.php

// 1. Gestion de la session sécurisée
/**Fonction de Sécurité (Le Gardien) : Avant même d'afficher la page, il vérifie si une session existe. Si un utilisateur essaie de tricher en tapant l'URL directement sans se connecter, le contrôleur le bloque. */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



require_once ROOT . '/APP/models/PatientModel.php';

// --- INITIALISATION CRUCIALE ---
// On définit ces variables à vide pour éviter les "Undefined variable" dans la vue
$erreur = null;
$idMedecin = $_GET['idMedecin'] ?? ($_POST['id_medecin'] ?? null);
$medecin = null;



// Récupération de l'ID

// 3. LE GARDIEN : Vérification d'authentification
/**Fonction de Dispatching : Il décide quoi faire des données. Si c'est une requête GET, il affiche le formulaire. Si c'est une requête POST (clic sur confirmer), il envoie les données au modèle. */
// Si l'ID patient n'est pas en session, on redirige vers l'inscription
if (!isset($_SESSION['patient_id']) || empty($_SESSION['patient_id'])) {
    header("Location: index.php?page=inscription&idMedecin=" . $idMedecin);
    exit(); // On arrête l'exécution ici
}

// 4. RÉCUPÉRATION INFOS MÉDECIN (pour l'affichage)
if ($idMedecin) {
    die("Débogage : L'ID du médecin est " . $idMedecin . " mais rien n'a été trouvé en base.");
    $medecin = getMedecinById($idMedecin); 
    if (!$medecin) { echo "Erreur : Aucun médecin trouvé pour l'ID " . $idMedecin; }
}



/**
 * 5. TRAITEMENT DU FORMULAIRE (POST)
 * Fonction de Dispatching : Il décide quoi faire des données. Si c'est une requête GET, il affiche le formulaire. Si c'est une requête POST (clic sur confirmer), il envoie les données au modèle.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
    // On récupère les infos du bénéficiaire (le fils par exemple)
    $data = [
        'id_parent'      => $_SESSION['patient_id'], // Le compte connecté
        'id_medecin'     => $_POST['id_medecin'],
        'nom_patient'    => $_POST['nom'],
        'prenom_patient' => $_POST['prenom'],
        'ddn_patient'    => $_POST['date_naissance'],
        'date_rdv'       => $_POST['date'],
        'periode'        => $_POST['periode']
    ];

    // Appel de la nouvelle fonction simplifiée dans le modèle
    $resultat = saveSimpleRendezVous($data);

    if ($resultat) {
        header('Location: index.php?page=historique&status=success');
        exit();
    } else {
        $erreur = "Erreur lors de l'enregistrement. Veuillez vérifier les informations.";
    }
}
// On définit proprement si l'utilisateur est connecté
$estConnecte = isset($_SESSION['patient_id']) && !empty($_SESSION['patient_id']);

// 3. Chargement de la vue
// Le fichier rdv.php pourra utiliser $medecin et $erreur
if (!$estConnecte) {
    // Ce cas ne devrait théoriquement pas arriver à cause du "Gardien" au point 3,
    // mais c'est une sécurité supplémentaire.
    header("Location: index.php?page=inscription&idMedecin=" . $idMedecin);
    exit();
} else {
    // C'est ici que $medecin, $idMedecin et $erreur sont transmis à la vue
    require_once ROOT . '/APP/views/patient/rdv.php';
}
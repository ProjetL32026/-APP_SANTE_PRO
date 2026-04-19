<?php
// controllers/HistoriqueController.php

// On vérifie si l'utilisateur est connecté, sinon on le redirige
if (!isset($_SESSION['patient_id'])) {
    header('Location: index.php?page=inscription');
    exit();
}

// 1. Simulation des données du patient (on utilise la session)
$patient = [
    'nom'       => $_SESSION['patient_nom'] ?? 'Nom',
    'prenom'    => $_SESSION['patient_prenom'] ?? 'Prénom',
    'email'     => $_SESSION['patient_email'] ?? 'email@exemple.com',
    'telephone' => $_SESSION['patient_tel'] ?? 'Non renseigné'
];

// 2. Simulation d'un tableau vide pour les rendez-vous (évite l'erreur count() )
$rendezVous = []; 

// 3. Préparation du JSON pour le JavaScript de la page
$rdvJson = json_encode($rendezVous);

// 4. Appel de la vue
require_once ROOT . '/APP/views/patient/historique.php';
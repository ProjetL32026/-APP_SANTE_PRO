<!--1InscriptionController.php uniquement pour afficher 
le formulaire vide quand on clique sur "S'inscrire".

2-AuthController.php pour toutes les actions (login, inscription, logout).
-->
<?php
require_once ROOT . '/APP/models/Pmodel/PatientModel.php';
// controllers/InscriptionController.php

// 1. On peut récupérer l'idMedecin depuis l'URL si besoin
$idMedecin = $_GET['idMedecin'] ?? null;

// 2. On charge la vue (le formulaire)
// Vérifie bien que le chemin vers ton fichier HTML/PHP est le bon !
require_once ROOT . '/APP/views/patient/inscription.php';
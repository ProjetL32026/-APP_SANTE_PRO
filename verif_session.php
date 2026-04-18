<?php
session_start();

// 1. On récupère le médecin choisi dans la liste
$id_medecin = $_GET['id_medecin'] ?? null;

// Sécurité : si on arrive ici sans avoir choisi de médecin, on retourne à l'accueil
if (!$id_medecin) {
    header("Location: index.php");
    exit();
}

// 2. On mémorise le choix du médecin en SESSION 
// (pour s'en souvenir après la connexion ou l'inscription)
$_SESSION['id_medecin_choisi'] = $id_medecin;

// 3. VERIFICATION : L'utilisateur est-il connecté ?
if (isset($_SESSION['id_utilisateur'])) {

    // CAS A : Déjà connecté -> On va direct à la confirmation du rendez-vous
    header("Location: confirmation_rdv.php");

} else {

    // CAS B : Pas connecté -> On l'envoie vers la page de connexion/inscription
    // Une fois connecté, ton script de login devra vérifier si $_SESSION['id_medecin_choisi'] existe
    header("Location: login.php");
}
exit();
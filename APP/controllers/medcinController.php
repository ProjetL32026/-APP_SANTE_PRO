<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/medcin.php';

$database = new Database();
$db = $database->getConnection();
$medecin = new Medecin($db);

// --- 1. TRAITEMENT DE LA SUPPRESSION (GET) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];
    try {
        $queryMed = "DELETE FROM medecin WHERE id_medecin = :id";
        $stmtMed = $db->prepare($queryMed);
        $stmtMed->execute([':id' => $id_a_supprimer]);

        $queryUser = "DELETE FROM utilisateur WHERE id = :id";
        $stmtUser = $db->prepare($queryUser);
        $stmtUser->execute([':id' => $id_a_supprimer]);

        header("Location: /SANTE_PRO/public/index.php?page=medcin&delete=success");
        exit();
    } catch (PDOException $e) {
        die("Erreur de suppression : " . $e->getMessage());
    }
}

// --- 2. TRAITEMENT POST (AJOUT OU MODIFICATION) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add'; // On récupère l'action (add par défaut)
    
    // Récupération des champs communs
    $id = $_POST['id_medecin'] ?? null;
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['telephone'] ?? '';
    $id_spec = $_POST['id_specialite'] ?? null;
    $h_debut = $_POST['heure_debut'] ?? null;
    $h_fin = $_POST['heure_fin'] ?? null;
    $jours = isset($_POST['jours_travail']) ? implode(', ', $_POST['jours_travail']) : '';

    if ($action === 'update' && $id) {
        // --- LOGIQUE DE MODIFICATION ---
        try {
            $db->beginTransaction();
            // Update utilisateur
            $sqlU = "UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?";
            $db->prepare($sqlU)->execute([$nom, $prenom, $email, $tel, $id]);

            // Update medecin
            $sqlM = "UPDATE medecin SET id_specialite = ?, heure_debut = ?, heure_fin = ?, jour_travail = ? WHERE id_medecin = ?";
            $db->prepare($sqlM)->execute([$id_spec, $h_debut, $h_fin, $jours, $id]);

            $db->commit();
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=updated");
        } catch (Exception $e) {
            $db->rollBack();
            die("Erreur modification : " . $e->getMessage());
        }
    } else {
        // --- LOGIQUE D'AJOUT ---
        $username = $_POST['username'] ?? '';
        $mdp = $_POST['password'] ?? '';
        $jours_array = $_POST['jours_travail'] ?? [];

        if ($medecin->ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_spec, $h_debut, $h_fin, $jours_array)) {
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=success");
        } else {
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=error");
        }
    }
    exit();
}
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/medcin.php';

$database = new Database();
$db = $database->getConnection();
// On garde le nom $medecinModel pour être cohérent
$medecinModel = new Medecin($db); 

// --- 1. TRAITEMENT DE LA SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];
    try {
        $db->beginTransaction(); 
        $stmtMed = $db->prepare("DELETE FROM medecin WHERE id_medecin = ?");
        $stmtMed->execute([$id_a_supprimer]);
        $stmtUser = $db->prepare("DELETE FROM utilisateur WHERE id = ?");
        $stmtUser->execute([$id_a_supprimer]);
        $db->commit();
        header("Location: index.php?page=medcin&status=deleted");
        exit();
    } catch (PDOException $e) {
        if ($db->inTransaction()) { $db->rollBack(); }
        header("Location: index.php?page=medcin&status=error");
        exit();
    }
}

// --- 2. TRAITEMENT POST (AJOUT OU MODIFICATION) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $id = $_POST['id_medecin'] ?? null;
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['telephone'] ?? '';
    $type = $_POST['type'] ?? 'Dr.';
    $id_spec = $_POST['id_specialite'] ?? null;
    $h_debut = $_POST['heure_debut'] ?? null;
    $h_fin = $_POST['heure_fin'] ?? null;
    $jours = isset($_POST['jours_travail']) ? implode(', ', $_POST['jours_travail']) : '';

    if ($action === 'update' && $id) {
        try {
            $db->beginTransaction();
            $sqlU = "UPDATE utilisateur SET username = ?, nom = ?, prenom = ?, email = ?, telephone = ?";
            $paramsU = [$username, $nom, $prenom, $email, $tel];
            $new_mdp = $_POST['password'] ?? '';
            if (!empty($new_mdp)) {
                $sqlU .= ", mot_de_passe = ?";
                $paramsU[] = password_hash($new_mdp, PASSWORD_DEFAULT);
            }
            $sqlU .= " WHERE id = ?";
            $paramsU[] = $id;
            $db->prepare($sqlU)->execute($paramsU);
            
            $sqlM = "UPDATE medecin SET type = ?, id_specialite = ?, heure_debut = ?, heure_fin = ?, jour_travail = ? WHERE id_medecin = ?";
            $db->prepare($sqlM)->execute([$type, $id_spec, $h_debut, $h_fin, $jours, $id]);
            
            $db->commit();
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=updated");
            exit();
        } catch (Exception $e) {
            $db->rollBack();
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=error");
            exit();
        }
    } else {
        $mdp = $_POST['password'] ?? '123456'; 
        $jours_array = $_POST['jours_travail'] ?? [];
        if ($medecinModel->ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_spec, $h_debut, $h_fin, $jours_array, $type)) {
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=success");
        } else {
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=error");
        }
        exit();
    }
}

// --- 3. PRÉPARATION DE L'AFFICHAGE (En dehors du IF POST pour que ça marche toujours) ---

// On récupère les données
$medecins = $medecinModel->getAllMedecins();
$all_specialities = $medecinModel->getAllSpecialities();

// Gestion du médecin à modifier (si action=edit dans l'URL)
$medecin_a_modifier = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $medecin_a_modifier = $medecinModel->getMedecinById($_GET['id']);
}

// On appelle enfin la vue
require_once __DIR__ . '/../views/admin/medcin.php';
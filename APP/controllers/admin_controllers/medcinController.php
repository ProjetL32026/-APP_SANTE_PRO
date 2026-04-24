<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../Models/admin_models/medcin.php';


$database = new Database();
$db = $database->getConnection();
// On garde le nom $medecinModel pour être cohérent
$medecinModel = new Medecin($db); 

// --- 1. TRAITEMENT DE LA SUPPRESSION ---
// --- 1. TRAITEMENT DE LA SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];
    
    // VERIFICATION : Est-ce que le médecin a des rendez-vous ?
    $checkRDV = $db->prepare("SELECT COUNT(*) FROM rendez_vous WHERE id_medecin = ?");
    $checkRDV->execute([$id_a_supprimer]);
    if ($checkRDV->fetchColumn() > 0) {
        header("Location: index.php?page=medcin&status=error&type=has_appointments");
        exit();
    }

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
        header("Location: index.php?page=medcin&status=error&type=db_error");
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
    $oldData = "&old_nom=" . urlencode($nom) . 
           "&old_prenom=" . urlencode($prenom) . 
           "&old_user=" . urlencode($username) . 
           "&old_email=" . urlencode($email) . 
           "&old_tel=" . urlencode($tel).
           "&old_type=" . urlencode($type).
           "&old_h_debut=" . urlencode($h_debut).
           "&old_h_fin=" . urlencode($h_fin).
           "&old_jours=" . urlencode( $jours).
           "&old_id_spec=" . urlencode($id_spec);

// VERIFICATION DES DOUBLONS
// 1. Test de l'Email
$checkEmail = $db->prepare("SELECT id FROM utilisateur WHERE email = ? " . ($id ? "AND id != ?" : ""));
$paramsEmail = $id ? [$email, $id] : [$email];
$checkEmail->execute($paramsEmail);

if ($checkEmail->fetch()) {
    $redir = ($action === 'update') ? "action=edit&id=$id" : "action=add";
    $params = "&old_nom=$nom&old_prenom=$prenom&old_user=$username&old_email=$email&old_tel=$tel";
    header("Location: index.php?page=medcin&$redir&status=error&type=email_exists" . $oldData);
    exit();
}

// 2. Test du Username
$checkUser = $db->prepare("SELECT id FROM utilisateur WHERE username = ? " . ($id ? "AND id != ?" : ""));
$paramsUser = $id ? [$username, $id] : [$username];
$checkUser->execute($paramsUser);

if ($checkUser->fetch()) {
    $redir = ($action === 'update') ? "action=edit&id=$id" : "action=add";
    $params = "&old_nom=$nom&old_prenom=$prenom&old_user=$username&old_email=$email&old_tel=$tel";
    header("Location: index.php?page=medcin&$redir&status=error&type=user_exists" . $oldData);
    exit();
}
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
            header("Location: /santepro/public/index.php?page=medcin&status=updated");
            exit();
        } catch (Exception $e) {
            $db->rollBack();
            header("Location: /santepro/public/index.php?page=medcin&status=error");
            exit();
        }
    } else {
        $mdp = $_POST['password'] ?? '123456'; 
        $jours_array = $_POST['jours_travail'] ?? [];
        if ($medecinModel->ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_spec, $h_debut, $h_fin, $jours_array, $type)) {
            header("Location: /santepro/public/index.php?page=medcin&status=success");
        } else {
            header("Location: /santepro/public/index.php?page=medcin&status=error");
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

require_once __DIR__ . '/../../views/admin/medcin.php';
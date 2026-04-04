<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/medcin.php';

$database = new Database();
$db = $database->getConnection();
$medecin = new Medecin($db);

// --- 1. TRAITEMENT DE LA SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];
    try {
        $db->beginTransaction(); 

        // 1. On supprime le profil spécifique (médecin)
        $stmtMed = $db->prepare("DELETE FROM medecin WHERE id_medecin = ?");
        $stmtMed->execute([$id_a_supprimer]);

        // 2. On supprime le compte général (utilisateur)
        $stmtUser = $db->prepare("DELETE FROM utilisateur WHERE id = ?");
        $stmtUser->execute([$id_a_supprimer]);

        $db->commit();
        
        // Redirection avec succès
        header("Location: /SANTE_PRO/public/index.php?page=medcin&delete=success");
        exit();

    } catch (PDOException $e) {
        // En cas d'erreur, on annule tout pour ne pas corrompre la base
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        // Log de l'erreur pour le développeur
        error_log("Erreur suppression médecin ID $id_a_supprimer : " . $e->getMessage());
        header("Location: /SANTE_PRO/public/index.php?page=medcin&status=error");
        exit();
    }
}
// --- 2. TRAITEMENT POST (AJOUT OU MODIFICATION) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add'; // On récupère l'action (add par défaut)
    
    // Récupération des champs communs
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
            
            // 1. Préparer la base de la requête SQL pour UTILISATEUR
            $sqlU = "UPDATE utilisateur SET username = ?, nom = ?, prenom = ?, email = ?, telephone = ?";
            $paramsU = [$username, $nom, $prenom, $email, $tel];

            // 2. VÉRIFICATION DU MOT DE PASSE
            $new_mdp = $_POST['password'] ?? '';
            if (!empty($new_mdp)) {
                // Si un nouveau MDP est saisi, on l'ajoute à la requête
                $sqlU .= ", mot_de_passe = ?";
                $paramsU[] = password_hash($new_mdp, PASSWORD_DEFAULT);
            }

            // 3. On termine la requête avec le WHERE
            $sqlU .= " WHERE id = ?";
            $paramsU[] = $id;

            $stmtU = $db->prepare($sqlU);
            $stmtU->execute($paramsU);
    
            // 4. Mise à jour de la table MEDECIN (Horaires, spécialité...)
            $sqlM = "UPDATE medecin SET type = ?, id_specialite = ?, heure_debut = ?, heure_fin = ?, jour_travail = ? WHERE id_medecin = ?";
$stmtM = $db->prepare($sqlM);
$stmtM->execute([$type, $id_spec, $h_debut, $h_fin, $jours, $id]);
    
            $db->commit();
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=updated");
        } catch (Exception $e) {
            $db->rollBack();
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=error");
        }
    
    } else {
        // --- LOGIQUE D'AJOUT (Nouveau Médecin) ---
        
        // 1. On récupère le mot de passe et les jours
        $mdp = $_POST['password'] ?? '123456'; 
        // On récupère le tableau des jours cochés (ex: ['Dim', 'Lun'])
        $jours_array = $_POST['jours_travail'] ?? [];

        // 2. On appelle la méthode du Modèle
        // Elle va créer l'Utilisateur, récupérer son ID, puis créer le Médecin
        if ($medecin->ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_spec, $h_debut, $h_fin, $jours_array, $type)) {
            // Succès : Redirection vers la liste avec un message vert
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=success");
        } else {
            // Échec : Redirection avec un message d'erreur (souvent dû à un username déjà pris)
            header("Location: /SANTE_PRO/public/index.php?page=medcin&status=error");
        }
    }
    exit();
}
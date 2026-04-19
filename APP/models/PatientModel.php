<?php
// models/PatientModel.php
require_once ROOT . '/config/connexion.php';

/**
 * 1. Récupérer toutes les spécialités avec le nombre de médecins
 */
function getAllSpecialites() {
    global $pdo;
    $sql = "SELECT s.id_specialite as id, s.nom_specialite as nom, COUNT(m.id_medecin) as nbMedecins
            FROM specialite s
            LEFT JOIN medecin m ON s.id_specialite = m.id_specialite
            GROUP BY s.id_specialite";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 2. Récupérer les médecins d'une spécialité précise
 * Récupère les médecins selon l'id de la spécialité
 * Elle récupère les informations de l'utilisateur (nom, prénom) croisées avec les informations de la table médecin.
 */
 
function getMedecinsBySpec($id_spec) {
    global $pdo;
    $sql = "SELECT u.id, u.nom, u.prenom, m.type, m.status, m.heure_debut, m.heure_fin
            FROM medecin m
            JOIN utilisateur u ON m.id_medecin = u.id
            WHERE m.id_specialite = :id_spec";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_spec' => $id_spec]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 3. Enregistrer un nouveau rendez-vous
 * Cette fonction crée le patient ET son rendez-vous (Transaction)
 */
function saveRendezVousAndPatient($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();
         // AJOUTE CETTE LIGNE POUR HACHER LE MOT DE PASSE
        $mdp_hash = password_hash($data['mdp'], PASSWORD_DEFAULT);
        // 1. Insertion dans 'utilisateur' (Infos communes)
        $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, email, role, telephone, mot_de_passe) 
                    VALUES (:nom, :prenom, :username, :email, 'patient', :tel, :mdp)";
        
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([
            'nom'      => $data['nom'],
            'prenom'   => $data['prenom'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'tel'      => $data['telephone'],
            'mdp'      => $mdp_hash
        ]);
        
        $id_new_user = $pdo->lastInsertId();

        // 2. Insertion dans 'patient' avec la DATE DE NAISSANCE
        // C'est ici qu'on met l'info spécifique au patient
        $sqlPat = "INSERT INTO patient (id_patient, date_naissance) VALUES (:id, :ddn)";
        $stmtPat = $pdo->prepare($sqlPat);
        $stmtPat->execute([
            'id'  => $id_new_user,
            'ddn' => $data['date_naissance']
        ]);

        // 3. Insertion du rendez-vous
        $sqlRdv = "INSERT INTO rendez_vous (id_patient, id_medecin, date, periode, statut) 
                    VALUES (:id_p, :id_m, :date, :periode, 'attente')";
        
        $stmtRdv = $pdo->prepare($sqlRdv);
        $stmtRdv->execute([
            'id_p'    => $id_new_user,
            'id_m'    => $data['id_medecin'],
            'date'    => $data['date'],
            'periode' => $data['periode']
        ]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }

}

/**
 * Enregistre un rendez-vous pour un bénéficiaire (ex: fils) 
 * lié au compte du parent connecté.
 */
function saveSimpleRendezVous($data) {
    global $pdo;
    // ICI : Pas de création de compte ! 
    // On prend juste l'ID du père (id_patient) et on crée le RDV.
    $sql = "INSERT INTO rendez_vous (id_patient, id_medecin, date, periode, statut) 
            VALUES (:id_p, :id_m, :date, :periode, 'attente')";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'id_p'    => $data['id_parent'], // ID récupéré de $_SESSION['patient_id']
        'id_m'    => $data['id_medecin'],
        'date'    => $data['date_rdv'],
        'periode' => $data['periode']
    ]);
}

/**
 * 4. Récupérer les infos d'un seul médecin par son ID
 * (Utilisé pour afficher le nom du médecin sur le formulaire de RDV)
 */
function getMedecinById($id) {
    global $pdo;
    // Vérifie bien que les colonnes s'appellent exactement 'nom' et 'type' dans ta base
    $sql = "SELECT nom, type, specialite FROM medecin WHERE id_medecin = ?"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Doit retourner un tableau
}



/**
 * 5. Vérifie les identifiants pour la connexion rapide
 */
/*function verifierConnexionPatient($email, $mdp) {
    global $pdo;
    // On cherche dans utilisateur l'email et le mdp (en tant que patient)
    $sql = "SELECT id, nom, prenom FROM utilisateur 
            WHERE email = :email AND mdp = :mdp AND role = 'patient' 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'email' => $email,
        'mdp'   => $mdp // Note: si ton mdp est haché, il faudra utiliser password_verify
    ]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
} */


   function verifierConnexionPatient($email, $mdp) {
    global $pdo;
    
    // On fait une jointure pour chercher l'email dans 'utilisateur' 
    // et vérifier que c'est bien un 'patient'
    $sql = "SELECT u.*, p.* FROM utilisateur u 
            JOIN patient p ON u.id = p.id_patient 
            WHERE u.email = ?"; 
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Vérification du mot de passe (si tu utilises password_hash)
    if ($user && password_verify($mdp, $user['mot_de_passe'])) {
        return $user;
    }
    
    // Si tu n'utilises pas encore password_hash pour tes tests :
    if ($user && $mdp === $user['mot_de_passe']) {
        return $user;
    }

    return false;
}

/**
 * 6. Inscrire un nouveau patient
 */

/*
 function inscrirePatient($data) {
    global $pdo;
    try {
        // Insertion dans la table utilisateur
        $sql = "INSERT INTO utilisateur (nom, prenom, email, username, mdp, telephone, date_naissance, role) 
                VALUES (:nom, :prenom, :email, :username, :mdp, :telephone, :date_naissance, 'patient')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom'            => $data['nom'],
            'prenom'         => $data['prenom'],
            'email'          => $data['email'],
            'username'       => $data['username'],
            'mdp'            => $data['mdp'],
            'telephone'      => $data['telephone'],
            'date_naissance' => $data['date_naissance']
        ]);
        
        return $pdo->lastInsertId(); // Retourne l'ID créé pour ouvrir la session
    } catch (Exception $e) {
        return false;
    }
}
*/

function inscrirePatient($data) {
    global $pdo;
    /**1Insérer dans la table utilisateur.
       2Récupérer l'ID créé.
       3Insérer dans la table patient avec cet ID. */

    try {
        $pdo->beginTransaction(); // On démarre une transaction pour être sûr que les deux tables sont remplies

        // 1. Insertion dans la table UTILISATEUR
        $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, email, telephone, mot_de_passe, role) 
                    VALUES (?, ?, ?, ?, ?, ?, 'patient')";
        
        $mdp_hash = password_hash($data['mdp'], PASSWORD_DEFAULT);
        
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([
            $data['nom'], 
            $data['prenom'], 
            $data['username'], 
            $data['email'], 
            $data['telephone'], 
            $mdp_hash
        ]);

        // On récupère l'ID qui vient d'être créé
        $userId = $pdo->lastInsertId();

        // 2. Insertion dans la table PATIENT (Héritage)
        $code = rand(100000, 999999);
        $sqlPatient = "INSERT INTO patient (id_patient, date_naissance, is_verified, verification_code) 
                       VALUES (?, ?, 0, ?)";
        
        $stmtPatient = $pdo->prepare($sqlPatient);
        $stmtPatient->execute([
            $userId, 
            $data['date_naissance'], 
            $code
        ]);

        $pdo->commit(); // On valide tout
        return $userId;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Pour le débug, garde le message, mais en production on retourne false
        return false;
    }
}


 function verifierLeCodeAction($id, $codeSaisi) {
    global $pdo;
    $sql = "SELECT verification_code FROM patient WHERE id_patient = ?"; // id_patient !
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $patient = $stmt->fetch();

    if ($patient && $codeSaisi == $patient['verification_code']) {
        $update = "UPDATE patient SET is_verified = 1 WHERE id_patient = ?";
        $pdo->prepare($update)->execute([$id]);
        return true;
    }
    return false;
}


function recupererPatientParId($id) {
    global $pdo;
    // On s'assure de prendre id_patient et verification_code
    $sql = "SELECT * FROM patient WHERE id_patient = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne un tableau
}
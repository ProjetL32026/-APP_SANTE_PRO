<?php
class Medecin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_spec, $h_debut, $h_fin, $jours,$type) {
        try {
            $this->db->beginTransaction();

            // 1. Insertion dans la table 'utilisateur'
            // MODIFICATION : On ajoute 'mot_de_passe' ICI
            $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, email, mot_de_passe, role, telephone) 
                        VALUES (:nom, :prenom, :username, :email, :mdp, 'medecin', :tel)";
            
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nom'      => $nom,
                ':prenom'   => $prenom,
                ':username' => $username,
                ':email'    => $email,
                ':mdp'      => password_hash($mdp, PASSWORD_DEFAULT), // On hache ici !
                ':tel'      => $tel
            ]);

            $userId = $this->db->lastInsertId();

         // 2. Insertion dans la table 'medecin'
$jours_str = is_array($jours) ? implode(', ', $jours) : $jours;

// On retire 'status' de la liste des colonnes pour laisser la BDD gérer sa valeur initiale
$sqlMed = "INSERT INTO medecin (id_medecin, type, heure_debut, heure_fin, jour_travail, id_specialite) 
           VALUES (:id, :type, :h_debut, :h_fin, :jours, :id_spec)";

$stmtMed = $this->db->prepare($sqlMed);
$stmtMed->execute([
    ':id'        => $userId,
    ':type'      => $type,
    ':h_debut'   => $h_debut,
    ':h_fin'     => $h_fin,
    ':jours'     => $jours_str,
    ':id_spec'   => $id_spec
]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erreur SQL Médecin : " . $e->getMessage());
            return false;
        }
    }
}
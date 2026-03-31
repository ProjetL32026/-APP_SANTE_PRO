<?php
class Medecin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_spec, $jours, $h_debut, $h_fin) {
        try {
            $this->db->beginTransaction();

            // 1. Insertion dans utilisateur
            $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, email, telephone, mot_de_passe, role) 
                        VALUES (:nom, :prenom, :username, :email, :tel, :mdp, 'medecin')";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':username' => $username,
                ':email' => $email,
                ':tel' => $tel,
                ':mdp' => password_hash($mdp, PASSWORD_DEFAULT)
            ]);

            $id_user = $this->db->lastInsertId();

            // 2. Transformer le tableau des jours en chaîne (ex: "Dim, Lun")
            $jours_str = !empty($jours) ? implode(', ', $jours) : '';

            // 3. Insertion dans medecin
            $sqlMed = "INSERT INTO medecin (id, id_specialite, jours_travail, heure_debut, heure_fin) 
                       VALUES (:id, :id_spec, :jours, :h_debut, :h_fin)";
            $stmtMed = $this->db->prepare($sqlMed);
            $stmtMed->execute([
                ':id' => $id_user,
                ':id_spec' => $id_spec,
                ':jours' => $jours_str,
                ':h_debut' => $h_debut,
                ':h_fin' => $h_fin
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
<?php
class Medecin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_spec, $h_debut, $h_fin, $jours) {
        try {
            $this->db->beginTransaction();

            // 1. Insertion dans la table 'utilisateur'
            // La table utilisateur contient bien ces colonnes [cite: 1]
            $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, email, role, telephone) 
                        VALUES (:nom, :prenom, :username, :email, 'medecin', :tel)";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nom'      => $nom,
                ':prenom'   => $prenom,
                ':username' => $username,
                ':email'    => $email,
                ':tel'      => $tel
            ]);

            $userId = $this->db->lastInsertId();

            // 2. Insertion dans la table 'medecin'
            // Transformation du tableau des jours pour la colonne 'jour_travail' 
            $jours_str = implode(', ', $jours);

            // CORRECTION : Ajout de la colonne 'type' présente dans ton SQL 
            $sqlMed = "INSERT INTO medecin (id_medecin, type, status, heure_debut, heure_fin, jour_travail, mot_de_passe, id_specialite) 
                       VALUES (:id, :type, :status, :h_debut, :h_fin, :jours, :mdp, :id_spec)";
            
            $stmtMed = $this->db->prepare($sqlMed);
            $stmtMed->execute([
                ':id'        => $userId,      // id_medecin est la PK et FK 
                ':type'      => 'Général',    // Valeur pour la colonne 'type' 
                ':status'    => 'ACTIF',      // Valeur pour la colonne 'status' 
                ':h_debut'   => $h_debut,
                ':h_fin'     => $h_fin,
                ':jours'     => $jours_str,
                ':mdp'       => password_hash($mdp, PASSWORD_BCRYPT),
                ':id_spec'   => $id_spec      // FK vers la table specialite [cite: 3, 4]
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            // Utilisation de error_log pour ne pas casser l'affichage utilisateur
            error_log("Erreur SQL Médecin : " . $e->getMessage());
            return false;
        }
    }
}
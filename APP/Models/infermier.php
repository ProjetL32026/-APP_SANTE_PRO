<?php
class Infirmier {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_specialite) {
        try {
            $this->db->beginTransaction();
    
            // 1. Insertion dans 'utilisateur' (On ajoute le mot_de_passe ICI)
            $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, email, mot_de_passe, role, telephone) 
                        VALUES (:nom, :prenom, :username, :email, :mdp, 'infirmier', :tel)";
            
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nom'      => $nom,
                ':prenom'   => $prenom,
                ':username' => $username,
                ':email'    => $email,
                ':mdp'      => password_hash($mdp, PASSWORD_DEFAULT), // Hachage ici
                ':tel'      => $tel
            ]);
    
            $userId = $this->db->lastInsertId();
    
            // 2. Insertion dans 'infirmier' (On retire le mot_de_passe d'ici)
            $sqlInf = "INSERT INTO infirmier (id, id_specialite) 
                       VALUES (:id, :id_spec)";
            
            $stmtInf = $this->db->prepare($sqlInf);
            $resultat = $stmtInf->execute([
                ':id'      => $userId,
                ':id_spec' => $id_specialite
            ]);
    
            $this->db->commit();
            return $resultat;
    
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erreur SQL Ajouter Infirmier : " . $e->getMessage());
            return false;
        }
    }

    public function modifier($id, $nom, $prenom, $username, $email, $tel, $id_spec, $new_mdp = '') {
        try {
            $this->db->beginTransaction();
    
            // 1. Construction dynamique de la requête pour 'utilisateur'
            $sqlUser = "UPDATE utilisateur 
                        SET nom = :nom, prenom = :prenom, username = :username, email = :email, telephone = :tel";
            
            $params = [
                ':nom'      => $nom,
                ':prenom'   => $prenom,
                ':username' => $username,
                ':email'    => $email,
                ':tel'      => $tel,
                ':id'       => $id
            ];

            // Si un nouveau mot de passe est fourni, on l'ajoute à l'UPDATE
            if (!empty($new_mdp)) {
                $sqlUser .= ", mot_de_passe = :mdp";
                $params[':mdp'] = password_hash($new_mdp, PASSWORD_DEFAULT);
            }

            $sqlUser .= " WHERE id = :id";
            
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute($params);
    
            // 2. Mise à jour de la table 'infirmier'
            $sqlInf = "UPDATE infirmier SET id_specialite = :id_spec WHERE id = :id";
            $stmtInf = $this->db->prepare($sqlInf);
            $stmtInf->execute([
                ':id_spec' => $id_spec,
                ':id'      => $id
            ]);
    
            $this->db->commit();
            return true;
    
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erreur Modification Infirmier : " . $e->getMessage());
            return false;
        }
    }

    public function supprimer($id) {
        try {
            $this->db->beginTransaction();
            
            // Supprimer d'abord l'enfant (infirmier) pour respecter les contraintes FK
            $stmt1 = $this->db->prepare("DELETE FROM infirmier WHERE id = ?");
            $stmt1->execute([$id]);

            // Supprimer ensuite le parent (utilisateur)
            $stmt2 = $this->db->prepare("DELETE FROM utilisateur WHERE id = ?");
            $result = $stmt2->execute([$id]);

            $this->db->commit();
            return $result;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erreur Suppression Infirmier : " . $e->getMessage());
            return false;
        }
    }
}
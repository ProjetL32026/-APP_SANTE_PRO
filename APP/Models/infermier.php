<?php
class Infirmier {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_specialite) {
        try {
            // Début de la transaction pour sécuriser l'ajout
            $this->db->beginTransaction();
    
            // 1. On insère d'abord dans la table 'utilisateur' (Parent) avec le nouveau champ username
            $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, email, role, telephone) 
                        VALUES (:nom, :prenom, :username, :email, 'infirmier', :tel)";
            
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':username' => $username, // Ajout du username ici
                ':email' => $email,
                ':tel' => $tel
            ]);
    
            // 2. On récupère l'ID qui vient d'être créé
            $userId = $this->db->lastInsertId();
    
            // 3. On insère dans la table 'infirmier' (Enfant) avec le mot de passe
            $sqlInf = "INSERT INTO infirmier (id, mot_de_passe, id_specialite) 
                       VALUES (:id, :mdp, :id_spec)";
            
            $stmtInf = $this->db->prepare($sqlInf);
            
            $resultat = $stmtInf->execute([
                ':id' => $userId,
                ':mdp' => password_hash($mdp, PASSWORD_BCRYPT), // Sécurité
                ':id_spec' => $id_specialite
            ]);
    
            // Validation finale de l'opération
            $this->db->commit();
            return $resultat;
    
        } catch (PDOException $e) {
            // En cas d'erreur, on annule tout pour ne pas avoir de données orphelines
            $this->db->rollBack();
            // Affiche l'erreur si ça échoue (très utile pour débugger sur Wamp)
            die("Erreur SQL : " . $e->getMessage());
        }
    }
    public function supprimer($id) {
        try {
            // On utilise $this->db car c'est le nom que tu as choisi en haut de la classe
            $query = "DELETE FROM utilisateur WHERE id = :id";
            $stmt = $this->db->prepare($query); 
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Utile pour voir l'erreur sur Wamp en cas de problème
            die("Erreur SQL Suppression : " . $e->getMessage());
        }
    }
    public function modifier($id, $nom, $prenom, $username, $email, $tel, $id_spec) {
        try {
            // Début d'une transaction pour s'assurer que les deux tables sont mises à jour
            $this->db->beginTransaction();
    
            // 1. Mise à jour de la table parent (utilisateur) incluant le username
            $sqlUser = "UPDATE utilisateur 
                        SET nom = :nom, 
                            prenom = :prenom, 
                            username = :username, 
                            email = :email, 
                            telephone = :tel 
                        WHERE id = :id";
            
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':username' => $username, // Ajout du username ici
                ':email' => $email,
                ':tel' => $tel,
                ':id' => $id
            ]);
    
            // 2. Mise à jour de la table enfant (infirmier)
            $sqlInf = "UPDATE infirmier SET id_specialite = :id_spec WHERE id = :id";
            $stmtInf = $this->db->prepare($sqlInf);
            $stmtInf->execute([
                ':id_spec' => $id_spec,
                ':id' => $id
            ]);
    
            // Validation des changements
            $this->db->commit();
            return true;
    
        } catch (PDOException $e) {
            // En cas d'erreur, on annule tout (rollback)
            $this->db->rollBack();
            die("Erreur Modification : " . $e->getMessage());
        }
    }
}
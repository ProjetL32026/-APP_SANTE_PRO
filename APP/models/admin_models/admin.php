<?php
class Admin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Modifier le profil complet (Infos centre + Identifiants)
    public function modifierProfil($id, $nom, $email, $username, $nouveau_mdp = null) {
        try {
            if (!empty($nouveau_mdp)) {
                // Mise à jour AVEC changement de mot de passe
                $sql = "UPDATE utilisateur 
                        SET nom = :nom, email = :email, username = :username, mot_de_passe = :mdp 
                        WHERE id = :id AND role = 'admin'";
                $params = [
                    ':nom' => $nom,
                    ':email' => $email,
                    ':username' => $username,
                    ':mdp' => password_hash($nouveau_mdp, PASSWORD_DEFAULT),
                    ':id' => $id
                ];
            } else {
                // Mise à jour SANS changer le mot de passe
                $sql = "UPDATE utilisateur 
                        SET nom = :nom, email = :email, username = :username 
                        WHERE id = :id AND role = 'admin'";
                $params = [
                    ':nom' => $nom,
                    ':email' => $email,
                    ':username' => $username,
                    ':id' => $id
                ];
            }
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
    
        } catch (PDOException $e) {
            error_log("Erreur SQL : " . $e->getMessage());
            return false;
        }
    }
}

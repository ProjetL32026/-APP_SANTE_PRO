<?php
class Admin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Modifier le profil complet (Infos centre + Identifiants)
    public function modifierProfil($id, $nom, $email, $username, $nouveau_mdp = null) {
        try {
            $this->db->beginTransaction();

            // 1. Mise à jour de la table utilisateur (Nom du centre, Email, Username)
            $sqlUser = "UPDATE utilisateur SET nom = :nom, email = :email, username = :username WHERE id = :id";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':username' => $username,
                ':id' => $id
            ]);

            // 2. Mise à jour du mot de passe uniquement s'il est saisi
            if (!empty($nouveau_mdp)) {
                $sqlAdmin = "UPDATE admin SET mot_de_passe = :mdp WHERE id = :id";
                $stmtAdmin = $this->db->prepare($sqlAdmin);
                $stmtAdmin->execute([
                    ':mdp' => password_hash($nouveau_mdp, PASSWORD_BCRYPT),
                    ':id' => $id
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            die("Erreur SQL : " . $e->getMessage());
        }
    }
}
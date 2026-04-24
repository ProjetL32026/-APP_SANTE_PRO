<?php
class Utilisateur {
    private $db;

    // Le constructeur reçoit la connexion à la base de données
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère toutes les infos d'un utilisateur par son pseudo
     */
    public function findByUsername($username) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE username = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username]);
            
            // Retourne les données (id, username, mot_de_passe, role...) ou false
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans findByUsername : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour le token de connexion (Se souvenir de moi)
     */
    public function updateRememberToken($userId, $token) {
        try {
            $sql = "UPDATE utilisateur SET remember_token = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$token, $userId]);
        } catch (PDOException $e) {
            error_log("Erreur dans updateRememberToken : " . $e->getMessage());
            return false;
        }
    }
}
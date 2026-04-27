<?php
class Utilisateur {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Récupère un utilisateur (admin, infirmier ou autre) par son pseudo
     */
    public function findByUsername($username) {
        try {
            // On sélectionne tout pour avoir le 'role' et le 'mot_de_passe'
            $sql = "SELECT * FROM utilisateur WHERE username = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username]);
            
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

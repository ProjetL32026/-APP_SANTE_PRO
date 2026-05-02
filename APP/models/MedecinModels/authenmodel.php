<?php
class UserModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Récupère un utilisateur (admin, infirmier ou autre) par son pseudo
     */
    public function findByUsername(string $username): false|array {
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
    public function updateRememberToken(int $userId, string $token): bool {
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
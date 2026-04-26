<?php
class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findByUsername($username) {
        try {
            // Ajustez "utilisateur" si votre table s'appelle "utilisateurs"
            $sql = "SELECT * FROM utilisateur WHERE username = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans findByUsername : " . $e->getMessage());
            return false;
        }
    }
}
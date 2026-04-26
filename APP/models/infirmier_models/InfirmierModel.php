<?php
class InfirmierModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Authentifie un infirmier en vérifiant le hachage du mot de passe
     */
    public function authenticate($username, $password) {
        // On récupère l'utilisateur et sa spécialité d'infirmier
        $sql = "SELECT u.*, i.id_specialite 
                FROM utilisateur u 
                LEFT JOIN infirmier i ON u.id = i.id 
                WHERE u.username = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si l'utilisateur existe
        if ($user) {
            // Vérification stricte du mot de passe haché (bcrypt/argon2)
            if (password_verify($password, $user['mot_de_passe'])) {
                
                // Sécurité : on ne stocke jamais le mot de passe dans la session
                unset($user['mot_de_passe']);
                
                return $user; // Succès
            }
        }

        // Échec de l'authentification (utilisateur inconnu ou mauvais mot de passe)
        return null;
    }
}
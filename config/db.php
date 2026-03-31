<?php
class Database {
    private $host = 'localhost';
    private $port = '3306'; // Garde 3306 ou change en 3308 si Wamp est sur 3308
    private $dbname = 'sante_pro_db';
    private $user = 'root';
    private $pass = ''; 
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // On crée la connexion PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname . ";charset=utf8", 
                $this->user, 
                $this->pass
            );
            // On active la gestion des erreurs
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>
<?php
// APP/models/UserModel.php
class UserModel {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM utilisateur WHERE username = :user LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user' => $username]);
        return $stmt->fetch();
    }
}
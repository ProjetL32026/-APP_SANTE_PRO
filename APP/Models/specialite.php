<?php
class Specialite {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function exists($nom) {
        $query = "SELECT COUNT(*) FROM specialite WHERE nom_specialite = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$nom]);
        return $stmt->fetchColumn() > 0;
    }
    // Pour l'Ajout
    public function create($nom) {
        $query = "INSERT INTO specialite (nom_specialite) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nom]);
    }

    // Pour la Suppression
    public function delete($id) {
        $query = "DELETE FROM specialite WHERE id_specialite = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
    
    // Pour l'Affichage
    public function readAll() {
        $query = "SELECT s.*, COUNT(m.id_medecin) as nb_medecins 
                  FROM specialite s 
                  LEFT JOIN medecin m ON s.id_specialite = m.id_specialite 
                  GROUP BY s.id_specialite";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
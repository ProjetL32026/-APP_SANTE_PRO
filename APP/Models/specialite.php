<?php
class Specialite {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    // NOUVELLE MÉTHODE : Vérifie si la spécialité est utilisée
    public function isUsed($id) {
        // On vérifie dans la table medecin
        $queryMed = "SELECT COUNT(*) FROM medecin WHERE id_specialite = ?";
        $stmtMed = $this->conn->prepare($queryMed);
        $stmtMed->execute([$id]);
        $countMed = $stmtMed->fetchColumn();

        // On vérifie dans la table infirmier
        $queryInf = "SELECT COUNT(*) FROM infirmier WHERE id_specialite = ?";
        $stmtInf = $this->conn->prepare($queryInf);
        $stmtInf->execute([$id]);
        $countInf = $stmtInf->fetchColumn();

        return ($countMed > 0 || $countInf > 0);
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
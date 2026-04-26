<?php

class GestionMedecinModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Récupérer les informations d'un médecin spécifique
     */
    public function getMedecinById($id_medecin) {
        // Correction : On sélectionne m.id_medecin pour l'utiliser dans le dashboard
        $sql = "SELECT m.id_medecin, u.nom, u.prenom, s.nom_specialite, m.status
                FROM medecin m
                JOIN utilisateur u ON m.id_medecin = u.id
                JOIN specialite s ON m.id_specialite = s.id_specialite
                WHERE m.id_medecin = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_medecin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jour le statut de présence
     */
    public function updateStatus($id_medecin, $status) {
        try {
            $sql = "UPDATE medecin SET status = :status WHERE id_medecin = :id";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                'status' => $status,
                'id' => $id_medecin
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Récupère tous les médecins
     */
    public function getAllMedecins() {
        $sql = "SELECT m.id_medecin, u.nom, u.prenom, s.nom_specialite, m.status
                FROM medecin m
                JOIN utilisateur u ON m.id_medecin = u.id
                JOIN specialite s ON m.id_specialite = s.id_specialite";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMedecinsBySpecialite($id_specialite) {
        $sql = "SELECT m.id_medecin, u.nom, u.prenom, s.nom_specialite
                FROM medecin m
                JOIN utilisateur u ON m.id_medecin = u.id
                JOIN specialite s ON m.id_specialite = s.id_specialite
                WHERE m.id_specialite = :id_spec";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_spec' => $id_specialite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
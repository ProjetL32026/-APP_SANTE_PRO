<?php
// On remonte de Smodel (..), puis de MODEL (..), puis on va dans config
require_once __DIR__ . '/../../config/Database.php';

class RdvModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Récupère les patients d'un médecin à une date précise
    public function getPatientsByAbsence($id_medecin, $date)
    {
        // Attention : vérifie bien si ta colonne s'appelle 'date' ou 'date_rdv'
        $sql = "SELECT r.id_rdv, u.email, u.nom, r.date
                FROM rendez_vous r
                JOIN utilisateur u ON r.id_patient = u.id_utilisateur
                WHERE r.id_medecin = :id_m 
                AND r.date= :date 
                AND r.statut != 'annule'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_m' => $id_medecin, 'date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Met à jour le statut du RDV
    public function updateStatut($id_rdv, $statut)
    {
        $sql = "UPDATE rendez_vous SET statut = :statut WHERE id_rdv = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['statut' => $statut, 'id' => $id_rdv]);
    }
}
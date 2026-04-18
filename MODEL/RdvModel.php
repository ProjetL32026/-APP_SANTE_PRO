<?php
require_once 'Database.php';

class RdvModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getPatientsByAbsence($id_medecin, $date)
    {
        $sql = "SELECT u.email, u.nom FROM utilisateur u
                JOIN rendez_vous r ON u.id_utilisateur = r.id_patient
                WHERE r.id_medecin = :id_m AND r.date = :d";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_m' => $id_medecin, 'd' => $date]);
        return $stmt->fetchAll();
    }
}
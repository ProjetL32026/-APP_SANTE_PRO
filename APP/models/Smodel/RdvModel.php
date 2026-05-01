<?php

class RdvModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * 1. Récupère les patients d'un médecin pour une date précise.
     * On ajoute r.mail_envoye pour savoir si on doit déclencher l'envoi.
     */
    public function getPatientsByAbsence($id_medecin, $date)
    {
        $sql = "SELECT r.id_rdv, u.email, u.nom, r.date, r.mail_envoye
                FROM rendez_vous r
                JOIN utilisateur u ON r.id_patient = u.id
                WHERE r.id_medecin = :id_m 
                AND r.date = :date 
                AND r.statut != 'annule'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_m' => $id_medecin, 'date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 2. Vérifie le statut du médecin dans la table 'medecin'
     */
    public function getMedecinStatus($id_medecin)
    {
        $sql = "SELECT status FROM medecin WHERE id_medecin = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['status'] ?? 'disponible';
    }

    /**
     * 3. Récupère le nom du médecin pour personnaliser l'email
     */
    public function getNomMedecin($id_medecin)
    {
        $sql = "SELECT nom FROM utilisateur WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['nom'] ?? "votre médecin";
    }

    /**
     * 4. Met à jour le statut du RDV (ex: passer à 'annule')
     */
    public function updateStatut($id_rdv, $statut)
    {
        $sql = "UPDATE rendez_vous SET statut = :statut WHERE id_rdv = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['statut' => $statut, 'id' => $id_rdv]);
    }

    /**
     * 5. Marque le mail comme envoyé pour éviter les répétitions
     */
    public function marquerMailEnvoye($id_rdv)
    {
        $sql = "UPDATE rendez_vous SET mail_envoye = 1 WHERE id_rdv = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id_rdv]);
    }
}
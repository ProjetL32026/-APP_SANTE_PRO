<?php
class Statistiques {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Remplace tes fonctions par celles-ci :
public function getRdvParSpecialite($annee) {
    $sql = "SELECT s.nom_specialite as label, COUNT(r.id_rdv) as valeur 
            FROM specialite s
            LEFT JOIN medecin m ON s.id_specialite = m.id_specialite
            LEFT JOIN rendez_vous r ON m.id_medecin = r.id_medecin AND YEAR(r.date) = :annee
            GROUP BY s.id_specialite";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['annee' => $annee]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getStatutConsultations($annee) {
    $sql = "SELECT statut as label, COUNT(*) as valeur 
            FROM rendez_vous 
            WHERE YEAR(date) = :annee
            GROUP BY statut";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['annee' => $annee]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAffluenceHebdomadaire($annee) {
    $sql = "SELECT DAYNAME(date) as label, COUNT(*) as valeur, WEEKDAY(date) as jour_num
            FROM rendez_vous 
            WHERE YEAR(date) = :annee
            GROUP BY label, jour_num
            ORDER BY jour_num";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['annee' => $annee]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function getDisponibiliteEquipes() {
        // IFNULL permet de remplacer le mot NULL par "Non défini" pour le graphique
        $sql = "SELECT IFNULL(status, 'Non défini') as label, COUNT(*) as valeur 
                FROM medecin 
                GROUP BY label";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
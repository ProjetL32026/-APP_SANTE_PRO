<?php
class Statistiques {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Performance par spécialité (Nombre de RDV)
    public function getRdvParSpecialite() {
        $sql = "SELECT s.nom_specialite as label, COUNT(r.id_rdv) as valeur 
                FROM specialite s
                LEFT JOIN medecin m ON s.id_specialite = m.id_specialite
                LEFT JOIN rendez_vous r ON m.id_medecin = r.id_medecin
                GROUP BY s.id_specialite";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Statut des Consultations (Terminée, En attente, Annulée)
    public function getStatutConsultations() {
        // On va chercher le statut dans la table 'rendez_vous' 
        // car c'est là que l'information se trouve dans ton SQL
        $sql = "SELECT statut as label, COUNT(*) as valeur 
                FROM rendez_vous 
                GROUP BY statut";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAffluenceHebdomadaire() {
        // Cette requête compte les RDV groupés par jour de la semaine
        // Note : On trie par le numéro du jour (WEEKDAY) pour avoir l'ordre Dimanche -> Samedi
        $sql = "SELECT DAYNAME(date) as label, COUNT(*) as valeur, WEEKDAY(date) as jour_num
                FROM rendez_vous 
                GROUP BY label, jour_num
                ORDER BY jour_num";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDisponibiliteEquipes() {
        // IFNULL permet de remplacer le mot NULL par "Non défini" pour le graphique
        $sql = "SELECT IFNULL(status, 'Non défini') as label, COUNT(*) as valeur 
                FROM medecin 
                GROUP BY label";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
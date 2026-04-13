<?php
class DashboardModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Récupérer le nombre total d'infirmiers
    public function getTotalInfirmiers() {
        $query = "SELECT COUNT(*) as total FROM utilisateur WHERE role = 'infirmier'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
    }

    // Récupérer les médecins actifs
    public function getTotalMedecinsActifs() {
        $query = "SELECT COUNT(*) as total 
                  FROM utilisateur u 
                  JOIN medecin m ON u.id = m.id_medecin 
                  WHERE u.role = 'medecin' AND m.status = 'présent'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
    }

    // Nombre de patients aujourd'hui
    public function getTotalPatientsJour() {
        $query = "SELECT COUNT(*) as total FROM rendez_vous WHERE DATE(date) = CURDATE()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
    }

    // Nombre d'absences
    public function getTotalAbsencesJour() {
        $query = "SELECT COUNT(*) as total FROM medecin WHERE status = 'ABSENT'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
    }

    // Top 5 des médecins
    public function getTopMedecins($limit = 5) {
        $query = "SELECT u.nom, u.prenom, m.type, s.nom_specialite, m.status, 
                         COUNT(r.id_rdv) as nb_consultations
                  FROM utilisateur u
                  JOIN medecin m ON u.id = m.id_medecin
                  JOIN specialite s ON m.id_specialite = s.id_specialite
                  LEFT JOIN rendez_vous r ON m.id_medecin = r.id_medecin
                  WHERE u.role = 'medecin'
                  GROUP BY m.id_medecin
                  ORDER BY nb_consultations DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
<?php
// models/PatientModel.php
require_once ROOT . '/config/db.php';

class PatientModel {
    private $pdo;

    /**
     * Le constructeur initialise la connexion à la base de données
     */
    public function __construct($db) {
        $this->pdo = $db;
    }

    /**
     * 1. Récupérer toutes les spécialités avec le nombre de médecins
     */
    public function getAllSpecialites() {
        $sql = "SELECT s.id_specialite as id, s.nom_specialite as nom, COUNT(m.id_medecin) as nbMedecins
                FROM specialite s
                LEFT JOIN medecin m ON s.id_specialite = m.id_specialite
                GROUP BY s.id_specialite";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 2. Récupérer les médecins d'une spécialité précise
     * Récupère les médecins selon l'id de la spécialité
     * Elle récupère les informations de l'utilisateur (nom, prénom) croisées avec les informations de la table médecin.
     */
    public function getMedecinsBySpec($id_spec) {
       $sql = "SELECT u.id, u.nom, u.prenom, u.telephone,
               m.type, m.status, m.heure_debut, m.heure_fin, m.jour_travail
        FROM medecin m
        JOIN utilisateur u ON m.id_medecin = u.id
        WHERE m.id_specialite = :id_spec";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_spec' => $id_spec]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Enregistre un rendez-vous pour un bénéficiaire (ex: fils) 
     * lié au compte du parent connecté.
     */
    public function saveSimpleRendezVous($data) {
        // Sécurité technique : si l'ID médecin est vide, on arrête tout
        if (empty($data['id_medecin'])) return false;

        $sql = "INSERT INTO rendez_vous 
                (id_patient, id_medecin, date, periode, statut, nom_patient, prenom_patient, ddn_patient) 
                VALUES 
                (:id_p, :id_m, :date, :periode, 'En attente', :nom_p, :prenom_p, :ddn_p)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id_p'     => $data['id_parent'],
            'id_m'     => $data['id_medecin'],
            'date'     => $data['date_rdv'],
            'periode'  => $data['periode'],
            'nom_p'    => $data['nom_patient'],
            'prenom_p' => $data['prenom_patient'],
            'ddn_p'    => $data['ddn_patient']
        ]);
    }

    /**
     * 4. Récupérer les infos d'un seul médecin par son ID
     * (Utilisé pour afficher le nom du médecin sur le formulaire de RDV)
     */
    public function getMedecinById($id) {
        try {
            // IMPORTANT : On ajoute m.jour_travail dans le SELECT
            $sql = "SELECT u.nom, u.prenom, m.type, m.jour_travail 
                    FROM medecin m
                    JOIN utilisateur u ON m.id_medecin = u.id 
                    WHERE m.id_medecin = ?"; 
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultat ?: null;

        } catch (PDOException $e) {
            error_log("Erreur SQL : " . $e->getMessage());
            return null;
        }
    }

    public function verifierConnexionPatient($email, $mdp) {
        // On fait une jointure pour chercher l'email dans 'utilisateur' 
        // et vérifier que c'est bien un 'patient'
        $sql = "SELECT u.*, p.* FROM utilisateur u 
                JOIN patient p ON u.id = p.id_patient 
                WHERE u.email = ?"; 
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Vérification du mot de passe
        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            return $user;
        }
        
        return false;
    }

    public function inscrirePatient($data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Insertion dans la table UTILISATEUR
            $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, mot_de_passe, email, role, telephone) 
                        VALUES (?, ?, ?, ?, ?, 'patient', ?)";
            
            $mdp_hash = password_hash($data['mdp'], PASSWORD_DEFAULT);
            
            $stmtUser = $this->pdo->prepare($sqlUser);
            $stmtUser->execute([
                $data['nom'], 
                $data['prenom'], 
                $data['username'], 
                $mdp_hash, 
                $data['email'], 
                $data['telephone']
            ]);

            $userId = $this->pdo->lastInsertId();

            // 2. Insertion dans la table PATIENT
            $sqlPatient = "INSERT INTO patient (id_patient, date_naissance, is_verified, verification_code) 
                           VALUES (?, ?, 0, NULL)";
            
            $stmtPatient = $this->pdo->prepare($sqlPatient);
            $stmtPatient->execute([
                $userId, 
                $data['date_naissance']
            ]);

            $this->pdo->commit();
            return $userId;

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return false;
        }
    }

    public function verifierLeCodeAction($id, $codeSaisi) {
        // On récupère le code envoyé par l'admin stocké en BD
        $sql = "SELECT verification_code FROM patient WHERE id_patient = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $patient = $stmt->fetch();

        // On vérifie si le code saisi correspond et n'est pas vide
        if ($patient && !empty($patient['verification_code']) && $codeSaisi == $patient['verification_code']) {
            
            // On valide le patient ET on remet le code à NULL pour la sécurité
            $update = "UPDATE patient SET is_verified = 1, verification_code = NULL WHERE id_patient = ?";
            $this->pdo->prepare($update)->execute([$id]);
            return true;
        }
        return false;
    }

    public function recupererPatientParId($id) {
    // On ajoute u.email et u.telephone dans le SELECT
    $sql = "SELECT u.nom, u.prenom, u.email, u.telephone, p.* 
            FROM utilisateur u 
            JOIN patient p ON u.id = p.id_patient 
            WHERE u.id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
    public function countRdvByMedecin($id_medecin) {
        $sql = "SELECT date, periode, COUNT(*) as total 
                FROM rendez_vous 
                WHERE id_medecin = :id 
                GROUP BY date, periode";
                
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id_medecin]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $rdvData = [];
            foreach ($results as $row) {
                $date = $row['date'];
                $periode = $row['periode'];
                if (!isset($rdvData[$date])) {
                    $rdvData[$date] = ['matin' => 0, 'aprem' => 0];
                }
                $rdvData[$date][$periode] = (int)$row['total'];
            }
            return $rdvData;
        } catch (PDOException $e) {
            return [];
        }
    }

    // Une fonction qui compte si ce patient existe déjà pour ce jour-là
    public function aDejaUnRdvDansCetteSpecialite($nom, $prenom, $date, $idMedecin) {
    // 1. On récupère d'abord la spécialité du médecin actuel
    $sqlSpec = "SELECT id_specialite FROM medecin WHERE id_medecin = ?";
    $stmtSpec = $this->pdo->prepare($sqlSpec);
    $stmtSpec->execute([$idMedecin]);
    $idSpecialite = $stmtSpec->fetchColumn();

    if (!$idSpecialite) return false;

    // 2. On vérifie si le patient a un RDV le même jour dans CETTE spécialité
    $sql = "SELECT COUNT(*) FROM rendez_vous r
            JOIN medecin m ON r.id_medecin = m.id_medecin
            WHERE r.nom_patient = :nom 
            AND r.prenom_patient = :prenom 
            AND r.date = :date 
            AND m.id_specialite = :id_spec";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'nom'     => $nom,
        'prenom'  => $prenom,
        'date'    => $date,
        'id_spec' => $idSpecialite
    ]);
    
    return $stmt->fetchColumn() > 0;
}


public function aDejaUnRdvDansCetteSpecialiteSaufCelui($nom, $prenom, $date, $idMedecin, $idRdvExclu) {
    $sqlSpec = "SELECT id_specialite FROM medecin WHERE id_medecin = ?";
    $stmtSpec = $this->pdo->prepare($sqlSpec);
    $stmtSpec->execute([$idMedecin]);
    $idSpecialite = $stmtSpec->fetchColumn();

    if (!$idSpecialite) return false;

    $sql = "SELECT COUNT(*) FROM rendez_vous r
            JOIN medecin m ON r.id_medecin = m.id_medecin
            WHERE r.nom_patient = :nom 
            AND r.prenom_patient = :prenom 
            AND r.date = :date 
            AND m.id_specialite = :id_spec
            AND r.id_rdv != :id_rdv_exclu";  // ← exclut le RDV actuel
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'nom'          => $nom,
        'prenom'       => $prenom,
        'date'         => $date,
        'id_spec'      => $idSpecialite,
        'id_rdv_exclu' => $idRdvExclu
    ]);
    
    return $stmt->fetchColumn() > 0;
}

    /**
 * Récupère tous les rendez-vous d'un patient avec les infos du médecin
 */
public function getRendezVousByPatient($id_patient) {
    // On sélectionne r.* (tous les champs du RDV) 
    // PLUS les noms qui nous manquent via des JOIN
    $sql = "SELECT 
                r.*, 
                u.nom as nom_medecin, 
                u.prenom as prenom_medecin,
                s.nom_specialite
            FROM rendez_vous r
            JOIN medecin m ON r.id_medecin = m.id_medecin
            JOIN utilisateur u ON m.id_medecin = u.id 
            JOIN specialite s ON m.id_specialite = s.id_specialite
            WHERE r.id_patient = ?
            ORDER BY r.date DESC";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id_patient]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Annuler un rendez-vous (change le statut en 'annule')
 */
public function annulerRendezVous($id_rdv) {
    $sql = "UPDATE rendez_vous SET statut = 'Annulé' WHERE id_rdv = ?";
    return $this->pdo->prepare($sql)->execute([$id_rdv]);
}

/**
 * Modifier la date et la période d'un rendez-vous
 */
public function modifierRendezVous($id_rdv, $nouvelle_date, $nouvelle_periode) {
    $sql = "UPDATE rendez_vous SET date = ?, periode = ?, statut = 'En attente'
            WHERE id_rdv = ?";
    return $this->pdo->prepare($sql)->execute([$nouvelle_date, $nouvelle_periode, $id_rdv]);
}

/**
 * Récupère un seul RDV par son ID (pour l'ordonnance)
 */
public function getRdvById($id_rdv) {
    $sql = "SELECT 
                r.*,
                u.nom as nom_medecin,
                u.prenom as prenom_medecin,
                s.nom_specialite,
                c.diagnostic,
                c.prescription
            FROM rendez_vous r
            JOIN medecin m ON r.id_medecin = m.id_medecin
            JOIN utilisateur u ON m.id_medecin = u.id
            JOIN specialite s ON m.id_specialite = s.id_specialite
            LEFT JOIN consultation c ON c.id_rdv = r.id_rdv
            WHERE r.id_rdv = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id_rdv]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function rechercherMedecins($recherche) {
    $sql = "SELECT u.id, u.nom, u.prenom, u.telephone,
                   m.type, m.status, m.heure_debut, m.heure_fin, m.jour_travail
            FROM medecin m
            JOIN utilisateur u ON m.id_medecin = u.id
            WHERE u.nom LIKE :search 
               OR u.prenom LIKE :search2";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'search'  => '%' . $recherche . '%',
        'search2' => '%' . $recherche . '%'
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
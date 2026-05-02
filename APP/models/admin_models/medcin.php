<?php
class Medecin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function ajouter($nom, $prenom, $username, $email, $tel, $mdp, $id_spec, $h_debut, $h_fin, $jours,$type) {
        try {
            $this->db->beginTransaction();

            // 1. Insertion dans la table 'utilisateur'
            // MODIFICATION : On ajoute 'mot_de_passe' ICI
            $sqlUser = "INSERT INTO utilisateur (nom, prenom, username, email, mot_de_passe, role, telephone) 
                        VALUES (:nom, :prenom, :username, :email, :mdp, 'medecin', :tel)";
            
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':nom'      => $nom,
                ':prenom'   => $prenom,
                ':username' => $username,
                ':email'    => $email,
                ':mdp'      => password_hash($mdp, PASSWORD_DEFAULT), // On hache ici !
                ':tel'      => $tel
            ]);

            $userId = $this->db->lastInsertId();

         // 2. Insertion dans la table 'medecin'
$jours_str = is_array($jours) ? implode(', ', $jours) : $jours;

// On retire 'status' de la liste des colonnes pour laisser la BDD gérer sa valeur initiale
$sqlMed = "INSERT INTO medecin (id_medecin, type, heure_debut, heure_fin, jour_travail, id_specialite) 
           VALUES (:id, :type, :h_debut, :h_fin, :jours, :id_spec)";

$stmtMed = $this->db->prepare($sqlMed);
$stmtMed->execute([
    ':id'        => $userId,
    ':type'      => $type,
    ':h_debut'   => $h_debut,
    ':h_fin'     => $h_fin,
    ':jours'     => $jours_str,
    ':id_spec'   => $id_spec
]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erreur SQL Médecin : " . $e->getMessage());
            return false;
        }
    }
    // --- AJOUTE CECI DANS LA CLASSE MEDECIN ---

// Pour afficher la grille des médecins
public function getAllMedecins() {
    $query = "SELECT u.nom, u.prenom, u.email, u.telephone, 
                     s.nom_specialite, m.status, m.id_medecin,
                     m.type, m.heure_debut, m.heure_fin, m.jour_travail 
              FROM utilisateur u 
              JOIN medecin m ON u.id = m.id_medecin 
              JOIN specialite s ON m.id_specialite = s.id_specialite
              WHERE u.role = 'medecin'";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Pour remplir la liste déroulante des spécialités
public function getAllSpecialities() {
    $stmt = $this->db->prepare("SELECT id_specialite, nom_specialite FROM specialite ORDER BY nom_specialite ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Pour charger les données d'UN médecin lors de la modification (Edit)
public function getMedecinById($id) {
    $stmt = $this->db->prepare("SELECT u.*, m.* FROM utilisateur u JOIN medecin m ON u.id = m.id_medecin WHERE m.id_medecin = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
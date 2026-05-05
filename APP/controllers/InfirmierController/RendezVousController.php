<?php

class RendezVousController {
    private $model;

    public function __construct($rendezVousModel) {
        $this->model = $rendezVousModel;
    }

    public function index() {
        // On récupère le médecin choisi en session
        $id_med_actif = isset($_SESSION['id_medecin_choisi']) ? (int)$_SESSION['id_medecin_choisi'] : null;

        if (!$id_med_actif) {
            header('Location: index.php?page=choix_medecin');
            exit();
        }

        // --- GESTION DU CHANGEMENT DE STATUT ---
        // Cette partie reçoit l'action du bouton (Présent, Absent ou le bouton "Fin")
        if (isset($_GET['action']) && $_GET['action'] === 'status') {
            if (isset($_GET['id']) && isset($_GET['valeur'])) {
                $id_rdv = (int)$_GET['id'];
                $nouveau_statut = $_GET['valeur']; // Ici, 'valeur' sera 'Absent' si on a cliqué sur 'Fin'

                // Mise à jour via le modèle
                $this->gererActionPresence($id_rdv, 'update_statut', $nouveau_statut);
                
                // Redirection pour rafraîchir la liste et appliquer le nouveau tri
                header('Location: index.php?page=presencePatient');
                exit();
            }
        }

        // On récupère les données avec le tri mis à jour (les Absents à la fin)
        $rdvs = $this->model->getTodayRendezVous($id_med_actif);
        
        $pageCSS = 'css/style_infirmier.css'; 
        $viewPath = __DIR__ . '/../../views/Infirmier/presencePatient.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Erreur : La vue 'presencePatient.php' est introuvable au chemin : " . $viewPath);
        }
    }

    /**
     * Méthode interne pour centraliser les mises à jour
     */
    public function gererActionPresence($id_rdv, $actionType, $valeur = null) {
        if ($actionType === 'update_statut' && $valeur !== null) {
            return $this->model->updateStatut($id_rdv, $valeur);
        }
        return false;
    }
}

/**
 * --- INSTANCIATION AUTOMATIQUE ---
 */
// 1. Initialisation du modèle (assurez-vous que $db est disponible via l'index)
$rdvModelForPresence = new RendezVousModel($db);

// 2. Création du contrôleur
$presenceCtrl = new RendezVousController($rdvModelForPresence);

// 3. Lancement
$presenceCtrl->index();
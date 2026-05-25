<?php
/**
 * Contrôleur pour la sélection du médecin par l'infirmier
 */
class ChoixMedecinController {
    private $medecinModel;

    public function __construct($medecinModel) {
        $this->medecinModel = $medecinModel;
    }

    public function handleRequest() {
        // --- SÉCURITÉ : Bloquer l'accès si un médecin a déjà été choisi ---
        if (isset($_SESSION['id_medecin_choisi'])) {
            header("Location: index.php?page=dashbord");
            exit();
        }

        $action = $_GET['action'] ?? null;
        if ($action === 'select_doctor') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $this->selectDoctor($id);
                return; 
            }
        }
        // Sinon afficher la liste
        $this->index();
    }

    public function index() {
        $id_spec_infirmier = $_SESSION['user']['id_specialite'] ?? $_SESSION['id_specialite'] ?? null;
        $medecins = $id_spec_infirmier ? $this->medecinModel->getMedecinsBySpecialite($id_spec_infirmier) : [];
        
        $pageTitle = "Sélection du Médecin | Santé Pro";
        $pageCSS = "css/style_infirmier.css"; 

        require_once __DIR__ . '/../../views/Infirmier/choix_medecin.php';
    }

    public function selectDoctor($id_medecin) {
        $medInfo = $this->medecinModel->getmedecinById($id_medecin);
        if ($medInfo) {
            $_SESSION['id_medecin_choisi'] = $id_medecin;
            $_SESSION['nom_medecin_choisi'] = ($medInfo['nom'] ?? '') . ' ' . ($medInfo['prenom'] ?? '');
            
            // On redirige vers le dashboard (orthographe 'dashbord' comme ton index)
            header("Location: index.php?page=dashbord");
            exit();
        }
    }
}

// --- AJOUTS POUR L'INSTANCIATION AUTOMATIQUE ---
// On charge le modèle nécessaire
require_once ROOT . '/APP/models/infirmier_models/GestionMedecinModel.php';

// On crée les objets en utilisant la variable $db de l'index
$medModel = new GestionMedecinModel($db); 
$choixCtrl = new ChoixMedecinController($medModel);

// On lance le traitement
$choixCtrl->handleRequest();
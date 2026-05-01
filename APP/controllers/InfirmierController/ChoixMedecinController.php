<?php

class ChoixMedecinController {
    private $medecinModel;

    public function __construct($medecinModel) {
        $this->medecinModel = $medecinModel;
    }

    public function handleRequest() {
        // Gérer l'action de sélection
        if (isset($_GET['action']) && $_GET['action'] === 'select_doctor') {
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
        $id_spec_infirmier = $_SESSION['user']['id_specialite'] ?? null;
        $medecins = $id_spec_infirmier ? $this->medecinModel->getMedecinsBySpecialite($id_spec_infirmier) : [];
        
        $pageTitle = "Sélection du Médecin | Santé Pro";
        $pageCSS = "css/style_infirmier.css"; 

        // Remonter de deux crans pour atteindre APP/views
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
<?php
// 1. Inclure la configuration de la base de données et le Modèle
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/DashboardModel.php';

class DashboardController {
    private $db;
    private $dashboardModel;

    public function __construct() {
        // Initialisation de la connexion
        $database = new Database();
        $this->db = $database->getConnection();

        // Initialisation du modèle en lui passant la connexion
        $this->dashboardModel = new DashboardModel($this->db);
    }

    public function index() {
        // 2. Récupération de toutes les données nécessaires via le Modèle
        $totalInfirmiers = $this->dashboardModel->getTotalInfirmiers();
        $totalMedecinsActifs = $this->dashboardModel->getTotalMedecinsActifs();
        $totalPatientsJour = $this->dashboardModel->getTotalPatientsJour();
        $totalAbsencesJour = $this->dashboardModel->getTotalAbsencesJour();
        
        // On récupère le Top 5 des médecins
        $topMedecins = $this->dashboardModel->getTopMedecins(5);

        // 3. Inclusion de la Vue (C'est ici que l'affichage HTML se produit)
        // Note : Les variables ci-dessus seront directement accessibles dans accueil.php
        include '../APP/views/admin/accueil.php';
    }
}

// 4. Exécution du contrôleur
$controller = new DashboardController();
$controller->index();
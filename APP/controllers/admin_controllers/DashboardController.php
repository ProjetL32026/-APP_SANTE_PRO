<?php
// 1. Inclure le Modèle via la constante ROOT définie dans l'index
require_once ROOT . '/APP/models/admin_models/DashboardModel.php';


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

        // 3. Inclusion de la Vue en utilisant ROOT
        require_once ROOT . '/APP/views/admin/accueil.php';
    }
}

// 4. Exécution du contrôleur
$controller = new DashboardController();
$controller->index();
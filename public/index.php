<?php
/**
 * SANTE_PRO - INDEX GLOBAL RÉVISÉ
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Définition des constantes
if (!defined('BASE_URL')) {
    define('BASE_URL', '/santepro');
}
define('ROOT', dirname(__DIR__));

// 2. Affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 3. Connexion à la base de données
require_once ROOT . '/config/db.php';
$db = $pdo;
//$database = new Database(); // Création de l'objet
//$db = $database->getConnection();
// 4. Récupération de TOUTES vos variables
$page = $_GET['page'] ?? 'accueil';
$controller = $_GET['controller'] ?? ''; // Variable controller rétablie
$action = $_GET['action'] ?? null;
$role = $_SESSION['role'] ?? $_SESSION['user_role'] ?? null;

// 5. Bloc de détection Connexion / Inscription (Votre logique exacte)
if ($page === 'connexion' || (isset($_GET['action']) && $_GET['action'] === 'login')) {
    if (isset($_GET['from']) && $_GET['from'] === 'inscription') {
        // On mémorise qu'après le login, il faut aller au RDV
        $_SESSION['redirect_after_login'] = 'rdv';
        $_SESSION['temp_id_medecin'] = $_GET['idMedecin'] ?? null;
    } else {
        // Sinon, on s'assure que la redirection par défaut est l'accueil
        $_SESSION['redirect_after_login'] = 'accueil';
    }
}

// 6. Gestion de la déconnexion
if ($page === 'logout' || $page === 'deconnexion') {
    session_destroy();
    header("Location: index.php?page=accueil");
    exit();
}

/**
 * 7. ROUTAGE DES ACTIONS (AuthController du Patient)
 */
if ($controller === 'auth') {
    require_once ROOT . '/APP/controllers/PatientController/AuthController.php';
    exit();
}

/**
 * 8. ROUTAGE DU LOGIN UNIQUE (Professionnels)
 */
if ($page === 'log') {
    require_once ROOT . '/APP/controllers/admin_controllers/LoginController.php';
    exit();
}

/**
 * 9. ROUTAGE SELON LE RÔLE OU ACCÈS LIBRE
 */

// --- ESPACE ADMINISTRATEUR ---
if ($role === 'admin') {
    switch ($page) {
        case 'medcin':
            require_once ROOT . '/APP/controllers/admin_controllers/medcinController.php';
            break;
        case 'infirmier':
            require_once ROOT . '/APP/controllers/admin_controllers/InfirmierController.php';
            break;
        case 'specialite':
            require_once ROOT . '/APP/controllers/admin_controllers/SpecialiteController.php';
            break;
        case 'statistique':
            require_once ROOT . '/APP/controllers/admin_controllers/StatsController.php';
            break;
        case 'parametre':
            require_once ROOT . '/APP/controllers/admin_controllers/AdminController.php';
            break;
        default:
            require_once ROOT . '/APP/controllers/admin_controllers/DashboardController.php';
            break;
    }
}

// --- ESPACE INFIRMIER ---
elseif ($role === 'infirmier') {
    require_once ROOT . '/APP/models/infirmier_models/RendezVousModel.php';
    require_once ROOT . '/APP/models/infirmier_models/TicketModel.php';
    switch ($page) {
        case 'choix_medecin':
            require_once ROOT . '/APP/controllers/InfirmierController/ChoixMedecinController.php';
            break;
        case 'presencePatient':
            require_once ROOT . '/APP/controllers/InfirmierController/RendezVousController.php';
            break;
        default:
            require_once ROOT . '/APP/controllers/InfirmierController/DashboardController.php';
            break;
    }
}

// --- ESPACE MÉDECIN ---
elseif ($role === 'medecin') {
    require_once ROOT . '/APP/controllers/MedecinControllers/MedecinController.php';
}

/**
 * 10. SÉPARATION PATIENT ET TICKET (ACCÈS LIBRE)
 */ else {
    switch ($page) {
        // PARTIE PATIENT
        case 'rdv':
            require_once ROOT . '/APP/controllers/PatientController/RdvController.php';
            break;
        case 'historique':
            require_once ROOT . '/APP/controllers/PatientController/HistoriqueController.php';
            break;
        case 'inscription':
            require_once ROOT . '/APP/controllers/PatientController/InscriptionController.php';
            break;
        case 'verification':
            require_once ROOT . '/APP/views/patient/verification.php';
            break;
        case 'connexion':
            require_once ROOT . '/APP/views/patient/inscription.php';
            break;

        case 'annuler_rdv':
    $data  = json_decode(file_get_contents('php://input'), true);
    $idRdv = (int)($data['idRdv'] ?? 0);

    if (!isset($_SESSION['patient_id']) || $idRdv <= 0) {
        echo json_encode(['success' => false]);
        exit();
    }

    // CORRECTION : instancier le modèle ici
    require_once ROOT . '/APP/models/Pmodel/PatientModel.php';
    $patientModel = new PatientModel($pdo);
    $ok = $patientModel->annulerRendezVous($idRdv);
    echo json_encode(['success' => (bool)$ok]);
    exit();

case 'modifier_rdv':
    $data    = json_decode(file_get_contents('php://input'), true);
    $idRdv   = (int)($data['idRdv'] ?? 0);
    $date    = $data['date']    ?? '';
    $periode = $data['periode'] ?? '';

    if (!isset($_SESSION['patient_id']) || $idRdv <= 0 || !$date) {
        echo json_encode(['success' => false, 'message' => 'Données invalides.']);
        exit();
    }

    require_once ROOT . '/APP/models/Pmodel/PatientModel.php';
    $patientModel = new PatientModel($pdo);

    // Récupérer le RDV existant pour avoir nom/prénom/médecin
    $rdvExistant = $patientModel->getRdvById($idRdv);

    // Vérification jour de travail du médecin
    $medecin = $patientModel->getMedecinById($rdvExistant['id_medecin']);
    $map = ['Lun'=>1,'Mar'=>2,'Mer'=>3,'Jeu'=>4,'Ven'=>5,'Sam'=>6,'Dim'=>0];
    $joursPermis = [];
    foreach (explode(',', $medecin['jour_travail'] ?? '') as $j) {
        $cle = trim($j);
        if (isset($map[$cle])) $joursPermis[] = $map[$cle];
    }
    $jourChoisi = (int)date('w', strtotime($date));
    if (!in_array($jourChoisi, $joursPermis)) {
        echo json_encode(['success' => false, 'message' => 'Ce médecin ne travaille pas ce jour-là.']);
        exit();
    }

    // AJOUT : Vérification doublon — même patient, même spécialité, même jour
    // Mais on exclut le RDV en cours de modification (idRdv)
    if ($patientModel->aDejaUnRdvDansCetteSpecialiteSaufCelui(
        $rdvExistant['nom_patient'],
        $rdvExistant['prenom_patient'],
        $date,
        $rdvExistant['id_medecin'],
        $idRdv  // ← on exclut le RDV actuel
    )) {
        echo json_encode(['success' => false, 'message' => 'Ce patient a déjà un rendez-vous ce jour-là dans cette spécialité.']);
        exit();
    }

    $ok = $patientModel->modifierRendezVous($idRdv, $date, $periode);
    echo json_encode(['success' => (bool)$ok]);
    exit();

    case 'voir_ordonnance':
    if (!isset($_SESSION['patient_id'])) {
        echo '<p class="text-danger">Non autorisé.</p>';
        exit;
    }
    require_once ROOT . '/APP/models/Pmodel/PatientModel.php';
    global $pdo;
    $patientModel = new PatientModel($pdo);
    $id  = intval($_GET['id'] ?? 0);
    $rdv = $patientModel->getRdvById($id);
    include ROOT . '/APP/views/medecin/ordonnance.php';
    exit;

        // PARTIE TICKET (SÉCURITÉ / EMAIL TOUTE SEULE)
        case 'ticket':
            require_once ROOT . '/APP/controllers/securiteController/TicketController.php';
            $ticketCtrl = new TicketController();

            if ($action === 'valider') {
                $ticketCtrl->validerEtAfficher();
            }
            // On ajoute 'voirFile' ici pour que le routeur le reconnaisse
            elseif ($action === 'live' || $action === 'voir_file' || $action === 'voirFile') {
                $ticketCtrl->voirFile();
            } else {
                $ticketCtrl->showSaisie();
            }
            break;

        case 'accueil':
        case 'accueil_patient':
        default:
            require_once ROOT . '/APP/controllers/PatientController/AccueilController.php';
            break;
    }
}
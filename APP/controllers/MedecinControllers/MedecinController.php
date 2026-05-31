<?php
require_once '../APP/models/MedecinModels/MedecinModel.php';
$model = new MedecinModel($pdo);

$id_medecin = $_SESSION['user_id'] ?? null;
$action = $_GET['action'] ?? 'liste';

// Sécurité : On s'assure que le médecin est bien connecté
if (!$id_medecin) {
    header('Location: index.php?page=connexion');
    exit();
}

$is_en_conge = $model->getStatusConge($id_medecin);

switch ($action) {
    case 'liste':
        $file_attente = $model->getFileAttente($id_medecin);
        $nb_termines = $model->getCountTermines($id_medecin);

        $pageTitle = "Tableau de Bord";
        $pageCSS = "stylebaya.css";

        require_once '../APP/views/layout/header.php';       // 1. En-tête global
        require_once '../APP/views/medecin/file_attente.php'; // 2. Le corps (La vue épurée)
        require_once '../APP/views/layout/footer.php';       // 3. Le pied de page global
        break;

    case 'consulter':
        $id_rdv = filter_input(INPUT_GET, 'id_rdv', FILTER_VALIDATE_INT);
        if (!$id_rdv) {
            header('Location: index.php?page=medecin&action=liste');
            exit();
        }

        $model->updateStatutEnConsultation($id_rdv);
        $patientData = $model->getPatientDetails($id_rdv);

        $pageTitle = "Dossier Patient";
        $pageCSS = "stylebaya.css";
        $pageScripts = ['jsbaya/consultation.js'];

        require_once '../APP/views/layout/header.php';
        require_once '../APP/views/medecin/consultation.php'; 
        require_once '../APP/views/layout/footer.php';
        break;

    case 'annuler_consultation':
        $id_rdv = $_GET['id_rdv'] ?? null;
        if ($id_rdv) {
            $model->updateStatutRetourFile($id_rdv);
        }
        // AJOUTER page=medecin ICI
        header('Location: index.php?page=medecin&action=liste');
        exit();
        break;

    case 'enregistrer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_rdv = filter_input(INPUT_POST, 'id_rdv', FILTER_VALIDATE_INT);
            $diagnostic = trim($_POST['diagnostic'] ?? '');

            // Récupération et formatage de la prescription (médicaments)
            $medocs = $_POST['medoc'] ?? [];
            $posos = $_POST['poso'] ?? [];
            $durees = $_POST['duree'] ?? [];

            $prescriptionLines = [];
            for ($i = 0; $i < count($medocs); $i++) {
                if (!empty($medocs[$i])) {
                    $prescriptionLines[] = "• " . htmlspecialchars($medocs[$i]) . " : " . htmlspecialchars($posos[$i]) . " (" . htmlspecialchars($durees[$i]) . ")";
                }
            }
            $prescriptionFormattee = implode("<br>", $prescriptionLines);

            $success = false;
            if ($id_rdv && (!empty($diagnostic) || !empty($prescriptionFormattee))) {
                $success = $model->saveConsultation($id_rdv, $id_medecin, $diagnostic, $prescriptionFormattee);
                $model->updateStatutRetourFile($id_rdv);
            }

            // 2. 🔥 ICI : On force le statut à rester ou devenir 'Chez le medecin'
            if ($success) {
                $model->updateStatutEnConsultation($id_rdv);
            }
            header('Location: index.php?page=medecin&action=liste');
            exit();
        }
        break;

    case 'historique':
        // 1. Récupération du mot-clé s'il existe
        $search = $_GET['search'] ?? '';

        // 2. Appel du modèle avec le filtre de recherche
        $historique = $model->getHistorique($id_medecin, $search);

        // 3. Détection de la requête AJAX
        // Ton fichier historique.js envoie l'en-tête 'X-Requested-With'
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Si c'est de l'AJAX, on affiche UNIQUEMENT les lignes du tableau
            include '../APP/views/medecin/historique_rows.php';
            exit(); // On stoppe le script pour ne pas charger les structures globales
        }

        // Cas normal (chargement initial de la page ou actualisation globale)
        $pageTitle = "Historique des Consultations";
        $pageCSS = "stylebaya.css";
        $pageScripts = ['jsbaya/historique.js', 'jsbaya/statut.js'];

        require_once '../APP/views/layout/header.php';
        require_once '../APP/views/medecin/historique.php';
        require_once '../APP/views/layout/footer.php';
        break;

    case 'get_ordonnance':
        $id_rdv = filter_input(INPUT_GET, 'id_rdv', FILTER_VALIDATE_INT);
        if ($id_rdv) {
            $cons = $model->getDetailsConsultation($id_rdv);

            if ($cons) {
                // Cas normal : On inclut la vue partielle de l'ordonnance
                include '../APP/views/medecin/ordonnance.php';
            } else {
                // CORRECTION MVC : Au lieu d'un "echo" de div Bootstrap, 
                // on délègue le rendu de l'erreur à une vue dédiée.
                require_once '../APP/views/errors/404.php';
            }
        }
        exit(); // Très important pour stopper le script en cas de requête AJAX
        break;

    case 'toggle_conge':
        // Sécurisation de la sortie en pur JSON
        ob_clean();
        header('Content-Type: application/json');

        $nouveauStatus = $_POST['status'] ?? 'Actif';
        $success = $model->updateStatusConge($id_medecin, $nouveauStatus);

        echo json_encode([
            'success' => $success,
            'status' => $nouveauStatus,
            'message' => $success ? "Statut mis à jour avec succès." : "Erreur lors de la mise à jour."
        ]);
        exit();
        break;

    default:
        header('Location: index.php?page=medecin&action=liste');
        exit();
}

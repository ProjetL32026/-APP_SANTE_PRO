<?php
require_once '../APP/models/MedecinModels/MedecinModel.php';
$model = new MedecinModel($pdo);

$id_medecin = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'liste';

switch ($action) {
    case 'liste':
        $file_attente = $model->getFileAttente($id_medecin);
        $nb_termines = $model->getCountTermines($id_medecin);
        $pageTitle = "Tableau de Bord";
        $pageCSS = "stylebaya.css";

        // Permet à la sidebar de savoir si le bouton doit être coché
        $is_en_conge = $model->getStatusConge($id_medecin);

        require_once '../APP/views/layout/header.php';
        require_once '../APP/views/medecin/file_attente.php';
        require_once '../APP/views/layout/footer.php';
        break;

    case 'consulter':
        $id_rdv = $_GET['id_rdv'] ?? null;
        if ($id_rdv) {
            $model->updateStatutEnConsultation($id_rdv);
            $patient = $model->getPatientDetails($id_rdv);
            $pageTitle = "Fiche Patient";
            $pageCSS = "stylebaya.css";

            require_once '../APP/views/layout/header.php';
            require_once '../APP/views/medecin/consultation.php';
            require_once '../APP/views/layout/footer.php';
        }
        break;

    case 'annuler_consultation':
        $id_rdv = $_GET['id_rdv'] ?? null;
        if ($id_rdv) {
            $model->updateStatutRetourFile($id_rdv);
        }
        header('Location: index.php?action=liste');
        exit();
        break;

    case 'enregistrer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_rdv = $_POST['id_rdv'] ?? null;
            $diagnostic = $_POST['diagnostic'] ?? '';

            if ($id_rdv) {
                $ordonnance_finale = "";
                if (isset($_POST['medoc']) && is_array($_POST['medoc'])) {
                    foreach ($_POST['medoc'] as $key => $nom_medoc) {
                        if (!empty(trim($nom_medoc))) {
                            $poso = $_POST['poso'][$key] ?? '';
                            $duree = $_POST['duree'][$key] ?? '';
                            $ordonnance_finale .= "• " . htmlspecialchars($nom_medoc) . " : " . htmlspecialchars($poso) . " (" . htmlspecialchars($duree) . ")\n";
                        }
                    }
                }
                $success = $model->saveConsultation($id_rdv, $id_medecin, $diagnostic, $ordonnance_finale);
                header('Location: index.php?action=liste&saved=' . ($success ? '1' : '0'));
                exit();
            }
            header('Location: index.php?action=liste&error=missing_id');
            exit();
        }
        break;

    case 'historique':
        $historique = $model->getHistorique($id_medecin);
        // Récupération du statut pour la sidebar ici aussi
        $is_en_conge = $model->getStatusConge($id_medecin);
        $pageTitle = "Historique";
        $pageCSS = "stylebaya.css";

        require_once '../APP/views/layout/header.php';
        require_once '../APP/views/medecin/historique.php';
        require_once '../APP/views/layout/footer.php';
        break;

    case 'get_ordonnance':
        $id_rdv = $_GET['id_rdv'] ?? null;
        if ($id_rdv) {
            $cons = $model->getDetailsConsultation($id_rdv);
            if ($cons) {
                include '../APP/views/medecin/ordonnance.php';
            } else {
                echo "<div class='alert alert-danger'>Consultation introuvable.</div>";
            }
        }
        exit();
        break;

    case 'toggle_conge':
        // On nettoie le tampon pour être sûr de n'envoyer QUE du JSON
        ob_clean();
        header('Content-Type: application/json');

        $nouveauStatus = $_POST['status'] ?? 'actif';
        $success = $model->updateStatusConge($id_medecin, $nouveauStatus);

        // On répond au format JSON pour statut.js
        echo json_encode([
            'success' => $success,
            'status' => $nouveauStatus
        ]);
        exit();
        break;
}

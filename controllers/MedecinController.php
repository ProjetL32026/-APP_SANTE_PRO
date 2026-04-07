<?php
require_once '../config/db.php';
require_once '../models/MedecinModel.php';

$model = new MedecinModel($pdo);
$id_medecin = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'liste';

switch ($action) {
    case 'liste':
        $file_attente = $model->getFileAttente($id_medecin);
        $nb_termines = $model->getCountTermines($id_medecin);
        require_once '../APP/views/medecin/file_attente.php';
        break;

    case 'consulter':
        $id_rdv = $_GET['id_rdv'] ?? null;
        $patient = $model->getPatientDetails($id_rdv);
        require_once '../APP/views/medecin/consultation.php';
        break;

    case 'enregistrer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Récupération du diagnostic
            $diagnostic = $_POST['diagnostic'] ?? '';

            // 2. Construction de l'ordonnance à partir des listes
            $ordonnance_finale = "";
            if (isset($_POST['medoc']) && is_array($_POST['medoc'])) {
                foreach ($_POST['medoc'] as $key => $nom_medoc) {
                    if (!empty(trim($nom_medoc))) {
                        $poso = $_POST['poso'][$key] ?? '';
                        $duree = $_POST['duree'][$key] ?? '';
                        // On formate chaque ligne avec une puce
                        $ordonnance_finale .= "• " . htmlspecialchars($nom_medoc) . " : " . htmlspecialchars($poso) . " (" . htmlspecialchars($duree) . ")\n";
                    }
                }
            }

            // 3. Sauvegarde via le modèle
            $success = $model->saveConsultation(
                $_POST['id_rdv'],
                $id_medecin,
                $diagnostic,
                $ordonnance_finale // On envoie la chaîne construite ici
            );

            header('Location: index.php?action=liste&saved=' . ($success ? '1' : '0'));
            exit();
        }
        break;

    case 'historique':
        $historique = $model->getHistorique($id_medecin);
        require_once '../APP/views/medecin/historique.php';
        break;
}

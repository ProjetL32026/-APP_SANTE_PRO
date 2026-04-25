<?php
// On garde les inclusions de base (Modèle et DB sont déjà gérés)
require_once '../APP/models/MedecinModel.php';
$model = new MedecinModel($pdo);

$id_medecin = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'liste';

switch ($action) {
    case 'liste':
        $file_attente = $model->getFileAttente($id_medecin);
        $nb_termines = $model->getCountTermines($id_medecin);
        $is_en_conge = $model->getStatutConge($id_medecin);

        // --- AFFICHAGE COMPLET ---
        require_once '../APP/views/layout/header.php';    // Affiche la sidebar et le début du HTML
        require_once '../APP/views/medecin/file_attente.php';
        require_once '../APP/views/layout/footer.php';    // Ferme les balises HTML
        break;

    case 'consulter':
        $id_rdv = $_GET['id_rdv'] ?? null;
        if ($id_rdv) {
            $model->updateStatutEnConsultation($id_rdv);
            $patient = $model->getPatientDetails($id_rdv);
            $is_en_conge = $model->getStatutConge($id_medecin);

            // --- AFFICHAGE COMPLET ---
            require_once '../APP/views/layout/header.php';
            require_once '../APP/views/medecin/consultation.php';
            require_once '../APP/views/layout/footer.php';
        }
        break;

    case 'annuler_consultation':
        $id_rdv = $_GET['id_rdv'] ?? null;
        if ($id_rdv) {
            // Le statut repasse à 'Présent' si on clique sur retour
            $model->updateStatutRetourFile($id_rdv);
        }
        header('Location: index.php?action=liste');
        exit();
        break;

    case 'enregistrer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // On récupère l'ID envoyé par le champ caché
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

                // Utilisation du modèle
                $success = $model->saveConsultation($id_rdv, $id_medecin, $diagnostic, $ordonnance_finale);

                header('Location: index.php?action=liste&saved=' . ($success ? '1' : '0'));
                exit();
            } else {
                // Si pas d'ID, on retourne à la liste par sécurité
                header('Location: index.php?action=liste&error=missing_id');
                exit();
            }
        }
        break;

    case 'historique':
        $id_medecin = $_SESSION['user_id'];
        $historique = $model->getHistorique($id_medecin);
        $is_en_conge = $model->getStatutConge($id_medecin);

        // C'est ICI qu'on répare l'affichage
        require_once '../APP/views/layout/header.php';    // Pour avoir la sidebar et le CSS
        require_once '../APP/views/medecin/historique.php'; // La vue elle-même
        require_once '../APP/views/layout/footer.php';    // Pour fermer le HTML
        break;
    // Dans ton switch ($action)
    case 'get_ordonnance':
        $id_rdv = $_GET['id_rdv'] ?? null;
        if ($id_rdv) {
            $cons = $model->getConsultationById($id_rdv);
            if ($cons) {
                // Cette ligne est CRUCIALE pour envoyer le HTML au JavaScript
                require_once '../APP/views/medecin/ordonnance.php';
            } else {
                echo "Détails introuvables.";
            }
        }
        exit(); // Arrête tout ici pour ne pas charger le reste de la page
        break;

    case 'toggle_conge':
        $nouveauStatus = $_POST['status'] ?? 'actif'; // Récupère "en congé" ou "actif"
        $id_medecin = $_SESSION['user_id'];

        $success = $model->updateStatusConge($id_medecin, $nouveauStatus);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
}

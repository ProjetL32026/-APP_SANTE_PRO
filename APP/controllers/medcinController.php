if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/db.php';
    require_once '../Models/medecin.php';

    $db = (new Database())->getConnection();
    $medecin = new Medecin($db);

    $nom      = $_POST['nom'];
    $prenom   = $_POST['prenom'];
    $email    = $_POST['email'];
    $tel      = $_POST['telephone'];
    $mdp      = $_POST['password'];
    $spec     = $_POST['specialite'];
    $h_debut  = $_POST['heure_debut'];
    $h_fin    = $_POST['heure_fin'];
    $jours    = $_POST['jours_travail'] ?? []; // Tableau des jours cochés

    if ($medecin->ajouterMedecin($nom, $prenom, $email, $tel, $mdp, $spec, $jours, $h_debut, $h_fin)) {
        header("Location: /SANTÉ_PRO/public/index.php?page=medecin&status=success");
    } else {
        header("Location: /SANTÉ_PRO/public/index.php?page=medecin&status=error");
    }
    exit();
}
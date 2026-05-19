<?php
$pageTitle = "Vérification du compte";
$pageCSS ='stylep.css';
$bodyClass  = 'bg-light';
include ROOT . '/APP/views/layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <span style="font-size: 3rem;">📩</span>
                    </div>
                    <h2 class="fw-bold mb-3">Vérifiez votre compte</h2>
                    <p class="text-muted">Un code de validation a été envoyé. Veuillez le saisir ci-dessous pour finaliser votre inscription.</p>
                    
                   <?php 
    // On récupère l'id depuis l'URL proprement
   $idMedecinAffiche = $_GET['idMedecin'] 
                 ?? $_SESSION['temp_id_medecin'] 
                 ?? '';
?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'code_invalide'): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-3">
        <i class="fas fa-exclamation-circle"></i>
        <span>Code incorrect. Veuillez vérifier et réessayer.</span>
    </div>
<?php endif; ?>

<form action="index.php?controller=auth&action=valider_code" method="POST">
    
    <input type="hidden" name="id_medecin" value="<?php echo htmlspecialchars($idMedecinAffiche); ?>">
    
    <div class="mb-4">
        <input type="text" name="code_verif" class="form-control form-control-lg text-center fw-bold" 
               placeholder="000000" maxlength="6" required style="letter-spacing: 10px; font-size: 1.5rem;">
    </div>
    
    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
        Confirmer et continuer
    </button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT . '/APP/views/layout/footer.php'; ?>
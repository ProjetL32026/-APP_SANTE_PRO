<?php
$pageTitle = "Vérification du compte";
$pageCSS ='styleP'
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
                    
                    <form action="index.php?controller=auth&action=valider_code<?= isset($_GET['idMedecin']) ? '&idMedecin='.$_GET['idMedecin'] : '' ?>" method="POST">
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
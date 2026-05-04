<?php 
require_once ROOT . '/config/db.php';
$pageCSS    = 'stylep.css';
$bodyClass  = 'bg-light';
$idMedecin = $_GET['idMedecin'] ?? '';
include ROOT . '/APP/views/layout/header.php';

 ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h4 class="text-aqua-dark fw-bold mb-4 text-center">Vos informations personnelles</h4>
                
               <?php 
// 1. On prépare la variable une seule fois pour tout le fichier
$idMedecin = $_GET['idMedecin'] ?? ''; 
?>

<div class="text-center mb-4">
    <h2 class="fw-bold">
        <?= $idMedecin ? "Inscrivez-vous pour finaliser votre rendez-vous" : "Créer un compte"; ?>
    </h2>
</div>

<div class="mb-4 text-center">
    <span class="text-muted">Vous avez déjà un compte ?</span>
    <a href="#"  data-bs-toggle="modal" data-bs-target="#loginModal" class="text-info fw-bold text-decoration-none ms-1">
        Connectez-vous ici
    </a>

    
</div>

                <form action="index.php?controller=auth&action=inscription" method="POST">
                   <input type="hidden" name="id_medecin" value="<?php echo $idMedecin; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nom complet *</label>
                            <input type="text" name="nom" class="form-control rounded-3" placeholder="Ex : Benali" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Prénom *</label>
                            <input type="text" name="prenom" class="form-control rounded-3" placeholder="Ex : Ahmed" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Adresse email *</label>
                            <div class="input-group">
                                <input type="email" id="email_input" name="email" class="form-control rounded-start-3" placeholder="exemple@email.com" required>
                                
                            </div>
                            <div id="msg-envoi" class="form-text mt-2"></div>
                        </div>

                        

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nom d'utilisateur *</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mot de passe *</label>
                            <input type="password" name="mdp" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Date de naissance *</label>
                            <input type="date" name="date_naissance" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Téléphone *</label>
                            <input type="tel" name="telephone" class="form-control" placeholder="05XXXXXXXX" required>
                        </div>
                    </div>

                    <div class="text-center mt-5">
        <button type="submit" class="btn shadow-lg rounded-pill px-5 fw-bold" 
                style="background: linear-gradient(135deg, #075985, #0ea5e9); 
                       color: #FFFFFF !important; 
                       border: none; 
                       height: 55px; 
                       min-width: 280px;">
            <span style="color: #FFFFFF !important;">
                <?php if ($idMedecin): ?>
            <i class="fas fa-calendar-check me-2"></i> S'inscrire et prendre rendez-vous
            <?php else: ?>
            <i class="fas fa-user-plus me-2"></i> Confirmer l'inscription
            <?php endif; ?></span>
    </button>
    </div>
                </form>
            </div>   
        </div>
    </div>
</div>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-aqua-dark" id="loginModalLabel">Connexion Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="index.php?controller=auth&action=login<?= !empty($idMedecin) ? '&idMedecin='.$idMedecin : '' ?>" method="POST">
                    <?php $idMed = $_GET['idMedecin'] ?? ''; ?>
                    <input type="hidden" name="id_medecin" value="<?= $idMedecin ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Adresse email</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="votre@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mot de passe</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" required>
                    </div>



                    <div class="text-center mt-5">
    <button type="submit" class="btn btn-aqua-grad btn-lg rounded-pill px-5 fw-bold shadow-lg" style="min-width: 280px;">
        <span>
            <?php if ($idMed): ?>
                Confirmer et prendre rendez-vous
            <?php else: ?>
                Se connecter
            <?php endif; ?>
        </span>
        <i class="fas fa-calendar-check ms-2"></i>
    </button>
</div>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<!--<script>
// Gestion de l'affichage de la zone du code
document.getElementById('btn-envoyer-code').addEventListener('click', function() {
    const email = document.getElementById('email_input').value;
    const msg = document.getElementById('msg-envoi');
    
    if(email.includes('@')) {
        // Message visuel pour le patient
        msg.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Un code de confirmation a été envoyé à ' + email + '</span>';
        
        // On affiche le champ de saisie du code avec une petite animation
        const zoneCode = document.getElementById('zone-code');
        zoneCode.style.display = 'block';
    } else {
        alert('Veuillez entrer une adresse email valide.');
    }
});
</script>-->

<?php include ROOT . '/APP/views/layout/footer.php'; ?>
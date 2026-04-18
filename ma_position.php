<?php
$infos = $mailCtrl->voirEtatFileAttente($_SESSION['id_ticket'], $_SESSION['id_medecin']);
?>

<div class="file-container">
    <h3>Suivi de votre passage</h3>
    <hr>

    <div class="card-attente">
        <label>Votre Rang</label>
        <span>#
            <?php echo $infos['votre_position']; ?>
        </span>
    </div>

    <div class="card-attente">
        <label>En cours</label>
        <span>n°
            <?php echo $infos['actuellement_au_cabinet']; ?>
        </span>
    </div>

    <div class="card-attente">
        <label>Attente</label>
        <span class="urgent">
            <?php echo $infos['personnes_avant_vous']; ?> pers.
        </span>
    </div>

    <?php if ($infos['personnes_avant_vous'] == 0): ?>
        <p style="color:green; font-weight:bold; margin-top:10px;">C'est bientôt votre tour ! Préparez-vous.</p>
    <?php endif; ?>
</div>
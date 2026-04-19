<?php
require_once ROOT . '/config/connexion.php';
$pageTitle  = 'Santé Pro - Accueil';
$pageScript = 'accueil.js';
include ROOT . '/APP/views/layout/header.php';

?>
<!-- ══════════════════════════════════
       HERO:  La grande bannière avec un titre, un sous-titre, et un bouton qui, quand on clique, fait défiler la page jusqu'à la section #specialites (grâce au href="#specialites")
  ══════════════════════════════════ -->
  <section class="hero-section text-white text-center">
    <!-- Cercles flottants décoratifs -->
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>
    <div class="hero-blob hero-blob-3"></div>
 
    <div class="container position-relative">
      <div class="hero-badge">
        <span class="dot"></span> Cabinet médical en ligne · Béjaia
      </div>
 
      <h1 class="fw-bold mb-3">Bienvenue au Cabinet<br>Santé Pro</h1>
      <p class="lead mb-5 mx-auto">Prenez rendez-vous facilement avec nos médecins spécialistes, 24h/24</p>
 
      <div class="hero-actions d-flex gap-3 justify-content-center flex-wrap">
        <a href="index.php?page=accueil#services-section" class="btn-hero-primary">
          <i class="fas fa-calendar-check me-2"></i>Prendre un rendez-vous
        </a>
        <a href="#localisation" class="btn-hero-secondary">
          <i class="fas fa-map-marker-alt me-2"></i>Nous localiser
        </a>
      </div>
    </div>
  </section>
 
  <!-- Barre de statistiques -->
  <div class="stats-bar">
    <div class="container">
      <div class="d-flex justify-content-center align-items-center gap-0">
        <div class="stat-item px-4">
          <div class="stat-number"><?php echo $nbMedecins; ?></div>
          <div class="stat-label">Médecins actifs</div>
        </div>
        
        <div class="stat-sep align-self-stretch" style="min-height:40px"></div>
        
        <div class="stat-item px-4">
          <div class="stat-number"><?php echo $nbSpecialites; ?></div>
          <div class="stat-label">Spécialités</div>
        </div>

        <div class="stat-sep align-self-stretch" style="min-height:40px"></div>

        <div class="stat-item px-4">
          <div class="stat-number"><?php echo $rdvJourMax; ?></div>
          <div class="stat-label">RDV / jour max</div>
        </div>

        <div class="stat-sep align-self-stretch d-none d-md-block" style="min-height:40px"></div>

        <div class="stat-item px-4 d-none d-md-block">
          <div class="stat-number">24h</div>
          <div class="stat-label">Réservation en ligne</div>
        </div>
      </div>
    </div>
</div>
 
  <!-- ══════════════════════════════════
       SPÉCIALITÉS + MÉDECINS
  ══════════════════════════════════ -->
  <section id="services-section" class="py-5">
    <div class="container">

        <div class="text-center mb-5">
            <div class="mb-2">
                <span class="section-badge"><i class="fas fa-stethoscope me-1"></i> NOS SERVICES</span>
            </div>
            <h2 class="text-center fw-bold text-aqua-dark" style="font-size:2.2rem;">
                <?php 
                    if ($specSelectionnee && isset($specialites)) {
                        // On cherche le nom de la spécialité cliquée et On affiche le nom de la spécialité (on le récupère du premier médecin trouvé)
                        $nomSpec = "";
                        foreach($specialites as $s) { if($s['id'] == $specSelectionnee) $nomSpec = $s['nom']; }
                        echo "Médecins en " . htmlspecialchars($nomSpec);
                    } else {
                        echo "Nos Spécialités";
                    }
                ?>
            </h2>  <!--Pour que le titre change (ex: "Médecins en Ophtalmologie"), on va d'abord récupérer le nom de la spécialité dans le Contrôleur ou directement dans la Vue.-->
            <?php if (!$specSelectionnee): ?>
                <p class="text-muted">Choisissez votre spécialité pour voir les médecins disponibles</p>
            <?php endif; ?>
        </div>
        
        <?php if (!$specSelectionnee): ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($specialites as $spec): ?>
             <!--cette boucle PHP :
             Si ma copine (l'admin) ajoute une spécialité "Ophtalmologie" dans la base de données, elle apparaîtra automatiquement sur l'accueil sans que tu touches au code.
            Le nombre de médecins se mettra à jour en temps réel.-->  
            <div class="col-md-4">
                <div class="card shadow-sm h-100 border-0 rounded-4 specialty-card" 
                     onclick="window.location.href='index.php?page=accueil&spec=<?php echo $spec['id']; ?>#services-section'"
                     style="cursor:pointer;">
                    <div class="card-body text-center p-5">
                        <div class="spec-icon-wrap mb-3" style="font-size: 2.5rem;">🩺</div>
                        <h3 class="h5 fw-bold text-aqua-dark mb-3"><?php echo htmlspecialchars($spec['nom']); ?></h3>
                        <span class="badge rounded-pill bg-aqua-light text-aqua-dark px-3 py-2">
                            <?php echo $spec['nbMedecins']; ?> médecins disponibles
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="text-center mb-4">
            <a href="index.php?page=accueil#services-section" class="btn btn-outline-aqua rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Retour aux spécialités
            </a>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($medecins)): ?>
                <?php foreach ($medecins as $doc): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4">
                        <div class="card-body">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                 style="width: 70px; height: 70px; background-color: #00a8e8; border-radius: 50%; color: white; font-size: 2rem;">
                                👤
                            </div>
                            
                            <h4 class="fw-bold text-aqua-dark h5 mb-1">
                                Dr. <?php echo htmlspecialchars($doc['nom'] . ' ' . $doc['prenom']); ?>
                            </h4>
                            
                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($doc['type']); ?></p> <!-- le type du médecin  titulaire ou visiteur -->
                            
                            <p class="fw-bold <?php echo ($doc['status'] == 'disponible') ? 'text-success' : 'text-danger'; ?>" style="font-size: 0.9rem;">
                                ● <?php echo ucfirst(htmlspecialchars($doc['status'])); ?> <!--status (disponible en gongé .....)-->
                            </p>


                            
                            <?php if (isset($_SESSION['patient_id']) && !empty($_SESSION['patient_id'])): ?>
    <a href="index.php?page=rdv&idMedecin=<?php echo $doc['id']; ?>" 
       class="btn btn-success text-white w-100 rounded-pill mt-2 fw-bold">
       Prendre rendez-vous (Direct)
    </a>
<?php else: ?>
    <a href="index.php?page=inscription&idMedecin=<?php echo $doc['id']; ?>" 
       class="btn btn-info text-white w-100 rounded-pill mt-2 fw-bold">
       Prendre rendez-vous
    </a>
<?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted">Aucun médecin trouvé dans cette catégorie.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
 
  <!-- ══════════════════════════════════
       LOCALISATION
  ══════════════════════════════════ -->
  <section id="localisation" class="bg-white py-5" style="padding-top:70px !important;">
    <div class="container">
      <div class="text-center mb-2">
        <span class="section-badge"><i class="fas fa-map-marker-alt me-1"></i> Où nous trouver</span>
      </div>
      <h2 class="text-center fw-bold text-aqua-dark mb-5" style="font-size:2rem;">Notre localisation</h2>
      <div class="map-wrap ratio ratio-16x9">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d101890.87!2d5.316!3d36.189!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12f31521b1648677%3A0x56e6b55047d4dc7c!2sS%C3%A9tif%2C%20Alg%C3%A9rie!5e0!3m2!1sfr!2sfr!4v1"
          style="border:0;" allowfullscreen="" loading="lazy">
        </iframe>
      </div>
    </div>
  </section>
 
  <script src="<?php echo BASE_URL; ?>js/accueil.js"></script>
</body>
</html>


<?php include ROOT . '/APP/views/layout/footer.php'; ?>
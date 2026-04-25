<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/santepro/public/css/style_Securite.css?v=<?= time(); ?>">
    <title>SANTE PRO - Connexion</title>
</head>

<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">

                <div class="text-center mb-4">
                    <div class="display-6 fw-bold text-dark">
                        <i class="fas fa-heartbeat text-info"></i> SANTE <span class="text-info">PRO</span>
                    </div>
                    <p class="text-muted">Portail de suivi patient</p>
                </div>

                <div class="card border-0 shadow-lg p-4" style="border-radius: 20px;">
                    <div class="card-body">
                        <h3 class="card-title text-center fw-bold mb-3">Bienvenue</h3>
                        <p class="text-center text-secondary small mb-4">Veuillez saisir le code présent sur votre
                            ticket pour accéder à la file d'attente.</p>

                        <form action="index.php?action=valider" method="POST">

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase text-muted">Votre Code
                                    Ticket</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="fas fa-ticket-alt"></i>
                                    </span>
                                    <input type="text" name="code_ticket" class="form-control border-start-0 ps-0"
                                        placeholder="Ex: TK-123" required>
                                </div>
                            </div>

                            <?php if (isset($erreur)): ?>
                                <div class="alert alert-danger d-flex align-items-center p-2 small" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <div><?= htmlspecialchars($erreur) ?></div>
                                </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-info btn-lg text-white fw-bold shadow-sm">
                                    Valider mon accès <i class="fas fa-chevron-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">&copy; 2026 SANTE PRO | Service de santé connecté</small>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
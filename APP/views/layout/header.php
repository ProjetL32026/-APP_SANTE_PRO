<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $pageTitle ?? 'Santé Pro' ?></title>
 
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 
  <?php if (isset($loadChartJS) && $loadChartJS): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <?php endif; ?>

  <?php if (!empty($pageCSS)): ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/<?= $pageCSS ?>?v=<?= time() ?>">
  <?php endif; ?>
</head>

<body class="<?= $bodyClass ?? '' ?>">
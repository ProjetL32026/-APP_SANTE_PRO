<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTÉ PRO</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="/santepro/public/css/stylebaya.css">

    <?php if (isset($pageCSS)): ?>
        <link rel="stylesheet" href="css/<?= $pageCSS ?>">
    <?php endif; ?>
</head>
<body>
    <div class="wrapper d-flex">
        <?php include 'sidebar.php'; ?>
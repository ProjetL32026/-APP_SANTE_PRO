<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santé Pro - Connexion</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            margin: 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 400px;
        }

        .logo-container {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 20px auto;
            color: white; font-size: 1.8rem;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
        }

        .form-control:focus {
            border-color: #0891b2;
            box-shadow: none;
        }

        .input-group-text {
            border: 2px solid #e5e7eb;
            background-color: #f9fafb;
            border-radius: 10px 0 0 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
        }

        .text-cyan { color: #0891b2; }
    </style>
</head>
<body>
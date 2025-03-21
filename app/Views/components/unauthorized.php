<?php require_once __DIR__ . '/../../../app/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès non autorisé - StockSanté</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-width: 600px;
            width: 90%;
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #6c757d;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <i class="fas fa-exclamation-circle error-icon"></i>
        <h1 class="error-title">Accès non autorisé</h1>
        <p class="error-message">
            Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.
            <?php if (isset($message)): ?>
                <br>
                <small><?php echo htmlspecialchars($message); ?></small>
            <?php endif; ?>
        </p>
        <div class="d-grid gap-2">
            <a href="<?= BASE_URL ?>/" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Retour à l'accueil
            </a>
            <a href="<?= BASE_URL ?>/login" class="btn btn-outline-secondary">
                <i class="fas fa-sign-in-alt me-2"></i>Se connecter avec un autre compte
            </a>
        </div>
    </div>
</body>
</html> 
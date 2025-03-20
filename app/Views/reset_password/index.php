<?php
// Assurez-vous que les variables sont définies
$errors = $errors ?? [];
$old = $old ?? [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - COUD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        <?php include __DIR__ . '/../../../public/css/forgot-password.css'; ?>
    </style>

</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="reset-header">
                <h2><i class="fas fa-key me-2"></i>Réinitialisation du mot de passe</h2>
                <p class="mb-0">Entrez votre adresse e-mail et votre matricule pour recevoir un lien de réinitialisation</p>
            </div>
            
            <div class="reset-body">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($errors['auth'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($errors['auth']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/coud_bouletplate/reset-password" novalidate>
                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                            id="email" name="email" placeholder="Email" 
                            value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                        <label for="email">Email</label>
                        <?php if (isset($errors['email'])): ?>
                            <div class="error-text"><?php echo htmlspecialchars($errors['email']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Matricule -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control <?php echo isset($errors['matricule']) ? 'is-invalid' : ''; ?>" 
                            id="matricule" name="matricule" placeholder="Matricule" 
                            value="<?php echo htmlspecialchars($old['matricule'] ?? ''); ?>">
                        <label for="matricule">Matricule</label>
                        <?php if (isset($errors['matricule'])): ?>
                            <div class="error-text"><?php echo htmlspecialchars($errors['matricule']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le lien de réinitialisation
                        </button>
                    </div>
                    
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-6">
                                <a href="<?= BASE_URL ?>/login" class="btn btn-link text-muted p-0">
                                    <i class="fas fa-arrow-left me-1"></i> Retour à la connexion
                                </a>
                            </div>
                            <div class="col-6 text-end">
                                <a href="<?= BASE_URL ?>/register" class="btn btn-link text-muted p-0">
                                    Créer un compte <i class="fas fa-user-plus ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

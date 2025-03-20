<?php
// Assurez-vous que les variables sont définies
$errors = $errors ?? [];
$old = $old ?? [];
$token = $token ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Définir un nouveau mot de passe - COUD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
   <style>
        <?php include __DIR__ . '/../../../public/css/reset.css'; ?>
    </style>    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="reset-header text-white p-4">
                <h2 class="mb-0"><i class="fas fa-key me-2"></i>Définir un nouveau mot de passe</h2>
            </div>
            <div class="reset-body p-4">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($errors['auth'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($errors['auth']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors['token'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($errors['token']); ?>
                        <div class="mt-3">
                            <a href="<?= BASE_URL ?>/reset-password" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-sync me-1"></i> Demander un nouveau lien
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?= BASE_URL ?>/reset-password/reset/<?php echo htmlspecialchars($token); ?>" novalidate>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <!-- Mot de passe -->
                        <div class="form-floating position-relative mb-3">
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                id="password" name="password" placeholder="Nouveau mot de passe">
                            <label for="password">Nouveau mot de passe</label>
                            <span class="password-toggle" onclick="togglePasswordVisibility('password')">
                                <i class="far fa-eye"></i>
                            </span>
                            <?php if (isset($errors['password'])): ?>
                                <div class="error-text"><?php echo htmlspecialchars($errors['password']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Confirmation du mot de passe -->
                        <div class="form-floating position-relative mb-3">
                            <input type="password" class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>" 
                                id="password_confirm" name="password_confirm" placeholder="Confirmer le mot de passe">
                            <label for="password_confirm">Confirmer le mot de passe</label>
                            <span class="password-toggle" onclick="togglePasswordVisibility('password_confirm')">
                                <i class="far fa-eye"></i>
                            </span>
                            <?php if (isset($errors['password_confirm'])): ?>
                                <div class="error-text"><?php echo htmlspecialchars($errors['password_confirm']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer le nouveau mot de passe
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <div class="mt-4 text-center">
                    <a href="<?= BASE_URL ?>/login" class="btn btn-link text-muted">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la connexion
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 
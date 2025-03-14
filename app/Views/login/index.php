<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Connexion' ?> | COUD</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            background-color: #4361ee;
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
            text-align: center;
        }
        .login-body {
            padding: 30px;
        }
        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
        }
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
        .error-feedback {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card">
            <div class="login-header">
                <h3 class="mb-0">
                    <i class="fas fa-user-circle"></i> Connexion
                </h3>
                <p class="mt-2 mb-0">Centre Universitaire - COUD</p>
            </div>
            
            <div class="login-body">
                <!-- Message de succès -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <!-- Message flash de succès/erreur -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['flash_message']['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_message']); ?>
                <?php endif; ?>
                
                <!-- Message d'erreur global -->
                <?php if (isset($errors['auth'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= $errors['auth'] ?>
                    </div>
                <?php endif; ?>
                
                <form action="/coud_bouletplate/login" method="POST" novalidate>
                    <!-- Champ Matricule -->
                    <div class="mb-3">
                        <label for="matricule" class="form-label">Matricule</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control <?= isset($errors['matricule']) ? 'is-invalid' : '' ?>" 
                                id="matricule" 
                                name="matricule" 
                                placeholder="Entrez votre matricule"
                                value="<?= $old['matricule'] ?? '' ?>"
                                required
                            >
                        </div>
                        <?php if (isset($errors['matricule'])): ?>
                            <div class="error-feedback">
                                <?= $errors['matricule'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Champ Mot de passe -->
                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input 
                                type="password" 
                                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                id="password" 
                                name="password" 
                                placeholder="Entrez votre mot de passe"
                                required
                            >
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <div class="error-feedback">
                                <?= $errors['password'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Options et bouton de connexion -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <a href="/coud_bouletplate/reset-password" class="text-decoration-none">Mot de passe oublié?</a>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </button>
                    </div>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p>Vous n'avez pas de compte?</p>
                    <a href="/coud_bouletplate/register" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap & JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour afficher/masquer le mot de passe
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
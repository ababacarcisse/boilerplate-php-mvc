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
    <title>Inscription - COUD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .register-header {
            background: #343a40;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .register-body {
            padding: 2rem;
        }
        .btn-primary {
            background-color: #343a40;
            border-color: #343a40;
        }
        .btn-primary:hover {
            background-color: #23272b;
            border-color: #23272b;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 35px;
            z-index: 10;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .error-text {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h2><i class="fas fa-user-plus me-2"></i>Inscription</h2>
                <p class="mb-0">Créez votre compte COUD pour accéder aux services</p>
            </div>
            
            <div class="register-body">
                <?php if (isset($errors['auth'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($errors['auth']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/coud_bouletplate/register" novalidate>
                    <div class="row">
                        <!-- Matricule -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control <?php echo isset($errors['matricule']) ? 'is-invalid' : ''; ?>" 
                                    id="matricule" name="matricule" placeholder="Matricule" 
                                    value="<?php echo htmlspecialchars($old['matricule'] ?? ''); ?>">
                                <label for="matricule">Matricule</label>
                                <?php if (isset($errors['matricule'])): ?>
                                    <div class="error-text"><?php echo htmlspecialchars($errors['matricule']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                    id="email" name="email" placeholder="Email" 
                                    value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                                <label for="email">Email</label>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="error-text"><?php echo htmlspecialchars($errors['email']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Nom -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : ''; ?>" 
                                    id="nom" name="nom" placeholder="Nom" 
                                    value="<?php echo htmlspecialchars($old['nom'] ?? ''); ?>">
                                <label for="nom">Nom</label>
                                <?php if (isset($errors['nom'])): ?>
                                    <div class="error-text"><?php echo htmlspecialchars($errors['nom']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Prénom -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control <?php echo isset($errors['prenom']) ? 'is-invalid' : ''; ?>" 
                                    id="prenom" name="prenom" placeholder="Prénom" 
                                    value="<?php echo htmlspecialchars($old['prenom'] ?? ''); ?>">
                                <label for="prenom">Prénom</label>
                                <?php if (isset($errors['prenom'])): ?>
                                    <div class="error-text"><?php echo htmlspecialchars($errors['prenom']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date de naissance -->
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control <?php echo isset($errors['date_naissance']) ? 'is-invalid' : ''; ?>" 
                            id="date_naissance" name="date_naissance" 
                            value="<?php echo htmlspecialchars($old['date_naissance'] ?? ''); ?>">
                        <label for="date_naissance">Date de naissance</label>
                        <?php if (isset($errors['date_naissance'])): ?>
                            <div class="error-text"><?php echo htmlspecialchars($errors['date_naissance']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <!-- Mot de passe -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                    id="password" name="password" placeholder="Mot de passe">
                                <label for="password">Mot de passe</label>
                                <span class="password-toggle" onclick="togglePasswordVisibility('password')">
                                    <i class="far fa-eye"></i>
                                </span>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="error-text"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Confirmation du mot de passe -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
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
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>S'inscrire
                        </button>
                    </div>
                    
                    <p class="text-center mt-3">
                        Vous avez déjà un compte? <a href="/coud_bouletplate/login">Connectez-vous</a>
                    </p>
                </form>
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
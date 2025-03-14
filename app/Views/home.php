<?php
// Démarrer la session pour accéder aux variables de session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - PHP MVC Boilerplate</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= Core\Helper::asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= Core\Helper::asset('css/home.css') ?>">
    <style>
        .navbar {
            background-color: #4361ee !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .nav-link:hover {
            color: #f0f0f0 !important;
        }
        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .btn-logout {
            background-color: transparent;
            border: 1px solid white;
            color: white;
        }
        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .dropdown-menu {
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <header>
        <!-- Navbar Bootstrap -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="/coud_bouletplate/">PHP MVC Boilerplate</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="/coud_bouletplate/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Caractéristiques</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://github.com/ababacarcisse/coud-boilerplate">Documentation</a>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle btn-logout" type="button" id="userDropdown" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <?= $_SESSION['user']['nom'] ?? 'Utilisateur' ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="/coud_bouletplate/profile">
                                        <i class="fas fa-user me-2"></i>Mon profil
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="/coud_bouletplate/login/logout" method="POST" class="m-0">
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="/coud_bouletplate/login" class="btn btn-logout">
                                <i class="fas fa-sign-in-alt me-1"></i> Connexion
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="hero">
                <h1>Bienvenue sur votre framework PHP MVC</h1>
                <p>Un framework PHP léger, sécurisé et facile à utiliser pour vos projets web.</p>
                <a href="#features" class="btn">Découvrir</a>
            </div>
        </div>
    </header>

    <section class="features" id="features">
        <div class="container">
            <h2>Caractéristiques</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>Architecture MVC</h3>
                    <p>Structure claire avec séparation des modèles, vues et contrôleurs pour un code organisé.</p>
                </div>
                <div class="feature-card">
                    <h3>Routage flexible</h3>
                    <p>Système de routage puissant pour gérer facilement les URL et les requêtes HTTP.</p>
                </div>
                <div class="feature-card">
                    <h3>ORM simple</h3>
                    <p>Un ORM léger pour interagir avec votre base de données sans SQL complexe.</p>
                </div>
                <div class="feature-card">
                    <h3>Sécurité avancée</h3>
                    <p>Protection CSRF, validation d'entrées et hachage des mots de passe pour une application robuste.</p>
                </div>
                <div class="feature-card">
                    <h3>Outils de développement</h3>
                    <p>Logging, gestion des erreurs et outils CLI pour faciliter le développement et le débogage.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Prêt à démarrer votre projet?</h2>
            <p>Commencez à développer dès maintenant avec notre framework MVC.</p>
            <a href="https://github.com/ababacarcisse/coud-boilerplate" class="btn">Documentation</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> PHP MVC Boilerplate. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
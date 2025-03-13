<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - PHP MVC Boilerplate</title>
    <link rel="stylesheet" href="<?= Core\Helper::asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= Core\Helper::asset('css/home.css') ?>">
</head>
<body>
    <header>
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
</body>
</html> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP MVC Boilerplate</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="/css/home.css">
</head>
<body>
    <header>
        <div class="container hero">
            <h1>PHP MVC Boilerplate</h1>
            <p>Un framework PHP MVC robuste, sécurisé et modulaire pour vos projets d'entreprise</p>
            <a href="/auth/register" class="btn">Commencer maintenant</a>
        </div>
    </header>

    <section class="features">
        <div class="container">
            <h2>Caractéristiques principales</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>Architecture MVC</h3>
                    <p>Structure claire avec séparation entre Modèles, Vues et Contrôleurs pour un code plus maintenable et évolutif.</p>
                </div>
                <div class="feature-card">
                    <h3>Système d'authentification</h3>
                    <p>Gestion complète des utilisateurs avec différents rôles et niveaux d'accès pour sécuriser votre application.</p>
                </div>
                <div class="feature-card">
                    <h3>Base de données sécurisée</h3>
                    <p>Connexion PDO avec requêtes préparées pour une protection efficace contre les injections SQL.</p>
                </div>
                <div class="feature-card">
                    <h3>Gestion d'emails</h3>
                    <p>Envoi d'emails via SMTP avec support de templates HTML pour des communications professionnelles.</p>
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
            <a href="https://github.com/votre-compte/votre-projet" class="btn">Documentation</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> PHP MVC Boilerplate. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html> 
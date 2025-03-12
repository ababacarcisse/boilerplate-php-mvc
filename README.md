# PHP MVC Boilerplate

Un framework PHP MVC léger, sécurisé et facile à utiliser pour vos projets web.

## Caractéristiques

- Architecture MVC claire et bien structurée
- Système de routage flexible et puissant
- ORM simple mais efficace pour interagir avec la base de données
- Gestion des erreurs et logging intégrés
- Sécurité renforcée (protection CSRF, XSS, validation d'entrées)
- Outil CLI pour générer rapidement des composants MVC
- Système d'authentification et de gestion des rôles
- Gestion des emails avec templates

## Installation

### Prérequis

- PHP 7.4 ou supérieur
- Composer
- MySQL ou MariaDB

### Étapes d'installation

1. Clonez ce repository :
   php coud create project -monProjet
   ou tu peux clonerr depuis  github
   ```
   git clone https://github.com/ababacarcisse/coud-boilerplate.git mon-projet
   ```

2. Accédez au dossier du projet :
   ```
   cd mon-projet
   ```

3. Installez les dépendances via Composer :
   ```
   composer install
   ```

4. Configurez votre base de données dans `config/config.php`

5. Assurez-vous que le serveur web a les permissions d'écriture sur les dossiers `logs/` et `app/cache/` (si utilisé)

6. Configurez votre serveur web pour pointer vers le dossier `public/`

### Configuration du serveur web

#### Apache

Assurez-vous que le module `mod_rewrite` est activé, puis créez ou modifiez le fichier `public/.htaccess` :

```
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.*)$ index.php [QSA,L]

Options -Indexes
```

## Utilisation

- Accédez à l'application via votre navigateur à l'adresse http://localhost/mon-projet
- Les routes peuvent être ajoutées dans la classe Router pour diriger les requêtes vers les contrôleurs appropriés.

## Outil CLI Coud

Coud est un outil en ligne de commande qui vous permet de générer rapidement des composants pour votre application.

### Commandes disponibles

#### Générer un composant
```bash
php coud add <type> <nom>
```

**Types disponibles :**
- `m` ou `model` : Génère un modèle avec unee structure de base incluannt la définition de table, clé primaire et champs remplissables
- `c` ou `controller` : Génère un contrôleur avec les méthodes CRUD (index, show, create, store)
- `v` ou `view` : Génère une vue avec une structure HTML de base dans le dossier correspondant

**Exemples :**
- `php coud add model User` - Génère un modèle User dans app/Models/
- `php coud add controller Home` - Génère un contrôleur HomeController dans app/Controllers/
- `php coud add view admin` - Génère une vue index.php dans app/Views/admin/

#### Créer un nouveau projet
```bash
php coud create project <nom>
```
Cette commande clone le repository du boilerplate et initialise un nouveau projet avec la structure MVC complète.

#### Afficher l'aide
```bash
php coud help
```
Affiche toutes les commandes disponibles avec leurs descriptions.

#### Générer une validation
```bash
php coud add validation <nom>
```
Cette commande génère un fichier de validation avec une structure de base dans le dossier `app/validations/`.

**Exemple :**
- `php coud add validation User` - Génère un fichier UserValidator.php dans app/validations/

## Contribuer

Les contributions sont les bienvenues ! Veuillez soumettre une demande de tirage (pull request) pour toute amélioration ou correction.

## License

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

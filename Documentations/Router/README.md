# Documentation du Système de Routage

## Introduction

Le système de routage de COUD permet de mapper facilement les URLs vers les contrôleurs et actions correspondants. Cette documentation explique comment configurer et utiliser le routage dans votre application.

## Principes de base

Le routage dans COUD suit une convention simple :

- Une URL de base (par exemple `/users`) est associée à un contrôleur (par exemple `UsersController`)
- Les segments supplémentaires de l'URL après la base sont utilisés comme nom de méthode dans le contrôleur
- Les segments après le nom de méthode sont passés comme paramètres à cette méthode

## Méthodes de routage

### 1. Routage simple avec `add()`

La méthode `add()` permet d'associer une URL à une fonction anonyme :

```php
$router->add('/about', function() {
    echo 'À propos de nous';
});
```

### 2. Routage de ressources avec `resource()`

La méthode `resource()` permet d'associer automatiquement toutes les routes d'un contrôleur selon une convention :

```php
$router->resource('/users', \App\Controllers\UsersController::class);
```

Cette seule ligne configure les routes suivantes :
- `/users` appelle `UsersController->index()`
- `/users/show/123` appelle `UsersController->show([123])`
- `/users/edit/123` appelle `UsersController->edit([123])`
- etc.

## Exemple complet

Voici comment configurer efficacement les routes dans le fichier `public/index.php` :

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;

$router = new Router();

// Routes simples
$router->add('/', function() {
    $controller = new \App\Controllers\HomeController();
    $controller->index();
});

// Routes de ressources (associe automatiquement toutes les méthodes du contrôleur)
$router->resource('/users', \App\Controllers\UsersController::class);
$router->resource('/products', \App\Controllers\ProductsController::class);
$router->resource('/categories', \App\Controllers\CategoriesController::class);

// Dispatcher la requête
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);
```

## Structure des Contrôleurs

Pour que le routage par convention fonctionne, vos contrôleurs doivent suivre une structure standard :

```php
<?php
namespace App\Controllers;

use Core\Controller;

class UsersController extends Controller
{
    // GET /users
    public function index()
    {
        // Liste tous les utilisateurs
    }
    
    // GET /users/show/123
    public function show($params)
    {
        $id = $params[0] ?? null;
        // Affiche l'utilisateur avec l'ID $id
    }
    
    // GET /users/create
    public function create()
    {
        // Affiche le formulaire de création
    }
    
    // POST /users/store
    public function store()
    {
        // Traite le formulaire de création
    }
    
    // GET /users/edit/123
    public function edit($params)
    {
        $id = $params[0] ?? null;
        // Affiche le formulaire d'édition
    }
    
    // POST /users/update/123
    public function update($params)
    {
        $id = $params[0] ?? null;
        // Traite le formulaire d'édition
    }
    
    // POST /users/delete/123
    public function delete($params)
    {
        $id = $params[0] ?? null;
        // Supprime l'utilisateur
    }
}
```

## Avantages du Routage par Convention

1. **Moins de code** : Une seule ligne suffit pour configurer toutes les routes d'un contrôleur
2. **Maintenabilité** : L'ajout de nouvelles actions ne nécessite pas de modifier la configuration des routes
3. **Cohérence** : Toutes les routes suivent la même structure, facilitant la compréhension du code
4. **Extensibilité** : Facile à étendre avec de nouveaux contrôleurs et actions

## Conseils pratiques

- Utilisez des noms significatifs pour vos contrôleurs et actions
- Respectez la convention de nommage (contrôleurs au pluriel, actions au singulier)
- Les contrôleurs doivent toujours avoir une méthode `index()` pour la route par défaut
- Pour les opérations de création/modification, utilisez les méthodes HTTP appropriées (GET pour afficher les formulaires, POST pour les traiter) 
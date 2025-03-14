# Documentation de l'API COUD

## Table des matières

1. [Introduction](#introduction)
2. [Architecture](#architecture)
3. [Authentification](#authentification)
   - [Aperçu](#aperçu)
   - [Processus d'inscription](#processus-dinscription)
   - [Processus de connexion](#processus-de-connexion)
   - [Rafraîchissement de token](#rafraîchissement-de-token)
   - [Déconnexion](#déconnexion)
   - [Exemples de requêtes](#exemples-de-requêtes)
4. [Utilisation de l'API](#utilisation-de-lapi)
5. [Création d'APIs](#création-dapis)
6. [Sécurité](#sécurité)
7. [Exemple CRUD](#exemple-crud)

## Introduction

Ce framework d'API est conçu pour le Centre Universitaire COUD, permettant une authentification sécurisée et modulaire basée sur JWT. Il est spécialement conçu pour éviter la duplication de code entre le frontend et le backend, tout en offrant une structure robuste pour le développement rapide d'APIs RESTful.

## Architecture

L'architecture de l'API suit le principe MVC (Modèle-Vue-Contrôleur) avec des services pour la logique métier :

- **Modèles** : Définissent la structure des données et les interactions avec la base de données
- **Contrôleurs** : Gèrent les requêtes HTTP et coordonnent les réponses
- **Services** : Contiennent la logique métier et les opérations complexes
- **Middlewares** : Traitent les requêtes avant qu'elles n'atteignent les contrôleurs (authentification, validation, etc.)

## Authentification

### Aperçu

Le système d'authentification du COUD est basé sur les JSON Web Tokens (JWT) et implémente un mécanisme de token de rafraîchissement pour maintenir les sessions utilisateurs de manière sécurisée. Il est conçu pour gérer les étudiants qui sont déjà enregistrés dans la base de données mais qui n'ont pas encore de compte utilisateur actif.

Le système utilise deux tokens :
- **Access Token** : Token à courte durée de vie (1 heure) pour l'authentification des requêtes
- **Refresh Token** : Token à longue durée de vie (30 jours) pour obtenir un nouveau access token sans reconnexion

### Processus d'inscription

L'inscription dans ce système est spécifique aux besoins du COUD et suit les étapes suivantes :

1. **Vérification préalable** : Le système vérifie d'abord si l'étudiant existe dans la base de données `etudiants` en utilisant les informations fournies (matricule, nom, prénom, date de naissance).

2. **Vérification du compte existant** : Le système vérifie ensuite si un compte utilisateur existe déjà avec le matricule fourni.

3. **Gestion des cas spécifiques** :
   - Si l'étudiant n'existe pas dans la base `etudiants` → Message d'erreur
   - Si un compte utilisateur existe déjà avec un mot de passe défini → Message d'erreur (compte déjà activé)
   - Si un compte utilisateur existe avec un mot de passe `null` → Activation du compte avec définition du mot de passe
   - Si aucun compte n'existe → Création d'un nouveau compte utilisateur

4. **Envoi d'email de bienvenue** : Après inscription réussie ou activation de compte, un email de bienvenue est envoyé à l'utilisateur.

5. **Génération de tokens** : Des tokens d'authentification (access + refresh) sont générés et renvoyés à l'utilisateur.

#### Endpoint d'inscription
```
POST /api/auth/register
```

#### Paramètres requis
- `matricule` : Matricule de l'étudiant
- `nom` : Nom de l'étudiant
- `prenom` : Prénom de l'étudiant
- `date_naissance` : Date de naissance au format YYYY-MM-DD
- `email` : Adresse email valide
- `password` : Mot de passe (minimum 8 caractères)

### Processus de connexion

La connexion se fait simplement en vérifiant les identifiants de l'utilisateur dans la base de données.

1. **Vérification des identifiants** : Le système vérifie si le matricule existe et si le mot de passe correspond.

2. **Génération de tokens** : En cas de succès, le système génère un access token et un refresh token.

3. **Notification de connexion** : Un email de notification est envoyé à l'utilisateur pour l'informer d'une nouvelle connexion à son compte.

#### Endpoint de connexion
```
POST /api/auth/login
```

#### Paramètres requis
- `matricule` : Matricule de l'étudiant
- `password` : Mot de passe

### Rafraîchissement de token

Lorsque l'access token expire, l'utilisateur peut utiliser son refresh token pour obtenir un nouveau access token sans avoir à se reconnecter.

1. **Vérification du refresh token** : Le système vérifie si le refresh token est valide et non révoqué.

2. **Génération d'un nouveau access token** : Si le refresh token est valide, un nouveau access token est généré.

#### Endpoint de rafraîchissement
```
POST /api/auth/refresh-token
```

#### Paramètres requis
- `refreshToken` : Refresh token obtenu lors de la connexion

### Déconnexion

La déconnexion consiste à révoquer tous les refresh tokens associés à l'utilisateur.

1. **Vérification de l'authentification** : Le système vérifie si l'utilisateur est authentifié via l'access token.

2. **Révocation des tokens** : Tous les refresh tokens de l'utilisateur sont marqués comme révoqués.

#### Endpoint de déconnexion
```
POST /api/auth/logout
```

#### Paramètres requis
- `Authorization` : En-tête contenant l'access token (`Bearer {token}`)

### Exemples de requêtes

#### Inscription

```http
POST /api/auth/register
Content-Type: application/json

{
  "matricule": "12345",
  "nom": "Dupont",
  "prenom": "Jean",
  "date_naissance": "1990-01-01",
  "email": "jean.dupont@example.com",
  "password": "motdepasse123"
}
```

Réponse de succès (201 Created) :
```json
{
  "success": true,
  "message": "Compte créé avec succès",
  "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "matricule": "12345",
    "fullName": "Jean Dupont",
    "email": "jean.dupont@example.com",
    "role": "etudiant"
  }
}
```

Réponse d'activation de compte existant (201 Created) :
```json
{
  "success": true,
  "message": "Compte activé avec succès",
  "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "matricule": "12345",
    "fullName": "Jean Dupont",
    "email": "jean.dupont@example.com",
    "role": "etudiant"
  }
}
```

Réponse d'erreur (400 Bad Request) :
```json
{
  "success": false,
  "message": "Un compte existe déjà avec ce matricule. Veuillez vous connecter."
}
```

#### Connexion

```http
POST /api/auth/login
Content-Type: application/json

{
  "matricule": "12345",
  "password": "motdepasse123"
}
```

Réponse de succès (200 OK) :
```json
{
  "success": true,
  "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "matricule": "12345",
    "fullName": "Jean Dupont",
    "email": "jean.dupont@example.com",
    "role": "etudiant"
  }
}
```

Réponse d'erreur (401 Unauthorized) :
```json
{
  "success": false,
  "message": "Identifiants invalides"
}
```

#### Rafraîchissement de token

```http
POST /api/auth/refresh-token
Content-Type: application/json

{
  "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

Réponse de succès (200 OK) :
```json
{
  "success": true,
  "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

#### Déconnexion

```http
POST /api/auth/logout
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

Réponse de succès (200 OK) :
```json
{
  "success": true,
  "message": "Déconnexion réussie"
}
```

## Conseils d'implémentation

### Côté frontend

1. **Stockage des tokens**
   - Stockez l'access token dans la mémoire (non dans localStorage pour des raisons de sécurité)
   - Stockez le refresh token dans un cookie HttpOnly si possible

2. **Intercepteurs de requêtes**
   - Créez un intercepteur pour ajouter automatiquement l'access token à l'en-tête Authorization
   - Créez un intercepteur pour traiter les erreurs 401 (Unauthorized) et tenter un rafraîchissement du token

3. **Gestion de la session**
   - Mettez en place un mécanisme de vérification périodique de la validité du token
   - Déconnectez l'utilisateur automatiquement en cas d'inactivité prolongée

### Côté backend

1. **Sécurité des tokens**
   - Assurez-vous que la clé secrète JWT est suffisamment complexe et stockée en toute sécurité
   - Ne stockez pas d'informations sensibles dans les tokens JWT

2. **Validation**
   - Validez toujours les données d'entrée avant traitement
   - Définissez des règles de validation strictes pour les mots de passe

3. **Gestion des erreurs**
   - Utilisez des codes HTTP appropriés pour chaque type d'erreur
   - Fournissez des messages d'erreur clairs mais ne révélez pas d'informations sensibles

## Utilisation de l'API

Pour consommer l'API, suivez ces étapes :

1. Authentifiez-vous pour obtenir un token JWT
2. Incluez le token dans l'en-tête `Authorization` de vos requêtes : `Bearer <votre_token>`
3. Effectuez des appels aux endpoints disponibles selon la documentation spécifique

## Création d'APIs

Pour créer une nouvelle API avec ce framework, suivez ces étapes :

1. **Créez un modèle** dans le dossier `app/models/` :
   ```javascript
   // app/models/Produit.js
   const mongoose = require('mongoose');
   
   const produitSchema = new mongoose.Schema({
     nom: { type: String, required: true },
     description: String,
     prix: { type: Number, required: true },
     stock: { type: Number, default: 0 },
     dateCreation: { type: Date, default: Date.now }
   });
   
   module.exports = mongoose.model('Produit', produitSchema);
   ```

2. **Créez un service** dans le dossier `app/services/` :
   ```javascript
   // app/services/produitService.js
   const Produit = require('../models/Produit');
   
   class ProduitService {
     async getAllProduits() {
       return await Produit.find();
     }
     
     async getProduitById(id) {
       return await Produit.findById(id);
     }
     
     async createProduit(produitData) {
       const produit = new Produit(produitData);
       return await produit.save();
     }
     
     async updateProduit(id, produitData) {
       return await Produit.findByIdAndUpdate(id, produitData, { new: true });
     }
     
     async deleteProduit(id) {
       return await Produit.findByIdAndDelete(id);
     }
   }
   
   module.exports = new ProduitService();
   ```

3. **Créez un contrôleur** dans le dossier `app/controllers/` :
   ```javascript
   // app/controllers/produitController.js
   const produitService = require('../services/produitService');
   
   class ProduitController {
     async getAllProduits(req, res) {
       try {
         const produits = await produitService.getAllProduits();
         return res.status(200).json(produits);
       } catch (error) {
         return res.status(500).json({ message: "Erreur lors de la récupération des produits", error });
       }
     }
     
     async getProduitById(req, res) {
       try {
         const produit = await produitService.getProduitById(req.params.id);
         if (!produit) return res.status(404).json({ message: "Produit non trouvé" });
         return res.status(200).json(produit);
       } catch (error) {
         return res.status(500).json({ message: "Erreur lors de la récupération du produit", error });
       }
     }
     
     async createProduit(req, res) {
       try {
         const produit = await produitService.createProduit(req.body);
         return res.status(201).json(produit);
       } catch (error) {
         return res.status(500).json({ message: "Erreur lors de la création du produit", error });
       }
     }
     
     async updateProduit(req, res) {
       try {
         const produit = await produitService.updateProduit(req.params.id, req.body);
         if (!produit) return res.status(404).json({ message: "Produit non trouvé" });
         return res.status(200).json(produit);
       } catch (error) {
         return res.status(500).json({ message: "Erreur lors de la mise à jour du produit", error });
       }
     }
     
     async deleteProduit(req, res) {
       try {
         const produit = await produitService.deleteProduit(req.params.id);
         if (!produit) return res.status(404).json({ message: "Produit non trouvé" });
         return res.status(200).json({ message: "Produit supprimé avec succès" });
       } catch (error) {
         return res.status(500).json({ message: "Erreur lors de la suppression du produit", error });
       }
     }
   }
   
   module.exports = new ProduitController();
   ```

4. **Définissez les routes** dans le dossier `app/routes/` :
   ```javascript
   // app/routes/produitRoutes.js
   const express = require('express');
   const router = express.Router();
   const produitController = require('../controllers/produitController');
   const authMiddleware = require('../middlewares/authMiddleware');
   
   // Routes publiques
   router.get('/', produitController.getAllProduits);
   router.get('/:id', produitController.getProduitById);
   
   // Routes protégées (nécessitent authentification)
   router.post('/', authMiddleware.verifyToken, produitController.createProduit);
   router.put('/:id', authMiddleware.verifyToken, produitController.updateProduit);
   router.delete('/:id', authMiddleware.verifyToken, produitController.deleteProduit);
   
   module.exports = router;
   ```

5. **Enregistrez vos routes** dans le fichier principal `app.js` ou `server.js` :
   ```javascript
   const produitRoutes = require('./app/routes/produitRoutes');
   app.use('/api/produits', produitRoutes);
   ```

## Sécurité

Le framework intègre plusieurs mesures de sécurité :

- Protection contre les attaques CSRF
- Limitation de débit pour prévenir les attaques par force brute
- Validation des données entrantes
- Sanitisation des données pour prévenir les injections
- En-têtes de sécurité HTTP configurés automatiquement

## Exemple CRUD

Voici un exemple complet d'utilisation de l'API pour effectuer des opérations CRUD sur la ressource "Produit" :

### Récupérer tous les produits
 
 
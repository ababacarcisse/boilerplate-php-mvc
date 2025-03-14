-- Tables nécessaires pour le système d'authentification COUD
-- Script de création des tables pour MySQL/MariaDB

-- Table des étudiants pré-enregistrés (table de référence)
-- Cette table contient les données des étudiants préinscrits
CREATE TABLE IF NOT EXISTS `ETUDIANTS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `matricule` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Numéro matricule unique de l''étudiant',
  `nom` VARCHAR(100) NOT NULL COMMENT 'Nom de famille de l''étudiant',
  `prenom` VARCHAR(100) NOT NULL COMMENT 'Prénom de l''étudiant',
  `date_naissance` DATE NOT NULL COMMENT 'Date de naissance au format YYYY-MM-DD',
  `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email institutionnel de l''étudiant',
  `faculte` VARCHAR(100) DEFAULT NULL COMMENT 'Faculté de l''étudiant',
  `filiere` VARCHAR(100) DEFAULT NULL COMMENT 'Filière d''études',
  `annee_inscription` INT DEFAULT NULL COMMENT 'Année d''inscription académique',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date d''ajout dans le système'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des utilisateurs (comptes activés)
CREATE TABLE IF NOT EXISTS `USERS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `matricule` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Numéro matricule unique de l''étudiant',
  `nom` VARCHAR(100) NOT NULL COMMENT 'Nom de famille de l''utilisateur',
  `prenom` VARCHAR(100) NOT NULL COMMENT 'Prénom de l''utilisateur',
  `date_naissance` DATE NOT NULL COMMENT 'Date de naissance au format YYYY-MM-DD',
  `email` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Adresse email unique',
  `password` VARCHAR(255) DEFAULT NULL COMMENT 'Mot de passe hashé, NULL pour les comptes non activés',
  `role` VARCHAR(20) DEFAULT 'etudiant' COMMENT 'Rôle de l''utilisateur: etudiant, admin, etc.',
  `faculte` VARCHAR(100) DEFAULT NULL COMMENT 'Faculté de l''étudiant',
  `filiere` VARCHAR(100) DEFAULT NULL COMMENT 'Filière d''études',
  `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active' COMMENT 'Statut du compte',
  `email_verified` BOOLEAN DEFAULT 0 COMMENT 'Indique si l''email a été vérifié',
  `last_login` TIMESTAMP NULL COMMENT 'Date de dernière connexion',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création du compte',
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de dernière modification',
  -- Contrainte: le matricule doit exister dans la table ETUDIANTS
  CONSTRAINT `fk_users_etudiants` FOREIGN KEY (`matricule`) REFERENCES `ETUDIANTS`(`matricule`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des tokens de rafraîchissement
CREATE TABLE IF NOT EXISTS `REFRESH_TOKENS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL COMMENT 'ID de l''utilisateur associé au token',
  `token` VARCHAR(255) NOT NULL COMMENT 'Token de rafraîchissement (hash)',
  `selector` VARCHAR(16) NOT NULL COMMENT 'Sélecteur unique pour le token (sécurité supplémentaire)',
  `revoked` BOOLEAN DEFAULT 0 COMMENT 'Indique si le token a été révoqué: 0=actif, 1=révoqué',
  `expires_at` TIMESTAMP NOT NULL COMMENT 'Date d''expiration du token',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création du token',
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'Adresse IP utilisée lors de la création du token',
  `user_agent` TEXT DEFAULT NULL COMMENT 'User-Agent du navigateur utilisé',
  UNIQUE (`selector`),
  FOREIGN KEY (`user_id`) REFERENCES `USERS`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des tentatives de connexion (pour la sécurité)
CREATE TABLE IF NOT EXISTS `LOGIN_ATTEMPTS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `matricule` VARCHAR(20) NOT NULL COMMENT 'Matricule utilisé lors de la tentative',
  `ip_address` VARCHAR(45) NOT NULL COMMENT 'Adresse IP de la tentative',
  `success` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Indique si la tentative a réussi',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de la tentative',
  INDEX `idx_matricule` (`matricule`),
  INDEX `idx_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des tokens de réinitialisation de mot de passe
CREATE TABLE IF NOT EXISTS `PASSWORD_RESET_TOKENS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL COMMENT 'Email de l''utilisateur',
  `token` VARCHAR(255) NOT NULL COMMENT 'Token de réinitialisation (hash)',
  `selector` VARCHAR(16) NOT NULL COMMENT 'Sélecteur unique pour le token',
  `expires_at` TIMESTAMP NOT NULL COMMENT 'Date d''expiration du token',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création du token',
  UNIQUE (`selector`),
  INDEX `idx_email` (`email`),
  INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index recommandés pour optimiser les performances
CREATE INDEX IF NOT EXISTS `idx_etudiants_matricule` ON `ETUDIANTS` (`matricule`);
CREATE INDEX IF NOT EXISTS `idx_etudiants_nom_prenom` ON `ETUDIANTS` (`nom`, `prenom`);
CREATE INDEX IF NOT EXISTS `idx_users_matricule` ON `USERS` (`matricule`);
CREATE INDEX IF NOT EXISTS `idx_users_email` ON `USERS` (`email`);
CREATE INDEX IF NOT EXISTS `idx_users_status_field` ON `USERS` (`status`);
CREATE INDEX IF NOT EXISTS `idx_refresh_tokens_token` ON `REFRESH_TOKENS` (`token`);
CREATE INDEX IF NOT EXISTS `idx_refresh_tokens_selector` ON `REFRESH_TOKENS` (`selector`);
CREATE INDEX IF NOT EXISTS `idx_refresh_tokens_user` ON `REFRESH_TOKENS` (`user_id`);

/*
Description du fonctionnement et des relations entre tables:

1. Processus d'authentification sécurisé:
   
   a) Vérification d'existence:
      - Lors de l'inscription, le système vérifie d'abord si l'étudiant existe dans la table ETUDIANTS
      - Cette vérification se fait par matricule, nom, prénom et date de naissance pour assurer l'exactitude
   
   b) Vérification de compte existant:
      - Si l'étudiant existe dans ETUDIANTS, le système vérifie s'il a déjà un compte dans USERS
      - Si un compte existe avec password=NULL, c'est un compte non activé qui peut être activé
      - Si un compte existe avec password défini, l'étudiant a déjà un compte actif
   
   c) Création ou activation de compte:
      - Si aucun compte n'existe: création d'un nouveau compte dans USERS
      - Si un compte non activé existe: activation du compte existant
   
   d) Sécurité des tokens:
      - Les refresh tokens utilisent un système de selector/validator pour prévenir les attaques de timing
      - Les tokens sont stockés sous forme de hash dans la base de données
      - Les informations de contexte (IP, User-Agent) sont conservées pour détecter les usages suspects
   
   e) Protection contre les attaques:
      - La table LOGIN_ATTEMPTS permet de limiter les tentatives de connexion par IP ou matricule
      - Les mots de passe sont stockés avec un algorithme de hachage sécurisé (Argon2id ou Bcrypt)

2. Contraintes d'intégrité:
   - La contrainte Foreign Key entre USERS.matricule et ETUDIANTS.matricule assure qu'un utilisateur
     ne peut être créé que s'il existe dans la table des étudiants pré-enregistrés
   - Cette contrainte sécurise le processus d'inscription en empêchant la création de comptes fictifs

3. Processus de sécurité supplémentaires:
   - Système de réinitialisation de mot de passe sécurisé avec tokens à usage unique
   - Statut des comptes (actif, inactif, suspendu) pour gérer les accès
   - Validation d'email pour confirmer l'identité de l'utilisateur
   - Suivi des dernières connexions pour des raisons de sécurité et d'audit
*/

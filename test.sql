-- phpMyAdmin SQL Dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `user_types` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des types de base
INSERT IGNORE INTO user_types (type_code, description) VALUES
('pharmacie', 'Utilisateur de la pharmacie'),
('magasin', 'Utilisateur du magasin');

-- Table des utilisateurs (agents)
CREATE TABLE IF NOT EXISTS `USERS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `matricule` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Numéro matricule unique de l''agent',
  `nom` VARCHAR(100) NOT NULL COMMENT 'Nom de famille de l''agent',
  `prenom` VARCHAR(100) NOT NULL COMMENT 'Prénom de l''agent',
  `email` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Adresse email unique',
  `password` VARCHAR(255) NOT NULL COMMENT 'Mot de passe hashé',
  `role` ENUM('admin','assistant') NOT NULL DEFAULT 'assistant' COMMENT 'Rôle de l''agent',
  `type` VARCHAR(50) DEFAULT NULL COMMENT 'Type d''agent (pharmacie ou magasin)',
  `status` ENUM('active','inactive','suspended') DEFAULT 'active' COMMENT 'Statut du compte',
  `last_login` TIMESTAMP NULL COMMENT 'Date de dernière connexion',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création du compte',
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de dernière modification',
  CONSTRAINT `fk_user_type` FOREIGN KEY (`type`) REFERENCES `user_types`(`type_code`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `permission_code` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des permissions de base
INSERT IGNORE INTO permissions (permission_code, description) VALUES
('dashboard', 'Accès au tableau de bord'),
('entries', 'Gestion des entrées de stock'),
('outputs', 'Gestion des sorties de stock'),
('sales', 'Gestion des ventes'),
('stats', 'Accès aux statistiques'),
('users', 'Gestion des utilisateurs');

-- Table des permissions utilisateur
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `user_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `permission_id`),
  FOREIGN KEY (`user_id`) REFERENCES `USERS`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des tokens de rafraîchissement
CREATE TABLE IF NOT EXISTS `REFRESH_TOKENS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL COMMENT 'ID de l''utilisateur associé au token',
  `token` VARCHAR(255) NOT NULL COMMENT 'Token de rafraîchissement (hash)',
  `selector` VARCHAR(16) NOT NULL COMMENT 'Sélecteur unique pour le token',
  `revoked` TINYINT(1) DEFAULT 0 COMMENT 'Indique si le token a été révoqué',
  `expires_at` TIMESTAMP NOT NULL COMMENT 'Date d''expiration du token',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création du token',
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'Adresse IP utilisée',
  `user_agent` TEXT DEFAULT NULL COMMENT 'User-Agent du navigateur',
  UNIQUE (`selector`),
  FOREIGN KEY (`user_id`) REFERENCES `USERS`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des tentatives de connexion
CREATE TABLE IF NOT EXISTS `LOGIN_ATTEMPTS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `matricule` VARCHAR(20) NOT NULL COMMENT 'Matricule utilisé',
  `ip_address` VARCHAR(45) NOT NULL COMMENT 'Adresse IP',
  `success` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Succès de la tentative',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de la tentative',
  INDEX `idx_matricule` (`matricule`),
  INDEX `idx_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des tokens de réinitialisation
CREATE TABLE IF NOT EXISTS `PASSWORD_RESET_TOKENS` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL COMMENT 'Email de l''utilisateur',
  `token` VARCHAR(255) NOT NULL COMMENT 'Token de réinitialisation',
  `selector` VARCHAR(16) NOT NULL COMMENT 'Sélecteur unique',
  `expires_at` TIMESTAMP NOT NULL COMMENT 'Date d''expiration',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  UNIQUE (`selector`),
  INDEX `idx_email` (`email`),
  INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Procédure pour assigner les permissions d'admin
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS assign_admin_permissions(IN p_user_id INT)
BEGIN
    INSERT IGNORE INTO user_permissions (user_id, permission_id)
    SELECT p_user_id, id FROM permissions;
END //
DELIMITER ;

-- Procédure pour assigner les permissions d'assistant
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS assign_assistant_permissions(IN p_user_id INT)
BEGIN
    INSERT IGNORE INTO user_permissions (user_id, permission_id)
    SELECT p_user_id, id FROM permissions WHERE id <= 4;
END //
DELIMITER ;

-- Trigger pour assigner automatiquement les permissions après l'insertion d'un utilisateur
DELIMITER //
CREATE TRIGGER after_user_insert
AFTER INSERT ON USERS
FOR EACH ROW
BEGIN
    IF NEW.role = 'admin' THEN
        CALL assign_admin_permissions(NEW.id);
    ELSE
        CALL assign_assistant_permissions(NEW.id);
    END IF;
END //
DELIMITER ; 
-- Création de la table des types d'utilisateurs
CREATE TABLE IF NOT EXISTS user_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertion des types de base
INSERT INTO user_types (type_code, description) VALUES
('pharmacie', 'Utilisateur de la pharmacie'),
('magasin', 'Utilisateur du magasin');

-- Création de la table des permissions
CREATE TABLE IF NOT EXISTS permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    permission_code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertion des permissions de base
INSERT INTO permissions (permission_code, description) VALUES
('dashboard', 'Accès au tableau de bord'),
('entries', 'Gestion des entrées de stock'),
('outputs', 'Gestion des sorties de stock'),
('sales', 'Gestion des ventes'),
('stats', 'Accès aux statistiques'),
('users', 'Gestion des utilisateurs');

-- Création de la table de liaison user_permissions
CREATE TABLE IF NOT EXISTS user_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_permission (user_id, permission_id)
) ENGINE=InnoDB;

-- Ajout de la colonne type à la table users
ALTER TABLE users 
ADD COLUMN type VARCHAR(50) DEFAULT 'magasin' AFTER role,
ADD CONSTRAINT fk_user_type FOREIGN KEY (type) REFERENCES user_types(type_code);

-- Procédure pour assigner les permissions d'admin
DELIMITER //
CREATE PROCEDURE assign_admin_permissions(IN p_user_id INT)
BEGIN
    INSERT INTO user_permissions (user_id, permission_id)
    SELECT p_user_id, id FROM permissions;
END //
DELIMITER ;

-- Procédure pour assigner les permissions d'assistant
DELIMITER //
CREATE PROCEDURE assign_assistant_permissions(IN p_user_id INT)
BEGIN
    INSERT INTO user_permissions (user_id, permission_id)
    SELECT p_user_id, id FROM permissions 
    WHERE permission_code IN ('dashboard', 'entries', 'outputs', 'stats');
END //
DELIMITER ;

-- Trigger pour assigner automatiquement les permissions après l'insertion d'un utilisateur
DELIMITER //
CREATE TRIGGER after_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.role = 'admin' THEN
        CALL assign_admin_permissions(NEW.id);
    ELSE
        CALL assign_assistant_permissions(NEW.id);
    END IF;
END //
DELIMITER ;

-- Mise à jour des utilisateurs existants
UPDATE users SET type = 'pharmacie' WHERE role = 'admin';
UPDATE users SET type = 'magasin' WHERE role = 'assistant';

-- Assigner les permissions aux utilisateurs existants
INSERT INTO user_permissions (user_id, permission_id)
SELECT u.id, p.id
FROM users u
CROSS JOIN permissions p
WHERE u.role = 'admin';

INSERT INTO user_permissions (user_id, permission_id)
SELECT u.id, p.id
FROM users u
CROSS JOIN permissions p
WHERE u.role = 'assistant'
AND p.permission_code IN ('dashboard', 'entries', 'outputs', 'stats'); 
<?php

namespace App\Api\Models;

use PDO;
use App\Lib\EnvLoader;

/**
 * Classe de passerelle entre l'API et le modèle du frontend
 * Évite la duplication du code en réutilisant le modèle existant
 */
class User 
{
    /**
     * Récupère l'instance du modèle frontend
     */
    private static function getFrontendModel(): \App\Models\User 
    {
        return new \App\Models\User();
    }
    
    /**
     * Convertit un objet modèle frontend en tableau pour l'API
     */
    private static function toArray($frontendUser): array 
    {
        if (!$frontendUser) return [];
        
        // Conversion en tableau en excluant les propriétés sensibles
        return [
            'id' => $frontendUser->id,
            'matricule' => $frontendUser->matricule,
            'fullName' => $frontendUser->fullName,
            'email' => $frontendUser->email,
            'role' => $frontendUser->role,
            'faculte' => $frontendUser->faculte,
            'filiere' => $frontendUser->filiere
        ];
    }
    
    /**
     * Vérifie les identifiants d'un utilisateur
     */
    public static function verifyCredentials($matricule, $password)
    {
        // Récupérer l'utilisateur par matricule
        $user = self::findByMatricule($matricule);
        
        if (!$user) {
            return false;
        }
        
        // Vérifier si le mot de passe est valide
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Trouve un utilisateur par son matricule
     */
    public static function findByMatricule($matricule)
    {
        $db = self::getDb();
        
        $stmt = $db->prepare("SELECT * FROM USERS WHERE matricule = :matricule");
        $stmt->execute(['matricule' => $matricule]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Formater le nom complet
            $user['fullName'] = $user['nom'] . ' ' . $user['prenom'];
        }
        
        return $user ?: false;
    }
    
    /**
     * Trouve un utilisateur par son ID
     */
    public static function findById($id)
    {
        $db = self::getDb();
        
        $stmt = $db->prepare("SELECT * FROM USERS WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Formater le nom complet
            $user['fullName'] = $user['nom'] . ' ' . $user['prenom'];
        }
        
        return $user ?: false;
    }
    
    /**
     * Trouve un utilisateur par son email
     */
    public static function findByEmail($email)
    {
        $db = self::getDb();
        
        $stmt = $db->prepare("SELECT * FROM USERS WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Formater le nom complet
            $user['fullName'] = $user['nom'] . ' ' . $user['prenom'];
        }
        
        return $user ?: false;
    }
    
    /**
     * Vérifie si un étudiant existe dans la base de données
     */
    public static function checkIfStudentExists($matricule, $nom, $prenom, $date_naissance)
    {
        $db = self::getDb();
        
        $stmt = $db->prepare("
            SELECT * FROM USERS 
            WHERE matricule = :matricule 
            AND nom = :nom 
            AND prenom = :prenom 
            AND date_naissance = :date_naissance
        ");
        
        $stmt->execute([
            'matricule' => $matricule,
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => $date_naissance
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public static function create($userData)
    {
        try {
            $db = self::getDb();
            
            // Journaliser les données reçues (sans le mot de passe)
            $logData = $userData;
            unset($logData['password']);
            error_log('Tentative de création d\'utilisateur avec les données : ' . json_encode($logData));
            
            // Vérifier si l'utilisateur existe déjà dans la table users
            $existingUser = self::findByMatricule($userData['matricule']);
            if ($existingUser) {
                error_log('Utilisateur existant trouvé avec le matricule : ' . $userData['matricule']);
                return [
                    'success' => false,
                    'message' => 'Un compte existe déjà avec ce matricule'
                ];
            }
            
            // Hasher le mot de passe
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Insérer le nouvel utilisateur
            $stmt = $db->prepare("
                INSERT INTO USERS (
                    matricule, nom, prenom, email, password, role, type, status, created_at
                ) VALUES (
                    :matricule, :nom, :prenom, :email, :password, :role, :type, 'active', NOW()
                )
            ");
            
            $params = [
                'matricule' => $userData['matricule'],
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'email' => $userData['email'],
                'password' => $hashedPassword,
                'role' => $userData['role'] ?? 'assistant',
                'type' => $userData['type'] ?? 'magasin'
            ];
            
            error_log('Exécution de la requête d\'insertion avec les paramètres : ' . json_encode($params));
            
            $result = $stmt->execute($params);
            
            if (!$result) {
                error_log('Échec de l\'insertion : ' . json_encode($stmt->errorInfo()));
                return false;
            }
            
            // Récupérer l'ID de l'utilisateur nouvellement créé
            $userId = $db->lastInsertId();
            error_log('Nouvel utilisateur créé avec l\'ID : ' . $userId);
            
            // Assigner les permissions en fonction du rôle
            if (($userData['role'] ?? 'assistant') === 'admin') {
                error_log('Attribution des permissions admin pour l\'utilisateur ' . $userId);
                // Assigner toutes les permissions pour un admin
                $stmt = $db->prepare("
                    INSERT INTO user_permissions (user_id, permission_id)
                    SELECT :user_id, id FROM permissions
                ");
                $stmt->execute(['user_id' => $userId]);
            } else {
                error_log('Attribution des permissions assistant pour l\'utilisateur ' . $userId);
                // Assigner les permissions de base pour un assistant
                $stmt = $db->prepare("
                    INSERT INTO user_permissions (user_id, permission_id)
                    SELECT :user_id, id FROM permissions WHERE id <= 4
                ");
                $stmt->execute(['user_id' => $userId]);
            }
            
            // Récupérer l'utilisateur nouvellement créé
            $newUser = self::findByMatricule($userData['matricule']);
            error_log('Utilisateur créé avec succès : ' . json_encode($newUser));
            return $newUser;
            
        } catch (\PDOException $e) {
            error_log('Erreur PDO lors de la création de l\'utilisateur : ' . $e->getMessage());
            error_log('Trace : ' . $e->getTraceAsString());
            return false;
        } catch (\Exception $e) {
            error_log('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
            error_log('Trace : ' . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Met à jour un utilisateur existant (pour les comptes avec mot de passe null)
     */
    public static function updateExistingUser($id, $userData)
    {
        $db = self::getDb();
        
        // Hasher le mot de passe
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Mettre à jour l'utilisateur
        $stmt = $db->prepare("
            UPDATE USERS SET 
            email = :email,
            password = :password,
            updated_at = NOW()
            WHERE id = :id
        ");
        
        $result = $stmt->execute([
            'email' => $userData['email'],
            'password' => $hashedPassword,
            'id' => $id
        ]);
        
        if (!$result) {
            return false;
        }
        
        // Récupérer l'utilisateur mis à jour
        return self::findById($id);
    }
    
    /**
     * Sauvegarde un token de rafraîchissement pour un utilisateur
     */
    public static function saveRefreshToken($userId, $refreshToken)
    {
        $db = self::getDb();
        
        $stmt = $db->prepare("
            INSERT INTO REFRESH_TOKENS (user_id, token, expires_at, created_at)
            VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 30 DAY), NOW())
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'token' => $refreshToken
        ]);
    }
    
    /**
     * Vérifie si un token de rafraîchissement est valide
     */
    public static function verifyRefreshToken($userId, $refreshToken)
    {
        $db = self::getDb();
        
        $stmt = $db->prepare("
            SELECT * FROM REFRESH_TOKENS
            WHERE user_id = :user_id
            AND token = :token
            AND expires_at > NOW()
            AND revoked = 0
        ");
        
        $stmt->execute([
            'user_id' => $userId,
            'token' => $refreshToken
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
    
    /**
     * Invalide tous les tokens de rafraîchissement d'un utilisateur
     */
    public static function invalidateAllRefreshTokens($userId)
    {
        $db = self::getDb();
        
        $stmt = $db->prepare("
            UPDATE REFRESH_TOKENS
            SET revoked = 1
            WHERE user_id = :user_id
        ");
        
        return $stmt->execute(['user_id' => $userId]);
    }
    
    /**
     * Obtient une connexion à la base de données
     */
    private static function getDb()
    {
        // Charger la configuration
        $config = require dirname(dirname(dirname(__FILE__))) . '/config/config.php';
        $dbConfig = $config['db'];
        
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            return new PDO($dsn, $dbConfig['user'], $dbConfig['password'], $options);
        } catch (\PDOException $e) {
            throw new \Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
}

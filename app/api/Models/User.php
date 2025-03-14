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
        $db = self::getDb();
        
        // Vérifier si l'utilisateur existe déjà dans la table users
        $existingUser = self::findByMatricule($userData['matricule']);
        if ($existingUser) {
            // Si le mot de passe est null, mettre à jour le compte existant
            if (empty($existingUser['password']) || $existingUser['password'] === null) {
                return self::updateExistingUser($existingUser['id'], $userData);
            }
            return false; // L'utilisateur a déjà un compte actif
        }
        
        // Hasher le mot de passe
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Insérer le nouvel utilisateur
        $stmt = $db->prepare("
            INSERT INTO USERS (
                matricule, nom, prenom, date_naissance, email, password, role, created_at
            ) VALUES (
                :matricule, :nom, :prenom, :date_naissance, :email, :password, :role, NOW()
            )
        ");
        
        $result = $stmt->execute([
            'matricule' => $userData['matricule'],
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'date_naissance' => $userData['date_naissance'],
            'email' => $userData['email'],
            'password' => $hashedPassword,
            'role' => $userData['role'] ?? 'etudiant'
        ]);
        
        if (!$result) {
            return false;
        }
        
        // Récupérer l'utilisateur nouvellement créé
        return self::findByMatricule($userData['matricule']);
    }
    
    /**
     * Met à jour un utilisateur existant (pour les comptes avec mot de passe null)
     */
    private static function updateExistingUser($id, $userData)
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

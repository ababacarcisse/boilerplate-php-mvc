<?php

namespace App\Api\Services;

use App\Api\Models\User;
use App\Lib\JWT;
use App\Lib\EmailService;
use PDO;

/**
 * Service d'authentification
 * 
 * Gère les opérations d'authentification et de gestion des utilisateurs
 * - Vérification d'existence d'étudiant
 * - Inscription d'utilisateur (création ou activation)
 * - Connexion et génération de tokens
 * - Rafraîchissement de tokens
 * - Déconnexion et révocation de tokens
 */
class AuthService
{
    private $emailService;
    private $db;

    /**
     * Initialise le service d'authentification
     */
    public function __construct()
    {
        try {
            // Charger la configuration
            $config = require dirname(dirname(dirname(__DIR__))) . '/app/config/config.php';
            
            // Connexion à la base de données
            $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->db = new PDO($dsn, $config['db']['user'], $config['db']['password'], $options);
            
            // Initialiser le service d'email
            $this->emailService = new EmailService();
            
        } catch (\PDOException $e) {
            throw new \Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception("Erreur d'initialisation du service d'authentification: " . $e->getMessage());
        }
    }

    /**
     * Authentifie un utilisateur
     * 
     * @param string $matricule Matricule de l'étudiant
     * @param string $password Mot de passe
     * @return array Résultat de l'authentification avec tokens si succès
     */
    public function login($matricule, $password)
    {
        // Enregistrer la tentative de connexion (pour sécurité)
        $this->logLoginAttempt($matricule, false);
        
        // Vérifier les identifiants
        $user = User::verifyCredentials($matricule, $password);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Identifiants invalides'
            ];
        }
        
        // Vérifier si le compte est actif
        if (isset($user['status']) && $user['status'] !== 'active') {
            return [
                'success' => false,
                'message' => 'Ce compte est ' . ($user['status'] === 'suspended' ? 'suspendu' : 'inactif') . '. Veuillez contacter l\'administration.'
            ];
        }
        
        // Mettre à jour la date de dernière connexion
        $this->updateLastLogin($user['id']);
        
        // Enregistrer la tentative de connexion réussie
        $this->logLoginAttempt($matricule, true);
        
        // Générer les tokens
        $accessToken = JWT::generate($user);
        $refreshToken = $this->generateSecureRefreshToken($user);
        
        // Envoyer une notification de connexion
        $this->emailService->sendLoginNotification($user);
        
        return [
            'success' => true,
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'user' => [
                'id' => $user['id'],
                'matricule' => $user['matricule'],
                'fullName' => $user['fullName'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }

    /**
     * Inscrit un nouvel utilisateur ou active un compte existant
     * 
     * @param array $userData Données utilisateur
     * @return array Résultat de l'inscription
     */
    public function register($userData)
    {
        // Vérifier si l'étudiant existe dans la base de données ETUDIANTS
        $exists = $this->checkIfStudentExists(
            $userData['matricule'],
            $userData['nom'],
            $userData['prenom'],
            $userData['date_naissance']
        );
        
        if (!$exists) {
            return [
                'success' => false,
                'message' => 'Cet étudiant n\'est pas reconnu dans notre système. Veuillez contacter l\'administration.'
            ];
        }
        
        // Vérifier si l'utilisateur existe déjà avec ce matricule
        $existingUser = User::findByMatricule($userData['matricule']);
        
        if ($existingUser) {
            // Si l'utilisateur existe mais n'a pas encore défini de mot de passe
            if (empty($existingUser['password'])) {
                // Mettre à jour l'utilisateur existant avec le nouveau mot de passe
                $user = $this->activateExistingUser($existingUser['id'], $userData);
                
                if (!$user) {
                    return [
                        'success' => false,
                        'message' => 'Erreur lors de la mise à jour du compte'
                    ];
                }
                
                // Générer les tokens
                $accessToken = JWT::generate($user);
                $refreshToken = $this->generateSecureRefreshToken($user);
                
                // Envoyer un email de bienvenue
                $this->emailService->sendWelcomeEmail($user);
                
                return [
                    'success' => true,
                    'message' => 'Compte activé avec succès',
                    'accessToken' => $accessToken,
                    'refreshToken' => $refreshToken,
                    'user' => [
                        'id' => $user['id'],
                        'matricule' => $user['matricule'],
                        'fullName' => $user['fullName'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ];
            } else {
                // L'utilisateur existe déjà avec un mot de passe défini
                return [
                    'success' => false,
                    'message' => 'Un compte existe déjà avec ce matricule. Veuillez vous connecter.'
                ];
            }
        }
        
        // Valider le mot de passe
        if (strlen($userData['password']) < 8) {
            return [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 8 caractères'
            ];
        }
        
        // Créer le nouvel utilisateur
        $user = $this->createNewUser($userData);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la création du compte'
            ];
        }
        
        // Envoyer un email de bienvenue
        $this->emailService->sendWelcomeEmail($user);
        
        // Générer les tokens
        $accessToken = JWT::generate($user);
        $refreshToken = $this->generateSecureRefreshToken($user);
        
        return [
            'success' => true,
            'message' => 'Compte créé avec succès',
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'user' => [
                'id' => $user['id'],
                'matricule' => $user['matricule'],
                'fullName' => $user['fullName'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }

    /**
     * Rafraîchit les tokens d'authentification
     * 
     * @param string $refreshToken Token de rafraîchissement
     * @return array Résultat avec nouveau accessToken si succès
     */
    public function refreshToken($refreshToken)
    {
        // Extraire le selector et le validator du token
        list($selector, $validator) = $this->parseRefreshToken($refreshToken);
        
        if (!$selector || !$validator) {
            return [
                'success' => false,
                'message' => 'Format de token de rafraîchissement invalide'
            ];
        }
        
        // Récupérer le token correspondant au selector
        $tokenData = $this->getRefreshTokenBySelector($selector);
        
        if (!$tokenData || $tokenData['revoked'] == 1 || strtotime($tokenData['expires_at']) < time()) {
            return [
                'success' => false,
                'message' => 'Token de rafraîchissement invalide ou expiré'
            ];
        }
        
        // Vérifier le validator (comparaison sécurisée)
        if (!hash_equals($tokenData['token'], hash('sha256', $validator))) {
            // Potentielle tentative de falsification, révoquer le token pour sécurité
            $this->revokeRefreshToken($tokenData['id']);
            
            return [
                'success' => false,
                'message' => 'Token de rafraîchissement invalide'
            ];
        }
        
        // Récupérer l'utilisateur
        $user = User::findById($tokenData['user_id']);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ];
        }
        
        // Vérifier si le compte est actif
        if (isset($user['status']) && $user['status'] !== 'active') {
            return [
                'success' => false,
                'message' => 'Ce compte est ' . ($user['status'] === 'suspended' ? 'suspendu' : 'inactif')
            ];
        }
        
        // Générer un nouveau access token
        $accessToken = JWT::generate($user);
        
        // Rotation des tokens (optionnelle mais recommandée pour plus de sécurité)
        // Cela invalidera l'ancien refresh token et en générera un nouveau
        $newRefreshToken = $this->rotateRefreshToken($tokenData['id'], $user);
        
        return [
            'success' => true,
            'accessToken' => $accessToken,
            'refreshToken' => $newRefreshToken
        ];
    }

    /**
     * Déconnecte un utilisateur en révoquant tous ses refresh tokens
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Résultat de la déconnexion
     */
    public function logout($userId)
    {
        // Invalider tous les tokens de rafraîchissement
        User::invalidateAllRefreshTokens($userId);
        
        return [
            'success' => true,
            'message' => 'Déconnexion réussie'
        ];
    }

    /**
     * Vérifie si un étudiant existe dans la table ETUDIANTS
     * 
     * @param string $matricule Matricule de l'étudiant
     * @param string $nom Nom de l'étudiant
     * @param string $prenom Prénom de l'étudiant
     * @param string $date_naissance Date de naissance au format YYYY-MM-DD
     * @return bool True si l'étudiant existe, false sinon
     */
    private function checkIfStudentExists($matricule, $nom, $prenom, $date_naissance)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM ETUDIANTS 
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
        
        return $stmt->fetch() ? true : false;
    }

    /**
     * Crée un nouvel utilisateur
     * 
     * @param array $userData Données utilisateur
     * @return array|bool Données utilisateur créé ou false si échec
     */
    private function createNewUser($userData)
    {
        // Hasher le mot de passe
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Insérer le nouvel utilisateur
        $stmt = $this->db->prepare("
            INSERT INTO USERS (
                matricule, nom, prenom, date_naissance, email, password, role, status, email_verified, created_at
            ) VALUES (
                :matricule, :nom, :prenom, :date_naissance, :email, :password, :role, 'active', :email_verified, NOW()
            )
        ");
        
        $result = $stmt->execute([
            'matricule' => $userData['matricule'],
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'date_naissance' => $userData['date_naissance'],
            'email' => $userData['email'],
            'password' => $hashedPassword,
            'role' => $userData['role'] ?? 'etudiant',
            'email_verified' => 0
        ]);
        
        if (!$result) {
            return false;
        }
        
        // Récupérer l'utilisateur nouvellement créé
        return User::findByMatricule($userData['matricule']);
    }

    /**
     * Active un compte utilisateur existant
     * 
     * @param int $id ID de l'utilisateur
     * @param array $userData Données utilisateur
     * @return array|bool Données utilisateur mis à jour ou false si échec
     */
    private function activateExistingUser($id, $userData)
    {
        // Hasher le mot de passe
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Mettre à jour l'utilisateur
        $stmt = $this->db->prepare("
            UPDATE USERS SET 
            email = :email,
            password = :password,
            status = 'active',
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
        return User::findById($id);
    }

    /**
     * Génère un refresh token sécurisé
     * 
     * @param array $user Données utilisateur
     * @return string Refresh token au format selector.validator
     */
    private function generateSecureRefreshToken($user)
    {
        // Générer un selector aléatoire (pour rechercher le token)
        $selector = bin2hex(random_bytes(8));
        
        // Générer un validator aléatoire (secret pour valider le token)
        $validator = bin2hex(random_bytes(32));
        
        // Hasher le validator pour le stockage
        $hashedValidator = hash('sha256', $validator);
        
        // Date d'expiration (30 jours)
        $expiresAt = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60);
        
        // Obtenir l'adresse IP et le User-Agent
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        // Enregistrer le token dans la base de données
        $stmt = $this->db->prepare("
            INSERT INTO REFRESH_TOKENS (user_id, selector, token, expires_at, ip_address, user_agent, created_at)
            VALUES (:user_id, :selector, :token, :expires_at, :ip_address, :user_agent, NOW())
        ");
        
        $stmt->execute([
            'user_id' => $user['id'],
            'selector' => $selector,
            'token' => $hashedValidator,
            'expires_at' => $expiresAt,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
        
        // Retourner le token au format selector.validator
        return $selector . '.' . $validator;
    }

    /**
     * Parse un refresh token pour extraire le selector et le validator
     * 
     * @param string $refreshToken Token au format selector.validator
     * @return array [selector, validator]
     */
    private function parseRefreshToken($refreshToken)
    {
        $parts = explode('.', $refreshToken);
        
        if (count($parts) !== 2) {
            return [null, null];
        }
        
        return [$parts[0], $parts[1]];
    }

    /**
     * Récupère un refresh token par son selector
     * 
     * @param string $selector Selector du token
     * @return array|bool Données du token ou false si non trouvé
     */
    private function getRefreshTokenBySelector($selector)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM REFRESH_TOKENS
            WHERE selector = :selector
        ");
        
        $stmt->execute(['selector' => $selector]);
        
        return $stmt->fetch();
    }

    /**
     * Révoque un refresh token
     * 
     * @param int $tokenId ID du token
     * @return bool True si succès, false sinon
     */
    private function revokeRefreshToken($tokenId)
    {
        $stmt = $this->db->prepare("
            UPDATE REFRESH_TOKENS
            SET revoked = 1
            WHERE id = :id
        ");
        
        return $stmt->execute(['id' => $tokenId]);
    }

    /**
     * Effectue une rotation de refresh token (révoque l'ancien et en crée un nouveau)
     * 
     * @param int $tokenId ID du token à révoquer
     * @param array $user Données utilisateur
     * @return string Nouveau refresh token
     */
    private function rotateRefreshToken($tokenId, $user)
    {
        // Révoquer l'ancien token
        $this->revokeRefreshToken($tokenId);
        
        // Générer un nouveau token
        return $this->generateSecureRefreshToken($user);
    }

    /**
     * Met à jour la date de dernière connexion d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return bool True si succès, false sinon
     */
    private function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE USERS
            SET last_login = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute(['id' => $userId]);
    }

    /**
     * Enregistre une tentative de connexion
     * 
     * @param string $matricule Matricule utilisé
     * @param bool $success Succès de la tentative
     * @return bool True si succès, false sinon
     */
    private function logLoginAttempt($matricule, $success)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt = $this->db->prepare("
            INSERT INTO LOGIN_ATTEMPTS (matricule, ip_address, success, created_at)
            VALUES (:matricule, :ip_address, :success, NOW())
        ");
        
        return $stmt->execute([
            'matricule' => $matricule,
            'ip_address' => $ipAddress,
            'success' => $success ? 1 : 0
        ]);
    }

    /**
     * Vérifie s'il y a eu trop de tentatives de connexion échouées
     * 
     * @param string $matricule Matricule ou null pour vérifier uniquement par IP
     * @param int $maxAttempts Nombre maximum de tentatives autorisées
     * @param int $timeWindow Fenêtre de temps en minutes
     * @return bool True si trop de tentatives, false sinon
     */
    public function tooManyLoginAttempts($matricule = null, $maxAttempts = 5, $timeWindow = 15)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $sql = "
            SELECT COUNT(*) as attempt_count
            FROM LOGIN_ATTEMPTS
            WHERE success = 0
            AND created_at > DATE_SUB(NOW(), INTERVAL :timeWindow MINUTE)
            AND ip_address = :ip_address
        ";
        
        $params = [
            'timeWindow' => $timeWindow,
            'ip_address' => $ipAddress
        ];
        
        // Si un matricule est fourni, ajouter la condition
        if ($matricule) {
            $sql .= " AND matricule = :matricule";
            $params['matricule'] = $matricule;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        
        return $result['attempt_count'] >= $maxAttempts;
    }

    /**
     * Initie le processus de réinitialisation de mot de passe
     * 
     * @param string $email Email de l'utilisateur
     * @param string $matricule Matricule de l'utilisateur
     * @return array Résultat de l'opération
     */
    public function initiatePasswordReset($email, $matricule = null)
    {
        try {
            // Si le matricule est fourni, vérifier que l'email et le matricule correspondent
            if ($matricule) {
                $user = User::findByEmail($email);
                
                // Vérifier que l'utilisateur existe et que le matricule correspond
                if (!$user || $user['matricule'] !== $matricule) {
                    // Pour des raisons de sécurité, ne pas indiquer la raison exacte de l'échec
                    return [
                        'success' => true,
                        'message' => 'Si cette adresse email est associée à un compte, un email de réinitialisation a été envoyé.'
                    ];
                }
            } else {
                // Si pas de matricule fourni, juste vérifier l'email
                $user = User::findByEmail($email);
                
                if (!$user) {
                    // Pour des raisons de sécurité, ne pas indiquer si l'email existe ou non
                    return [
                        'success' => true,
                        'message' => 'Si cette adresse email est associée à un compte, un email de réinitialisation a été envoyé.'
                    ];
                }
            }
            
            // Générer un token de réinitialisation
            $selector = bin2hex(random_bytes(8));
            $validator = bin2hex(random_bytes(32));
            
            // Hasher le validator pour le stockage
            $hashedValidator = hash('sha256', $validator);
            
            // Date d'expiration (1 heure)
            $expiresAt = date('Y-m-d H:i:s', time() + 60 * 60);
            
            // Supprimer les tokens existants pour cet utilisateur
            $stmt = $this->db->prepare("
                DELETE FROM PASSWORD_RESET_TOKENS
                WHERE email = :email
            ");
            
            $stmt->execute(['email' => $email]);
            
            // Insérer le nouveau token
            $stmt = $this->db->prepare("
                INSERT INTO PASSWORD_RESET_TOKENS (email, selector, token, expires_at, created_at)
                VALUES (:email, :selector, :token, :expires_at, NOW())
            ");
            
            $stmt->execute([
                'email' => $email,
                'selector' => $selector,
                'token' => $hashedValidator,
                'expires_at' => $expiresAt
            ]);
            
            // Construire l'URL de réinitialisation avec le sous-domaine approprié
            $domaine = getenv('DOMAINE') ?: 'http://localhost/coud_bouletplate';
            $resetUrl = $domaine . '/reset-password/reset/' . $selector . '.' . $validator;
            
            // Journaliser l'URL de réinitialisation en développement
            if (getenv('APP_ENV') !== 'production') {
                error_log("URL de réinitialisation de mot de passe générée: $resetUrl");
            }
            
            // Retourner un message de succès avec les informations du token
            return [
                'success' => true,
                'message' => 'Si cette adresse email est associée à un compte, un email de réinitialisation a été envoyé.',
                'token_info' => [
                    'selector' => $selector,
                    'validator' => $validator, // Ne pas envoyer dans un environnement de production réel
                    'reset_url' => $resetUrl,
                    'expires_at' => $expiresAt
                ]
            ];
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            error_log('Erreur lors de la génération du token de réinitialisation: ' . $e->getMessage());
            
            // Retourner un message d'erreur
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la génération du token de réinitialisation.',
                'error' => $e->getMessage() // Ne pas inclure en production
            ];
        }
    }

    /**
     * Vérifie la validité d'un token de réinitialisation de mot de passe
     * 
     * @param string $token Token de réinitialisation
     * @return array|bool Données du token avec email ou false si invalide
     */
    public function verifyPasswordResetToken($token)
    {
        // Parse le token
        list($selector, $validator) = $this->parseRefreshToken($token);
        
        if (!$selector || !$validator) {
            return false;
        }
        
        // Récupérer le token correspondant au selector
        $stmt = $this->db->prepare("
            SELECT * FROM PASSWORD_RESET_TOKENS
            WHERE selector = :selector
            AND expires_at > NOW()
        ");
        
        $stmt->execute(['selector' => $selector]);
        
        $tokenData = $stmt->fetch();
        
        if (!$tokenData) {
            return false;
        }
        
        // Vérifier le validator
        if (!hash_equals($tokenData['token'], hash('sha256', $validator))) {
            return false;
        }
        
        return $tokenData;
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur
     * 
     * @param string $token Token de réinitialisation
     * @param string $newPassword Nouveau mot de passe
     * @return array Résultat de l'opération
     */
    public function resetPassword($token, $newPassword)
    {
        // Vérifier le token
        $tokenData = $this->verifyPasswordResetToken($token);
        
        if (!$tokenData) {
            return [
                'success' => false,
                'message' => 'Token de réinitialisation invalide ou expiré'
            ];
        }
        
        // Valider le mot de passe
        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 8 caractères'
            ];
        }
        
        // Récupérer l'utilisateur
        $user = User::findByEmail($tokenData['email']);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ];
        }
        
        // Hasher le nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Mettre à jour le mot de passe
        $stmt = $this->db->prepare("
            UPDATE USERS
            SET password = :password, updated_at = NOW()
            WHERE email = :email
        ");
        
        $result = $stmt->execute([
            'password' => $hashedPassword,
            'email' => $tokenData['email']
        ]);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du mot de passe'
            ];
        }
        
        // Supprimer le token utilisé
        $stmt = $this->db->prepare("
            DELETE FROM PASSWORD_RESET_TOKENS
            WHERE selector = :selector
        ");
        
        $stmt->execute(['selector' => $tokenData['selector']]);
        
        // Révoquer tous les refresh tokens de l'utilisateur pour sécurité
        User::invalidateAllRefreshTokens($user['id']);
        
        return [
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès. Veuillez vous connecter avec votre nouveau mot de passe.'
        ];
    }
} 
<?php

namespace App\Controllers;

require_once dirname(__DIR__) . '/config.php';

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Core\Controller;
use App\Validations\LoginValidator;
use App\logs\ErrorHandler;
use App\Services\LoginService;

/**
 * Contrôleur de login
 */
class LoginController extends Controller
{
    /**
     * Service d'authentification
     */
    private $loginService;
    
    /**
     * Validateur des données de connexion
     */
    private $validator;
    
    /**
     * Gestionnaire d'erreurs
     */
    private $errorHandler;
    
    /**
     * Initialise le contrôleur
     */
    public function __construct()
    {
        parent::__construct();
        
        try {
            // Initialiser le validateur
            $this->validator = new LoginValidator();
            
            // Initialiser le gestionnaire d'erreurs
            $this->errorHandler = new ErrorHandler();
            
            // Initialiser le service de login
            $this->loginService = new LoginService();
            
            // Vérification de la route de déconnexion
            if ($this->loginService->isLoggedIn() && 
                $_SERVER['REQUEST_URI'] !== BASE_URL . '/login/logout') {
                $this->redirectTo('/');
            }
        } catch (\Exception $e) {
            error_log("Erreur d'initialisation: " . $e->getMessage());
        }
    }

    /**
     * Page d'accueil du login
     */
    public function index()
    {
        // Si c'est une requête POST, traiter la tentative de connexion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->processLogin();
        }
        
        // Afficher le formulaire de connexion
        $this->render('login/index', [
            'title' => 'Connexion',
            'errors' => [],
            'old' => [],
            'BASE_URL' => BASE_URL
        ]);
    }
    
    /**
     * Traiter le formulaire de connexion
     */
    public function processLogin()
    {
        $data = [
            'matricule' => $_POST['matricule'] ?? '',
            'password' => $_POST['password'] ?? ''
        ];
        
        // Valider les données
        $validationResult = $this->validator->validate($data);
        
        if ($validationResult !== true) {
            return $this->render('login/index', [
                'title' => 'Connexion',
                'errors' => $validationResult,
                'old' => $data,
                'BASE_URL' => BASE_URL
            ]);
        }
        
        try {
            $loginResult = $this->loginService->login($data['matricule'], $data['password']);
            
            if (!$loginResult['success']) {
                // Afficher le message d'erreur
                return $this->render('login/index', [
                    'title' => 'Connexion',
                    'errors' => ['auth' => $loginResult['message'] ?? 'Identifiants invalides'],
                    'old' => $data,
                    'BASE_URL' => BASE_URL
                ]);
            }
            
            // Connexion réussie - Stocker les informations dans la session
            $_SESSION['user'] = $loginResult['user'];
            $_SESSION['access_token'] = $loginResult['accessToken'];
            $_SESSION['refresh_token'] = $loginResult['refreshToken'];
            
            // Message de succès
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Connexion réussie! Redirection...'
            ];
            
            // Rediriger vers la page d'accueil
            $this->redirectTo('/');
            
        } catch (\Exception $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            return $this->render('login/index', [
                'title' => 'Connexion',
                'errors' => ['auth' => 'Une erreur est survenue lors de la tentative de connexion'],
                'old' => $data,
                'BASE_URL' => BASE_URL
            ]);
        }
    }
    
    /**
     * Déconnecte l'utilisateur
     * Peut être appelé par GET ou POST
     */
    public function logout()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Journaliser la déconnexion si un utilisateur est connecté
        if (isset($_SESSION['user']) && isset($_SESSION['user']['matricule'])) {
            $matricule = $_SESSION['user']['matricule'];
            $this->errorHandler->logError("Déconnexion de l'utilisateur: " . $matricule, 'auth', 1);
        }
        
        // Appeler la méthode de déconnexion du service
        if (isset($this->loginService)) {
            $this->loginService->logout();
        } else {
            // Détruire la session manuellement si le service n'est pas disponible
            session_destroy();
        }
        
        // Message de succès à afficher (facultatif)
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Vous avez été déconnecté avec succès.'
        ];
        
        // Rediriger vers la page de connexion
        $this->redirectTo('/login');
    }
    
    /**
     * Redirige vers une URL
     * 
     * @param string $url URL de destination
     */
    protected function redirectTo($url): void
    {
        // Utiliser la constante BASE_URL
        if (strpos($url, BASE_URL) !== 0 && $url !== '/') {
            $url = BASE_URL . $url;
        } else if ($url === '/') {
            $url = BASE_URL . '/';
        }
        
        header('Location: ' . $url);
        exit;
    }

    /**
     * Affiche les logs d'erreur pour le débogage (à désactiver en production)
     */
    public function debug()
    {
        // Vérifier si l'environnement est de développement
        if (getenv('APP_ENV') === 'production') {
            $this->redirectTo('/login');
            return;
        }
        
        // Définir le chemin du fichier de logs
        $logFile = dirname(dirname(__FILE__)) . '/logs/files/' . date('Y-m-d') . '_email.log';
        $phpErrorLog = '/opt/lampp/logs/php_error_log';
        
        // Vérifier si le fichier existe
        $logs = [];
        if (file_exists($logFile)) {
            $logs['email'] = file_get_contents($logFile);
        } else {
            $logs['email'] = "Aucun fichier de log trouvé à: $logFile";
        }
        
        if (file_exists($phpErrorLog)) {
            // Récupérer les 200 dernières lignes du fichier d'erreur PHP
            $logs['php'] = shell_exec("tail -n 200 $phpErrorLog");
        } else {
            $logs['php'] = "Aucun fichier de log PHP trouvé à: $phpErrorLog";
        }
        
        // Afficher les logs
        echo "<h1>Logs de débogage</h1>";
        echo "<h2>Logs d'emails</h2>";
        echo "<pre>" . htmlspecialchars($logs['email']) . "</pre>";
        echo "<h2>Logs PHP</h2>";
        echo "<pre>" . htmlspecialchars($logs['php']) . "</pre>";
        
        exit;
    }
}
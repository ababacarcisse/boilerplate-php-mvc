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
            
            // Utiliser la constante BASE_URL pour la vérification
            if ($this->loginService->isLoggedIn() && 
                strpos($_SERVER['REQUEST_URI'], BASE_URL . '/login/logout') === false) {
                $this->redirectTo(BASE_URL . '/');
            }
        } catch (\Exception $e) {
            error_log("Erreur d'initialisation des dépendances: " . $e->getMessage());
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
    private function processLogin()
    {
        // Récupérer les données du formulaire
        $data = [
            'matricule' => $_POST['matricule'] ?? '',
            'password' => $_POST['password'] ?? ''
        ];
        
        // Valider les données
        $validationResult = $this->validator->validate($data);
        
        // Si la validation échoue, afficher le formulaire avec les erreurs
        if ($validationResult !== true) {
            return $this->render('login/index', [
                'title' => 'Connexion',
                'errors' => $validationResult,
                'old' => $data
            ]);
        }
        
        try {
            // Tenter de connecter l'utilisateur
            $loginResult = $this->loginService->login($data['matricule'], $data['password']);
            
            // Si la connexion échoue, afficher les erreurs
            if (!$loginResult['success']) {
                $errorMessage = $loginResult['message'] ?? 'Identifiants invalides';
                
                // En mode développement, afficher plus de détails pour le débogage
                if (isset($loginResult['httpCode']) || isset($loginResult['response'])) {
                    $errorMessage .= ' (Code: ' . ($loginResult['httpCode'] ?? 'inconnu');
                    
                    if (isset($loginResult['response']) && !empty($loginResult['response'])) {
                        $errorMessage .= ', Réponse: ' . substr($loginResult['response'], 0, 100) . '...)';
                    } else {
                        $errorMessage .= ', Pas de réponse)';
                    }
                }
                
                return $this->render('login/index', [
                    'title' => 'Connexion',
                    'errors' => ['auth' => $errorMessage],
                    'old' => $data
                ]);
            }
            
            // Rediriger vers la page d'accueil après connexion réussie
            $this->redirectTo('/');
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            $this->errorHandler->logError($e->getMessage(), 'login');
            
            // Afficher une erreur générique
            return $this->render('login/index', [
                'title' => 'Connexion',
                'errors' => ['auth' => 'Une erreur est survenue lors de la tentative de connexion'],
                'old' => $data
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
    private function redirectTo($url): void
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
<?php

namespace App\Controllers;

use Core\Controller;
use App\Validations\RegisterValidator;
use App\Services\RegisterService;
use App\logs\ErrorHandler;

/**
 * Généré automatiquement par Coud le " . date('Y-m-d H:i') . "
 * Contrôleur: RegisterController
 */
class RegisterController extends Controller
{
    /**
     * Service d'inscription
     */
    private $registerService;
    
    /**
     * Validateur des données d'inscription
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
            $this->validator = new RegisterValidator();
            
            // Initialiser le gestionnaire d'erreurs
            $this->errorHandler = new ErrorHandler();
            
            // Initialiser le service d'inscription
            $this->registerService = new RegisterService();
        } catch (\Exception $e) {
            error_log("Erreur d'initialisation des dépendances: " . $e->getMessage());
        }
    }

    /**
     * Page d'accueil
     */
    public function index()
    {
        // Si c'est une requête POST, traiter la tentative d'inscription
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->processRegister();
        }
        
        // Afficher le formulaire d'inscription
        $this->render('register/index', [
            'title' => 'Inscription',
            'errors' => [],
            'old' => []
        ]);
    }

    /**
     * Afficher un élément
     */
    public function show($id)
    {
        // Logique pour récupérer et afficher un élément
        $this->render('register/show', ['id' => $id]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $this->render('register/create');
    }

    /**
     * Traiter le formulaire de création
     */
    public function store()
    {
        // Logique pour traiter les données du formulaire et créer un nouvel élément
        // Redirection vers l'index après création
        header('Location: /register');
    }

    /**
     * Traiter le formulaire d'inscription
     */
    private function processRegister()
    {
        // Récupérer les données du formulaire
        $data = [
            'matricule' => $_POST['matricule'] ?? '',
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'date_naissance' => $_POST['date_naissance'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];
        
        // Valider les données
        $validationResult = $this->validator->validate($data);
        
        // Si la validation échoue, afficher le formulaire avec les erreurs
        if ($validationResult !== true) {
            return $this->render('register/index', [
                'title' => 'Inscription',
                'errors' => $validationResult,
                'old' => $data
            ]);
        }
        
        try {
            // Préparer les données pour l'API (retirer la confirmation du mot de passe)
            $apiData = $data;
            unset($apiData['password_confirm']);
            
            // Tenter d'inscrire l'utilisateur
            $registerResult = $this->registerService->register($apiData);
            
            // Si l'inscription échoue, afficher les erreurs
            if (!$registerResult['success']) {
                $errorMessage = $registerResult['message'] ?? 'Erreur lors de l\'inscription';
                
                // En mode développement, afficher plus de détails pour le débogage
                if (isset($registerResult['httpCode']) || isset($registerResult['response'])) {
                    $errorMessage .= ' (Code: ' . ($registerResult['httpCode'] ?? 'inconnu');
                    
                    if (isset($registerResult['response']) && !empty($registerResult['response'])) {
                        $errorMessage .= ', Réponse: ' . substr($registerResult['response'], 0, 100) . '...)';
                    } else {
                        $errorMessage .= ', Pas de réponse)';
                    }
                }
                
                return $this->render('register/index', [
                    'title' => 'Inscription',
                    'errors' => ['auth' => $errorMessage],
                    'old' => $data
                ]);
            }
            
            // Définir un message de succès
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.'
            ];
            
            // Rediriger vers la page de connexion
            $this->redirectTo('/login');
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            $this->errorHandler->logError($e->getMessage(), 'register');
            
            // Afficher une erreur générique
            return $this->render('register/index', [
                'title' => 'Inscription',
                'errors' => ['auth' => 'Une erreur est survenue lors de la tentative d\'inscription'],
                'old' => $data
            ]);
        }
    }
    
    /**
     * Redirige vers une URL
     * 
     * @param string $url URL de destination
     */
    private function redirectTo(string $url): void
    {
        // Préfixer l'URL avec le sous-répertoire si ce n'est pas déjà le cas
        if (strpos($url, '/coud_bouletplate') !== 0 && $url !== '/') {
            $url = '/coud_bouletplate' . $url;
        } else if ($url === '/') {
            $url = '/coud_bouletplate/';
        }
        
        header('Location: ' . $url);
        exit;
    }
}
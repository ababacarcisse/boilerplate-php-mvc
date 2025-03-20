<?php

namespace App\Controllers;


use Core\Controller;
use App\Validations\ResetPasswordValidator;
use App\Services\ResetPasswordService;
use App\logs\ErrorHandler;

/**
 * Généré automatiquement par Coud le " . date('Y-m-d H:i') . "
 * Contrôleur: ResetPasswordController
 */
class ResetPasswordController extends Controller
{
   
    /**
     * Service de réinitialisation de mot de passe
     */
    private $resetPasswordService;
    
    /**
     * Validateur des données de réinitialisation
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
        
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        try {
            // Initialiser le validateur
            $this->validator = new ResetPasswordValidator();
            
            // Initialiser le gestionnaire d'erreurs
            $this->errorHandler = new ErrorHandler();
            
            // Initialiser le service de réinitialisation
            $this->resetPasswordService = new ResetPasswordService();
        } catch (\Exception $e) {
            error_log("Erreur d'initialisation des dépendances: " . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de demande de réinitialisation
     */
    public function index()
    {
        // Si c'est une requête POST, traiter la demande
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->processRequest();
        }
        
        // Afficher le formulaire de demande
        $this->render('reset_password/index', [
            'title' => 'Réinitialisation du mot de passe',
            'errors' => [],
            'old' => []
        ]);
    }
    
    /**
     * Traite la demande de réinitialisation de mot de passe
     */
    private function processRequest()
    {
        // Récupérer les données du formulaire
        $data = [
            'email' => $_POST['email'] ?? '',
            'matricule' => $_POST['matricule'] ?? '',
        ];
        
        // Valider les données
        $validationResult = $this->validator->validate($data);
        
        // Si la validation échoue, afficher le formulaire avec les erreurs
        if ($validationResult !== true) {
            return $this->render('reset_password/index', [
                'title' => 'Réinitialisation du mot de passe',
                'errors' => $validationResult,
                'old' => $data
            ]);
        }
        
        try {
            // Envoyer la demande de réinitialisation
            $resetResult = $this->resetPasswordService->requestReset($data);
            
            // Si la demande échoue, afficher les erreurs
            if (!$resetResult['success']) {
                $errorMessage = $resetResult['message'] ?? 'Erreur lors de la demande de réinitialisation';
                
                // En mode développement, afficher plus de détails pour le débogage
                if (isset($resetResult['httpCode']) || isset($resetResult['response'])) {
                    $errorMessage .= ' (Code: ' . ($resetResult['httpCode'] ?? 'inconnu');
                    
                    if (isset($resetResult['response']) && !empty($resetResult['response'])) {
                        $errorMessage .= ', Réponse: ' . substr($resetResult['response'], 0, 100) . '...)';
                    } else {
                        $errorMessage .= ', Pas de réponse)';
                    }
                }
                
                return $this->render('reset_password/index', [
                    'title' => 'Réinitialisation du mot de passe',
                    'errors' => ['auth' => $errorMessage],
                    'old' => $data
                ]);
            }
            
            // Définir un message de succès dans la session
            $_SESSION['success_message'] = 'Un e-mail de réinitialisation a été envoyé à l\'adresse indiquée. Veuillez vérifier votre boîte de réception et suivre les instructions.';
            
            // Rediriger vers la même page pour afficher le message
            $this->redirectTo('/reset-password');
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            $this->errorHandler->logError($e->getMessage(), 'reset_password');
            
            // Afficher une erreur générique
            return $this->render('reset_password/index', [
                'title' => 'Réinitialisation du mot de passe',
                'errors' => ['auth' => 'Une erreur est survenue lors de la demande de réinitialisation'],
                'old' => $data
            ]);
        }
    }
    
    /**
     * Affiche le formulaire de réinitialisation avec le token
     */
    public function reset($params)
    {
        // Récupérer le token de l'URL
        $token = $params[0] ?? '';
        
        // Si pas de token, rediriger vers la page de demande
        if (empty($token)) {
            $this->redirectTo('/reset-password');
        }
        
        // Si c'est une requête POST, traiter la réinitialisation
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->processReset($token);
        }
        
        // Afficher le formulaire de réinitialisation
        $this->render('reset_password/reset', [
            'title' => 'Définir un nouveau mot de passe',
            'token' => $token,
            'errors' => [],
            'old' => []
        ]);
    }
    
    /**
     * Traite la réinitialisation du mot de passe
     */
    private function processReset(string $token)
    {
        // Récupérer les données du formulaire
        $data = [
            'token' => $token,
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];
        
        // Valider les données
        $validationResult = $this->validator->validateReset($data);
        
        // Si la validation échoue, afficher le formulaire avec les erreurs
        if ($validationResult !== true) {
            return $this->render('reset_password/reset', [
                'title' => 'Définir un nouveau mot de passe',
                'token' => $token,
                'errors' => $validationResult,
                'old' => $data
            ]);
        }
        
        try {
            // Envoyer la demande de réinitialisation
            $resetResult = $this->resetPasswordService->resetPassword($data);
            
            // Si la réinitialisation échoue, afficher les erreurs
            if (!$resetResult['success']) {
                $errorMessage = $resetResult['message'] ?? 'Erreur lors de la réinitialisation du mot de passe';
                
                // En mode développement, afficher plus de détails pour le débogage
                if (isset($resetResult['httpCode']) || isset($resetResult['response'])) {
                    $errorMessage .= ' (Code: ' . ($resetResult['httpCode'] ?? 'inconnu');
                    
                    if (isset($resetResult['response']) && !empty($resetResult['response'])) {
                        $errorMessage .= ', Réponse: ' . substr($resetResult['response'], 0, 100) . '...)';
                    } else {
                        $errorMessage .= ', Pas de réponse)';
                    }
                }
                
                return $this->render('reset_password/reset', [
                    'title' => 'Définir un nouveau mot de passe',
                    'token' => $token,
                    'errors' => ['auth' => $errorMessage],
                    'old' => $data
                ]);
            }
            
            // Définir un message de succès dans la session
            $_SESSION['success_message'] = 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.';
            
            // Rediriger vers la page de connexion
            $this->redirectTo('/login');
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            $this->errorHandler->logError($e->getMessage(), 'reset_password');
            
            // Afficher une erreur générique
            return $this->render('reset_password/reset', [
                'title' => 'Définir un nouveau mot de passe',
                'token' => $token,
                'errors' => ['auth' => 'Une erreur est survenue lors de la réinitialisation du mot de passe'],
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

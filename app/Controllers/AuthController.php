<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Core\CSRF;
use Core\Validator;

class AuthController extends Controller
{
    // Authentifie l'utilisateur
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matricule = $_POST['matricule'];
            $password = $_POST['password'];

            $user = User::findByMatricule($matricule);

            if ($user && password_verify($password, $user->password)) {
                // Initialiser la session
                session_start();
                $_SESSION['user_id'] = $user->id;
                $_SESSION['role'] = $user->role;

                // Redirection selon le rôle
                switch ($user->role) {
                    case 'etudiant':
                        header('Location: /etudiant/dashboard');
                        break;
                    case 'admin':
                        header('Location: /admin/dashboard');
                        break;
                    case 'assistant':
                        header('Location: /assistant/dashboard');
                        break;
                    case 'agent':
                        header('Location: /agent/dashboard');
                        break;
                    case 'super_admin':
                        header('Location: /super_admin/dashboard');
                        break;
                }
                exit;
            } else {
                // Gérer l'erreur d'authentification
                echo "Identifiants invalides.";
            }
        }

        // Afficher le formulaire de connexion
        $this->render('auth/login');
    }

    // Déconnecte l'utilisateur
    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /auth/login');
        exit;
    }

    // Inscription d'un nouvel utilisateur
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF
            if (!CSRF::verifyToken($_POST['csrf_token'])) {
                die("Token CSRF invalide.");
            }

            $matricule = Validator::sanitize($_POST['matricule']);
            $date_naissance = Validator::sanitize($_POST['date_naissance']);
            $email = Validator::sanitize($_POST['email']);

            // Validation des entrées
            if (!Validator::validateMatricule($matricule) || !Validator::validateEmail($email)) {
                die("Entrées invalides.");
            }

            // Créer un nouvel utilisateur
            $user = new User();
            $user->matricule = $matricule;
            $user->date_naissance = $date_naissance;
            $user->email = $email;

            // Envoyer un email de confirmation
            $this->sendConfirmationEmail($user);
        }

        // Afficher le formulaire d'inscription avec le token CSRF
        $csrfToken = CSRF::generateToken();
        $this->render('auth/register', ['csrf_token' => $csrfToken]);
    }

    // Envoie un email de confirmation
    private function sendConfirmationEmail($user)
    {
        $mail = new PHPMailer(true);
        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'votre_email@example.com';
            $mail->Password = 'votre_mot_de_passe';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Destinataire
            $mail->setFrom('votre_email@example.com', 'Nom de l\'Université');
            $mail->addAddress($user->email);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation d\'inscription';
            $mail->Body = 'Cliquez sur ce lien pour choisir votre mot de passe : <a href="http://example.com/confirm?matricule=' . $user->matricule . '">Confirmer</a>';

            $mail->send();
            echo 'Email de confirmation envoyé.';
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
        }
    }
} 
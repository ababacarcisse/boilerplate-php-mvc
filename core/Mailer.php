<?php

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private $mail;

    public function __construct()
    {
        // Configuration de PHPMailer
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.example.com'; // Hôte SMTP
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'votre_email@example.com'; // Nom d'utilisateur SMTP
        $this->mail->Password = 'votre_mot_de_passe'; // Mot de passe SMTP
        $this->mail->SMTPSecure = 'tls'; // Type de sécurité
        $this->mail->Port = 587; // Port SMTP
    }

    // Envoie un email
    public function sendEmail($to, $subject, $body)
    {
        try {
            // Destinataire
            $this->mail->setFrom('votre_email@example.com', 'Nom de l\'Université');
            $this->mail->addAddress($to); // Ajoute le destinataire

            // Contenu de l'email
            $this->mail->isHTML(true); // Définit le format de l'email en HTML
            $this->mail->Subject = $subject; // Sujet de l'email
            $this->mail->Body = $body; // Corps de l'email

            // Envoie l'email
            $this->mail->send();
            return true; // Retourne vrai si l'email a été envoyé avec succès
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : {$this->mail->ErrorInfo}";
            return false; // Retourne faux en cas d'erreur
        }
    }

    // Charge un template d'email
    public function loadTemplate($template, $data = [])
    {
        // Extrait les données pour les rendre accessibles dans le template
        extract($data);
        ob_start(); // Démarre la mise en mémoire tampon de sortie
        require "../app/Views/emails/$template.php"; // Inclut le fichier de template
        return ob_get_clean(); // Retourne le contenu du template
    }

    // Envoie un email de confirmation d'inscription
    public function sendConfirmationEmail($to, $matricule)
    {
        $subject = 'Confirmation d\'inscription';
        $body = $this->loadTemplate('confirmation', ['matricule' => $matricule]);
        return $this->sendEmail($to, $subject, $body);
    }

    // Envoie un email de réinitialisation de mot de passe
    public function sendPasswordResetEmail($to, $token)
    {
        $subject = 'Réinitialisation de votre mot de passe';
        $body = $this->loadTemplate('password_reset', ['token' => $token]);
        return $this->sendEmail($to, $subject, $body);
    }
} 
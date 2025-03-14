<?php

namespace App\Lib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;
    private $config;

    public function __construct()
    {
        // Charger la configuration
        $configFile = dirname(__DIR__) . '/config/config.php';
        if (!file_exists($configFile)) {
            throw new \Exception("Le fichier de configuration n'existe pas: $configFile");
        }
        
        $config = require $configFile;
        $this->config = $config['smtp'];
        
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();
    }

    private function setupMailer()
    {
        try {
            // Configuration SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            $this->mailer->SMTPSecure = $this->config['secure'];
            $this->mailer->Port = $this->config['port'];
            $this->mailer->CharSet = 'UTF-8';
            
            // Expéditeur par défaut
            $this->mailer->setFrom($this->config['username'], 'Centre Universitaire COUD');
            
            // Activer le HTML
            $this->mailer->isHTML(true);
        } catch (Exception $e) {
            throw new \Exception('Erreur de configuration de l\'email: ' . $e->getMessage());
        }
    }

    /**
     * Envoie un email
     */
    public function send(string $to, string $subject, string $body, array $attachments = []): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            // Ajouter les pièces jointes si nécessaire
            foreach ($attachments as $attachment) {
                $this->mailer->addAttachment($attachment);
            }
            
            return $this->mailer->send();
        } catch (Exception $e) {
            // En production, loguer l'erreur plutôt que de la lever
            error_log('Erreur d\'envoi d\'email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un email de bienvenue
     */
    public function sendWelcomeEmail($user): bool
    {
        $subject = 'Bienvenue au COUD - Votre compte a été créé';
        
        $body = <<<HTML
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4361ee; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f8f9fa; }
                .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #6c757d; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Bienvenue au COUD</h1>
                </div>
                <div class="content">
                    <p>Bonjour {$user['fullName']},</p>
                    <p>Votre compte a été créé avec succès. Voici vos informations de connexion :</p>
                    <p><strong>Matricule :</strong> {$user['matricule']}</p>
                    <p>Vous pouvez maintenant vous connecter à votre espace personnel.</p>
                    <p>Cordialement,<br>L'équipe du COUD</p>
                </div>
                <div class="footer">
                    <p>Ce message est automatique, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
        
        return $this->send($user['email'], $subject, $body);
    }

    /**
     * Envoie une notification de connexion
     */
    public function sendLoginNotification($user): bool
    {
        $time = date('d/m/Y H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $subject = 'COUD - Nouvelle connexion à votre compte';
        
        $body = <<<HTML
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4361ee; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f8f9fa; }
                .alert { color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; }
                .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #6c757d; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Alerte de sécurité</h1>
                </div>
                <div class="content">
                    <p>Bonjour {$user['fullName']},</p>
                    <p>Une connexion à votre compte a été effectuée le {$time} depuis l'adresse IP {$ip}.</p>
                    <p>Si vous n'êtes pas à l'origine de cette connexion, veuillez contacter immédiatement notre service de sécurité.</p>
                    <p>Cordialement,<br>L'équipe du COUD</p>
                </div>
                <div class="footer">
                    <p>Ce message est automatique, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
        
        return $this->send($user['email'], $subject, $body);
    }
    
    /**
     * Envoie un email de réinitialisation de mot de passe
     */
    public function sendPasswordResetEmail($user, $resetUrl): bool
    {
        $subject = 'COUD - Réinitialisation de votre mot de passe';
        
        $body = <<<HTML
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4361ee; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f8f9fa; }
                .button { display: inline-block; background-color: #4361ee; color: white; padding: 10px 20px; 
                           text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .note { background-color: #fff3cd; padding: 10px; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #6c757d; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Réinitialisation de mot de passe</h1>
                </div>
                <div class="content">
                    <p>Bonjour {$user['fullName']},</p>
                    <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
                    <p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le bouton ci-dessous :</p>
                    <p style="text-align: center;">
                        <a href="{$resetUrl}" class="button">Réinitialiser mon mot de passe</a>
                    </p>
                    <p>Si vous ne parvenez pas à cliquer sur le bouton, copiez et collez l'URL suivante dans votre navigateur :</p>
                    <p>{$resetUrl}</p>
                    <div class="note">
                        <p><strong>Note importante :</strong> Ce lien expirera dans 1 heure.</p>
                        <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email ou contacter notre service sécurité.</p>
                    </div>
                    <p>Cordialement,<br>L'équipe du COUD</p>
                </div>
                <div class="footer">
                    <p>Ce message est automatique, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
        
        return $this->send($user['email'], $subject, $body);
    }
} 
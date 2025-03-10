<?php

namespace Core;

class Validator
{
    // Nettoie une chaîne pour éviter les attaques XSS
    public static function sanitize($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    // Valide un email
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Valide un matricule
    public static function validateMatricule($matricule)
    {
        return preg_match('/^[A-Z0-9]{9}$/', $matricule); // Exemple de validation pour un matricule de 9 caractères
    }
} 
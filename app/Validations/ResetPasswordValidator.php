<?php

namespace App\Validations;

/**
 * Validateur pour la réinitialisation de mot de passe
 */
class ResetPasswordValidator
{
    /**
     * Valide les données de demande de réinitialisation
     * 
     * @param array $data Les données à valider
     * @return bool|array Retourne true si les données sont valides, 
     *                   ou un tableau d'erreurs si la validation échoue
     */
    public function validate(array $data)
    {
        $errors = [];
        
        // Validation de l'email (obligatoire)
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est obligatoire';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email n\'est pas valide';
        }
        
        // Validation du matricule (obligatoire)
        if (empty($data['matricule'])) {
            $errors['matricule'] = 'Le matricule est obligatoire';
        } elseif (strlen($data['matricule']) < 3) {
            $errors['matricule'] = 'Le matricule doit contenir au moins 3 caractères';
        }
        
        // Retourner true si pas d'erreurs, sinon le tableau d'erreurs
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Valide les données de réinitialisation de mot de passe
     * 
     * @param array $data Les données à valider
     * @return bool|array Retourne true si les données sont valides, 
     *                   ou un tableau d'erreurs si la validation échoue
     */
    public function validateReset(array $data)
    {
        $errors = [];
        
        // Validation du token (obligatoire)
        if (empty($data['token'])) {
            $errors['token'] = 'Le token est manquant';
        }
        
        // Validation du mot de passe (obligatoire, min 8 caractères)
        if (empty($data['password'])) {
            $errors['password'] = 'Le mot de passe est obligatoire';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        
        // Validation de la confirmation du mot de passe
        if (empty($data['password_confirm'])) {
            $errors['password_confirm'] = 'La confirmation du mot de passe est obligatoire';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Les mots de passe ne correspondent pas';
        }
        
        // Retourner true si pas d'erreurs, sinon le tableau d'erreurs
        return empty($errors) ? true : $errors;
    }
}
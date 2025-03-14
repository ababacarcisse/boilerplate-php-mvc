<?php

namespace App\Validations;

use Core\Validator;

/**
 * Validation des données de connexion
 */
class LoginValidator extends Validator
{
    /**
     * Valide les données du formulaire de connexion
     * 
     * @param array $data Données à valider
     * @return array|bool Tableau d'erreurs ou true si valide
     */
    public function validate(array $data) 
    {
        $errors = [];
        
        // Vérifier que le matricule est présent
        if (empty($data['matricule'])) {
            $errors['matricule'] = 'Le matricule est requis';
        }
        
        // Vérifier que le mot de passe est présent
        if (empty($data['password'])) {
            $errors['password'] = 'Le mot de passe est requis';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        
        // Si des erreurs existent, retourner les erreurs
        if (!empty($errors)) {
            return $errors;
        }
        
        // Tout est valide
        return true;
    }
} 
<?php

namespace App\Validations;

/**
 * Validateur pour l'inscription des utilisateurs
 */
class RegisterValidator
{
    /**
     * Valide les données d'inscription
     * 
     * @param array $data Les données à valider
     * @return bool|array Retourne true si les données sont valides, 
     *                   ou un tableau d'erreurs si la validation échoue
     */
    public function validate(array $data)
    {
        $errors = [];
        
        // Validation du matricule (obligatoire)
        if (empty($data['matricule'])) {
            $errors['matricule'] = 'Le matricule est obligatoire';
        } elseif (strlen($data['matricule']) < 3) {
            $errors['matricule'] = 'Le matricule doit contenir au moins 3 caractères';
        }
        
        // Validation du nom (obligatoire)
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est obligatoire';
        } elseif (strlen($data['nom']) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères';
        }
        
        // Validation du prénom (obligatoire)
        if (empty($data['prenom'])) {
            $errors['prenom'] = 'Le prénom est obligatoire';
        } elseif (strlen($data['prenom']) < 2) {
            $errors['prenom'] = 'Le prénom doit contenir au moins 2 caractères';
        }
        
        // Validation de la date de naissance (obligatoire)
        if (empty($data['date_naissance'])) {
            $errors['date_naissance'] = 'La date de naissance est obligatoire';
        } else {
            // Vérifier le format de la date (YYYY-MM-DD)
            $date = \DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
            if (!$date || $date->format('Y-m-d') !== $data['date_naissance']) {
                $errors['date_naissance'] = 'La date de naissance doit être au format YYYY-MM-DD';
            } else {
                // Vérifier que l'utilisateur a au moins 18 ans
                $now = new \DateTime();
                $age = $now->diff($date)->y;
                if ($age < 12) {
                    $errors['date_naissance'] = 'Vous devez avoir au moins 12 ans pour vous inscrire';
                }
            }
        }
        
        // Validation de l'email (obligatoire)
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est obligatoire';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email n\'est pas valide';
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
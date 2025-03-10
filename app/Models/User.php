<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    public $id;
    public $matricule;
    public $fullName;
    public $date_naissance;
    public $lieu_naissance;
    public $numero_telephone;
    public $sexe;
    public $faculte;
    public $filiere;
    public $email;
    public $password;
    public $role;

    // Récupère un utilisateur par matricule
    public static function findByMatricule($matricule)
    {
        $sql = "SELECT * FROM USERS WHERE matricule = :matricule";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute(['matricule' => $matricule]);
        return $stmt->fetchObject(self::class);
    }

    // Vérifie les identifiants
    public static function verifyCredentials($matricule, $password)
    {
        $user = self::findByMatricule($matricule);
        return $user && password_verify($password, $user->password);
    }

    // Met à jour le mot de passe
    public function updatePassword($newPassword)
    {
        $this->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE USERS SET password = :password WHERE id = :id";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute(['password' => $this->password, 'id' => $this->id]);
    }

    // Vérifie le rôle de l'utilisateur
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAssistant()
    {
        return $this->role === 'assistant';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function isEtudiant()
    {
        return $this->role === 'etudiant';
    }
} 
<?php

namespace App\Api\Services;

/**
 * Service pour la gestion des utilisateurs
 */
class UserService
{
    protected $db;

    public function __construct()
    {
        $this->db = db::getInstance();
    }

    /**
     * Récupère tous les utilisateurs
     */
    public function getAllUsers($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT id, matricule, email, role, created_at, updated_at 
                FROM users 
                ORDER BY id DESC 
                LIMIT :limit OFFSET :offset";
        
        return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
    }

    /**
     * Récupère un utilisateur par ID
     */
    public function getUserById($id)
    {
        $sql = "SELECT id, matricule, email, role, created_at, updated_at 
                FROM users 
                WHERE id = :id";
        
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Récupère un utilisateur par matricule
     */
    public function getUserByMatricule($matricule)
    {
        $sql = "SELECT * FROM users WHERE matricule = :matricule";
        return $this->db->fetchOne($sql, ['matricule' => $matricule]);
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function createUser($userData)
    {
        // Vérifier si le matricule existe déjà
        $existingUser = $this->getUserByMatricule($userData['matricule']);
        if ($existingUser) {
            throw new \Exception('Ce matricule est déjà utilisé');
        }
        
        // Hasher le mot de passe
        if (isset($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }
        
        // Ajouter les dates
        $userData['created_at'] = date('Y-m-d H:i:s');
        $userData['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('users', $userData);
    }

    /**
     * Met à jour un utilisateur
     */
    public function updateUser($id, $userData)
    {
        // Si le mot de passe est fourni, le hasher
        if (isset($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }
        
        // Mettre à jour la date
        $userData['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->update('users', $userData, ['id' => $id]);
        return $this->getUserById($id);
    }

    /**
     * Supprime un utilisateur
     */
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        return $this->db->query($sql, ['id' => $id])->rowCount() > 0;
    }
} 
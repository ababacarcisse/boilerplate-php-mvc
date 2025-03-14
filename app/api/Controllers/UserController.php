<?php

namespace App\Api\Controllers;

use App\Api\Services\UserService;

class UserController {
    protected $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    public function index() {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        
        try {
            $users = $this->userService->getAllUsers($page, $limit);
            echo json_encode(['success' => true, 'data' => $users]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function show($id) {
        try {
            $user = $this->userService->getUserById($id);
            if ($user) {
                echo json_encode(['success' => true, 'data' => $user]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function store() {
        // Logique pour créer un nouvel utilisateur
        echo "Créer un nouvel utilisateur";
    }

    public function update($id) {
        // Logique pour mettre à jour un utilisateur
        echo "Mettre à jour l'utilisateur avec ID: $id";
    }

    public function destroy($id) {
        // Logique pour supprimer un utilisateur
        echo "Supprimer l'utilisateur avec ID: $id";
    }
} 
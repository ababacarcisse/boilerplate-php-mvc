<?php

namespace App\Api\Controllers;

class UserController {
    public function index() {
        // Logique pour obtenir tous les utilisateurs
        echo "Liste des utilisateurs";
    }

    public function show($id) {
        // Logique pour obtenir un utilisateur par ID
        echo "Afficher l'utilisateur avec ID: $id";
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
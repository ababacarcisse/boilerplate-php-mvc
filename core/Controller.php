<?php

namespace Core;

abstract class Controller
{
    // Charge une vue et passe des données à celle-ci
    protected function render($view, $data = [])
    {
        extract($data); // Extrait les données pour les rendre accessibles dans la vue
        require "../app/Views/$view.php"; // Inclut le fichier de vue
    }
}

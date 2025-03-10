<?php

namespace Core;

class View
{
    // Gère le rendu des templates de vues
    public static function render($view, $data = [])
    {
        extract($data); // Extrait les données pour les rendre accessibles dans la vue
        require "../app/Views/$view.php"; // Inclut le fichier de vue
    }
}

<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Généré automatiquement par Coud le " . date('Y-m-d H:i') . "
 * Contrôleur: StatistiquesController
 */
class StatistiquesController extends Controller
{
    /**
     * Page d'accueil
     */
    public function index()
    {
        $this->render('statistiques/index');
    }

    /**
     * Afficher un élément
     */
    public function show($id)
    {
        // Logique pour récupérer et afficher un élément
        $this->render('statistiques/show', ['id' => $id]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $this->render('statistiques/create');
    }

    /**
     * Traiter le formulaire de création
     */
    public function store()
    {
        // Logique pour traiter les données du formulaire et créer un nouvel élément
        // Redirection vers l'index après création
        header('Location: /statistiques');
    }
}
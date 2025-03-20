<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Généré automatiquement par Coud le " . date('Y-m-d H:i') . "
 * Contrôleur: UtilisateursController
 */
class UtilisateursController extends Controller
{
    /**
     * Page d'accueil
     */
    public function index()
    {
        $this->render('utilisateurs/index');
    }

    /**
     * Afficher un élément
     */
    public function show($id)
    {
        // Logique pour récupérer et afficher un élément
        $this->render('utilisateurs/show', ['id' => $id]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $this->render('utilisateurs/create');
    }

    /**
     * Traiter le formulaire de création
     */
    public function store()
    {
        // Logique pour traiter les données du formulaire et créer un nouvel élément
        // Redirection vers l'index après création
        header('Location: /utilisateurs');
    }
}
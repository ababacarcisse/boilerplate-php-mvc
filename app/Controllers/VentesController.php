<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Généré automatiquement par Coud le " . date('Y-m-d H:i') . "
 * Contrôleur: VentesController
 */
class VentesController extends Controller
{
    /**
     * Page d'accueil
     */
    public function index()
    {
        $this->render('ventes/index');
    }

    /**
     * Afficher un élément
     */
    public function show($id)
    {
        // Logique pour récupérer et afficher un élément
        $this->render('ventes/show', ['id' => $id]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $this->render('ventes/create');
    }

    /**
     * Traiter le formulaire de création
     */
    public function store()
    {
        // Logique pour traiter les données du formulaire et créer un nouvel élément
        // Redirection vers l'index après création
        header('Location: /ventes');
    }
}
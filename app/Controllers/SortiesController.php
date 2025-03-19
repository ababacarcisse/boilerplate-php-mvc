<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Généré automatiquement par Coud le " . date('Y-m-d H:i') . "
 * Contrôleur: SortiesController
 */
class SortiesController extends Controller
{
    /**
     * Page d'accueil
     */
    public function index()
    {
        $this->render('sorties/index');
    }

    /**
     * Afficher un élément
     */
    public function show($id)
    {
        // Logique pour récupérer et afficher un élément
        $this->render('sorties/show', ['id' => $id]);
    }

     
   
 
}
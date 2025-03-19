<?php

namespace App\Controllers;

use Core\Controller;

class EntresController extends Controller
{
    public function index()
    {
        var_dump("EntresController::index appelé"); // Point de débogage 2
        $this->render('entres/index');
    }
} 
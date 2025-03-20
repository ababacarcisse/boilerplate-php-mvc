<?php

namespace App\Controllers;

use Core\Controller;

class EntresController extends Controller
{
    public function index()
    {
         $this->render('entres/index');
    }
} 
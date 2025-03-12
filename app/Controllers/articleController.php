<?php

namespace App\Controllers;

use Core\Controller;

class ArticleController extends Controller
{
    public function index()
    {
        $this->render('article');
    }
} 
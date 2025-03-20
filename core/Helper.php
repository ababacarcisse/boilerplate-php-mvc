<?php

namespace Core;

require_once dirname(__DIR__) . '/app/config.php';

class Helper
{
    /**
     * Génère une URL basée sur la racine du site
     */
    public static function url($path = '')
    {
        return BASE_URL . '/' . ltrim($path, '/');
    }
    
    /**
     * Génère une URL pour un asset (CSS, JS, image)
     */
    public static function asset($path)
    {
        return self::url('public/' . ltrim($path, '/'));
    }
} 
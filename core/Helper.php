<?php

namespace Core;

class Helper
{
    /**
     * Génère une URL basée sur la racine du site
     */
    public static function url($path = '')
    {
        $base_url = '/gestion-pharmacie';
        return $base_url . '/' . ltrim($path, '/');
    }
    
    /**
     * Génère une URL pour un asset (CSS, JS, image)
     */
    public static function asset($path)
    {
        return self::url('public/' . ltrim($path, '/'));
    }
} 
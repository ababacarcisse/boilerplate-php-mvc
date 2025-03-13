<?php

namespace App\Api\Routes;

class ApiRouter {
    protected $middlewares = [];

    public function addMiddleware($middleware) {
        $this->middlewares[] = $middleware;
    }

    public function dispatch($uri) {
        // Appliquer les middlewares
        foreach ($this->middlewares as $middleware) {
            $middleware->handle();
        }

        // Logique de routage
        echo "Dispatching URI: $uri";
    }
} 
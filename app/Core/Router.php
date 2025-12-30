<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $fallback;

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function setFallback($callback)
    {
        $this->fallback = $callback;
    }

    public function resolve()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';

        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$method][$uri])) {
            call_user_func($this->routes[$method][$uri]);
            return;
        }

        // Special dynamic route for Media
        if ($method === 'GET' && preg_match('#^/media/(.+)$#', $uri, $matches)) {
            // Check if a handler for /media is registered
            if (isset($this->routes['GET']['/media'])) {
                call_user_func($this->routes['GET']['/media'], $matches[1]);
                return;
            }
        }

        // Special dynamic route for API
        if ($method === 'GET' && preg_match('#^/api/content/(.+)$#', $uri, $matches)) {
            if (isset($this->routes['GET']['/api/content'])) {
                call_user_func($this->routes['GET']['/api/content'], $matches[1]);
                return;
            }
        }

        // Try Fallback (Dynamic Pages)
        if ($this->fallback) {
            call_user_func($this->fallback, ltrim($uri, '/'));
            return;
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
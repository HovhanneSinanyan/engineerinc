<?php

namespace Kernel;

class Router
{

    private static $instance = null;
    private $selectedRoute;
    private $routes = [];

    private function __construct()
    {
        $this->routes = [];
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function createKey($method, $uri) {
       return $method . '/' .$uri;
    }

    private function registerRoute(string $uri, string $controller, string $action, string $method)
    {
        $routeKey = $this->createKey($method, $uri);
        $this->routes[$routeKey] = [
            'controller' => $controller,
            'method' => $method,
            'action' => $action,
            'middlewares' => [],
            'params' => []
        ];
        $this->selectedRoute = $routeKey;
        return $this;
    }

    private function parseUrl(string $uri) {
        foreach (array_keys($this->routes) as $route) {
            $pattern = '#^' . preg_replace('/:[a-zA-Z0-9]+/', '([a-zA-Z0-9-]+)', $route) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                preg_match_all('/:([a-zA-Z0-9]+)/', $route, $paramNames);
                $paramName = $paramNames[1][0];

                $this->routes[$route]['params'][$paramName] = $matches[0];
                return $this->routes[$route];
            }
        }

        return null;
    }

    public function getAction(string $uri, string $method)
    {
        $route = $this->createKey($method, $uri);
        return array_key_exists($route, $this->routes) ? $this->routes[$route] : $this->parseUrl($route);
    }

    public static function get(string $uri, string $className, string $action, )
    {
        return self::$instance->registerRoute($uri, $className, $action, 'GET');
    }

    public static function post(string $uri, string $className, string $action)
    {
        return self::$instance->registerRoute($uri, $className, $action, 'POST');
    }

    public static function put(string $uri, string $className, string $action)
    {
        return self::$instance->registerRoute($uri, $className, $action, 'PUT');
    }

    public static function patch(string $uri, string $className, string $action)
    {
        return self::$instance->registerRoute($uri, $className, $action, 'PATCH');
    }

    public static function delete(string $uri, string $className, string $action)
    {
        return self::$instance->registerRoute($uri, $className, $action, 'DELETE');
    }

    public function middlewares($middlewares)
    {
        $this->routes[$this->selectedRoute]['middlewares'] = is_array($middlewares) ? $middlewares : [$middlewares];
    }
}
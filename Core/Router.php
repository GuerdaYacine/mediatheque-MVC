<?php

namespace Core;

class Router
{
    protected $routes = [];
    protected $database;

    public function __construct($database = null)
    {
        $this->database = $database;
    }

    public function get($uri, $controller)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => 'GET'
        ];
    }

    public function post($uri, $controller)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => 'POST'
        ];
    }

    public function route($uri, $method)
    {
        foreach ($this->routes as $route) {
            $routeMethod = strtoupper($route['method'] ?? '');
            $routeTarget = $route['controller'] ?? '';

            $pattern = preg_replace('#\{[\w]+\}#', '([\w-]+)', $route['uri']);
            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $uri, $matches) && $routeMethod === strtoupper($method)) {
                array_shift($matches);
                $params = $matches;

                if (strpos($routeTarget, '@') !== false) {
                    [$class, $action] = explode('@', $routeTarget);

                    $controller = $this->createControllerWithDependencies($class);
                    return call_user_func_array([$controller, $action], $params);
                }
            }
        }

        $this->abort();
    }

    protected function createControllerWithDependencies($class)
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type && !$type->isBuiltin()) {
                $dependencyClass = $type->getName();

                if (class_exists($dependencyClass)) {
                    $dependencies[] = new $dependencyClass($this->database);
                } else {
                    throw new \Exception("Dependency class {$dependencyClass} not found");
                }
            }
        }

        return new $class(...$dependencies);
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        $errorPath = __DIR__ . "/../Views/{$code}.php";
        if (file_exists($errorPath)) {
            require $errorPath;
        } else {
            echo "Error {$code}";
        }
        die();
    }
}

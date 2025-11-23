<?php
// FILE: /app/core/Router.php

class Router {
    private $routes = array();
    private $namedRoutes = array();

    public function get($pattern, $callback, $name = null) {
        $this->addRoute('GET', $pattern, $callback, $name);
    }

    public function post($pattern, $callback, $name = null) {
        $this->addRoute('POST', $pattern, $callback, $name);
    }

    public function put($pattern, $callback, $name = null) {
        $this->addRoute('PUT', $pattern, $callback, $name);
    }

    public function delete($pattern, $callback, $name = null) {
        $this->addRoute('DELETE', $pattern, $callback, $name);
    }

    public function any($pattern, $callback, $name = null) {
        $this->addRoute('ANY', $pattern, $callback, $name);
    }

    private function addRoute($method, $pattern, $callback, $name = null) {
        $this->routes[] = array(
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        );

        if ($name) {
            $this->namedRoutes[$name] = $pattern;
        }
    }

    public function dispatch() {
        $uri = Request::uri();
        $method = Request::method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }

            $pattern = '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['pattern']) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return $this->executeCallback($route['callback'], $params);
            }
        }

        Response::notFound();
    }

    private function executeCallback($callback, $params) {
        if (is_string($callback)) {
            $parts = explode('@', $callback);
            $controllerName = $parts[0];
            $methodName = isset($parts[1]) ? $parts[1] : 'index';

            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

            if (!file_exists($controllerFile)) {
                throw new Exception("Controller file not found: $controllerFile");
            }

            require_once $controllerFile;

            if (!class_exists($controllerName)) {
                throw new Exception("Controller class not found: $controllerName");
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method $methodName not found in controller $controllerName");
            }

            return call_user_func_array(array($controller, $methodName), $params);
        }

        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        throw new Exception("Invalid route callback");
    }

    public function route($name, $params = array()) {
        if (!isset($this->namedRoutes[$name])) {
            return '#';
        }

        $pattern = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $pattern = str_replace('{' . $key . '}', $value, $pattern);
        }

        return $pattern;
    }
}

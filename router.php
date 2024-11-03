<?php

require_once __DIR__ . '/app/controllers/BlogController.php';
require_once __DIR__ . '/app/controllers/UserController.php';

class Router {
    private $routes = [];
    private $blogController;
    private $userController;

    public function __construct() {
        $this->blogController = new BlogController();
        $this->userController = new UserController();

        $this->registerRoutes();
    }

    private function registerRoutes() {
        // Blog routes
        $this->addRoute('GET', '/api/blogs', [$this->blogController, 'index']);
        $this->addRoute('POST', '/api/blogs', [$this->blogController, 'store']);
        $this->addRoute('GET', '/api/blogs/{id}', [$this->blogController, 'show']);
        $this->addRoute('PUT', '/api/blogs/{id}', [$this->blogController, 'update']);
        $this->addRoute('DELETE', '/api/blogs/{id}', [$this->blogController, 'destroy']);

        // User routes
        $this->addRoute('POST', '/api/register', [$this->userController, 'register']);
        $this->addRoute('POST', '/api/login', [$this->userController, 'login']);
        $this->addRoute('GET', '/api/users/{id}', [$this->userController, 'getUserById']);
        $this->addRoute('GET', '/api/users/username/{username}', [$this->userController, 'getUserByUsername']);
        $this->addRoute('GET', '/api/users', [$this->userController, 'getAllUsers']);
    }

    private function addRoute($method, $path, $handler) {
        $this->routes[$method][$path] = $handler;
    }

    public function route($request) {
        $requestPath = strtok($request, '?');
        $basePath = '/blogs-api';

        if (strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
        }

        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] as $route => $handler) {
            $routePattern = preg_replace('/\{(\w+)\}/', '(\w+)', $route);
            $routePattern = str_replace('/', '\/', $routePattern);

            if (preg_match('/^' . $routePattern . '$/', $requestPath, $matches)) {
                array_shift($matches); // Remove full match from results
                $this->executeHandler($handler, $matches);
                return;
            }
        }

        echo json_encode(['error' => 'API Endpoint Not Found']);
    }

    private function executeHandler($handler, $params) {
        call_user_func_array($handler, $params);
    }
}

<?php

require_once __DIR__ . '/app/controllers/BlogController.php';
require_once __DIR__ . '/app/controllers/UserController.php';

class Router {
    private $blogController;
    private $userController;

    public function __construct() {
        $this->blogController = new BlogController();
        $this->userController = new UserController();
    }

    public function route($request) {
        $requestPath = strtok($request, '?');
        $basePath = '/blogs-api';

        if (strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
        }

        if (strpos($requestPath, '/api/blogs') === 0) {
            $this->handleBlogs($requestPath);
        } elseif ($requestPath === '/api/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->userController->register();
        } elseif ($requestPath === '/api/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->userController->login();
        } elseif (preg_match('/^\/api\/users\/(\d+)$/', $requestPath, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->userController->getUserById($matches[1]);
        } elseif (preg_match('/^\/api\/users\/username\/(.+)$/', $requestPath, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->userController->getUserByUsername($matches[1]);
        } elseif ($requestPath === '/api/users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->userController->getAllUsers();
        } else {
            echo json_encode(['error' => 'API Endpoint Not Found']);
        }
    }

    private function handleBlogs($requestPath) {
        $parts = explode('/', $requestPath);
        $id = isset($parts[3]) ? intval($parts[3]) : null;

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if ($id) {
                    $this->blogController->show($id);
                } else {
                    $this->blogController->index();
                }
                break;

            case 'POST':
                $this->blogController->store();
                break;

            case 'PUT':
            case 'PATCH':
                if ($id) {
                    $this->blogController->update($id);
                } else {
                    echo json_encode(['error' => 'ID not provided for update']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $this->blogController->destroy($id);
                } else {
                    echo json_encode(['error' => 'ID not provided for deletion']);
                }
                break;

            default:
                echo json_encode(['error' => 'Request method not supported']);
                break;
        }
    }
}

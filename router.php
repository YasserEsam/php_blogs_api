<?php

require_once __DIR__ . '/app/controllers/BlogController.php';

class Router {
    private $controller;

    public function __construct() {
        $this->controller = new BlogController();
    }

    public function route($request) {
        $requestPath = strtok($request, '?');

        // Update the base path to remove index.php
        $basePath = '/blogs-api';
        if (strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
        }

        if (strpos($requestPath, '/api/blogs') === 0) {
            $this->handleBlogs($requestPath);
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
                    $this->controller->show($id);
                } else {
                    $this->controller->index();
                }
                break;

            case 'POST':
                $this->controller->store();
                break;

            case 'PUT':
            case 'PATCH':
                if ($id) {
                    $this->controller->update($id);
                } else {
                    echo json_encode(['error' => 'ID not provided for update']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $this->controller->destroy($id);
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

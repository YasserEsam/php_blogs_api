<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/JWTHandler.php';

class UserController {
    private $db;
    private $user;
    private $jwt;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->user = new User($this->db);
        $this->jwt = new JWTHandler();
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->username, $data->password, $data->role, $data->email)) {
            $this->user->username = $data->username;
            $this->user->password = password_hash($data->password, PASSWORD_DEFAULT);
            $this->user->role = $data->role;
            $this->user->email = $data->email;

            if ($this->user->createUser()) {
                $this->sendResponse(['message' => 'User registered successfully']);
            } else {
                $this->sendResponse(['message' => 'User registration failed'], 500);
            }
        } else {
            $this->sendResponse(['message' => 'Invalid input'], 400);
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->username, $data->password)) {
            $userData = $this->user->findUserByUsername(htmlspecialchars(strip_tags($data->username)));

            if ($userData && password_verify($data->password, $userData['password'])) {
                $token = $this->jwt->generateToken(['id' => $userData['id'], 'role' => $userData['role']]);
                $this->sendResponse(['token' => $token]);
            } else {
                $this->sendResponse(['message' => 'Login failed'], 401);
            }
        } else {
            $this->sendResponse(['message' => 'Invalid input'], 400);
        }
    }

    public function getUserById($id) {
        $userData = $this->user->findUserById($id);
        if ($userData) {
            $this->sendResponse($userData);
        } else {
            $this->sendResponse(['message' => 'User not found'], 404);
        }
    }

    public function getUserByUsername($username) {
        $userData = $this->user->findUserByUsername($username);
        if ($userData) {
            $this->sendResponse($userData);
        } else {
            $this->sendResponse(['message' => 'User not found'], 404);
        }
    }

    public function getAllUsers() {
        $users = $this->user->findAllUsers();
        $this->sendResponse($users);
    }

    private function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

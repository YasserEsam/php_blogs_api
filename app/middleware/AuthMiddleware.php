<?php

require_once __DIR__ . '/../helpers/JWT.php';

class AuthMiddleware {
    private $jwt;

    public function __construct() {
        $this->jwt = new JWTHandler();
    }

    public function verifyRole($requiredRole) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $decoded = $this->jwt->verifyToken($token);
            if ($decoded && $decoded->data->role === $requiredRole) {
                return true;
            }
        }
        echo json_encode(['message' => 'Access Denied'], 403);
        return false;
    }
}

<?php

require_once './vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JWTHandler {
    private $secretKey = 'Yasser';

    public function generateToken($data) {
        $payload = [
            'iss' => "localhost",
            'iat' => time(),
            'exp' => time() + (60 * 60), // Token expiration in 1 hour
            'data' => $data
        ];
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function verifyToken($jwt) {
        try {
            return JWT::decode($jwt, $this->secretKey, ['HS256']);
        } catch (Exception $e) {
            return null;
        }
    }
}

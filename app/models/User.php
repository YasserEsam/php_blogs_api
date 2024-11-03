<?php

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $password;
    public $role;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function findUserByUsername($username) {
        $query = "SELECT * FROM {$this->table} WHERE username LIKE :username";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$username}%";
        $stmt->bindParam(':username', $searchTerm);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findUserById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAllUsers() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser() {
        $query = "INSERT INTO {$this->table} (username, password, role, email) VALUES (:username, :password, :role, :email)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':email', $this->email);

        return $stmt->execute();
    }
}

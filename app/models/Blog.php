<?php

class Blog {
    private $conn;
    private $table = 'blogs';

    public $id;
    public $title;
    public $content;
    public $author;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

  

    public function getBlogById($id) {
        $query = "SELECT id, title, content, author, created_at FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt;
    }

  public function getBlogsPaginated($limit, $offset) {
        $query = "SELECT id, title, content, author, created_at FROM {$this->table} LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    
    public function createBlog() {
        $query = "INSERT INTO {$this->table} (title, content, author) VALUES (:title, :content, :author)";
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->author = htmlspecialchars(strip_tags($this->author));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':author', $this->author);

        return $stmt->execute();
    }

    public function updateBlog() {
        $query = "UPDATE {$this->table} SET title = :title, content = :content, author = :author WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function deleteBlog() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}

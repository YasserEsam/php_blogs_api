<?php

require_once __DIR__ . '/../models/Blog.php';
require_once __DIR__ . '/../../config/database.php';

class BlogController {
    private $db;
    private $blog;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->blog = new Blog($this->db);
    }

    public function index() {
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $result = $this->blog->getBlogsPaginated($limit, $offset);
        $blogs = $result->fetchAll(PDO::FETCH_ASSOC);

        $this->sendResponse([
            'data' => $blogs,
            'meta' => [
                'limit' => $limit,
                'page' => $page
            ]
        ]);
    }

    public function show($id) {
        $result = $this->blog->getBlogById($id);
        $blog = $result->fetch(PDO::FETCH_ASSOC);
        if ($blog) {
            $this->sendResponse($blog);
        } else {
            $this->sendResponse(['message' => 'Blog not found'], 404);
        }
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->title, $data->content, $data->author)) {
            $this->blog->title = $data->title;
            $this->blog->content = $data->content;
            $this->blog->author = $data->author;

            if ($this->blog->createBlog()) {
                $this->sendResponse(['message' => 'Blog Created'], 201);
            } else {
                $this->sendResponse(['message' => 'Blog Not Created'], 500);
            }
        } else {
            $this->sendResponse(['message' => 'Invalid Input'], 400);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->title, $data->content, $data->author)) {
            $this->blog->id = $id;
            $this->blog->title = $data->title;
            $this->blog->content = $data->content;
            $this->blog->author = $data->author;

            if ($this->blog->updateBlog()) {
                $this->sendResponse(['message' => 'Blog Updated']);
            } else {
                $this->sendResponse(['message' => 'Blog Not Updated'], 500);
            }
        } else {
            $this->sendResponse(['message' => 'Invalid Input'], 400);
        }
    }

    public function destroy($id) {
        $this->blog->id = $id;

        if ($this->blog->deleteBlog()) {
            $this->sendResponse(['message' => 'Blog Deleted']);
        } else {
            $this->sendResponse(['message' => 'Blog Not Deleted'], 500);
        }
    }

    public function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
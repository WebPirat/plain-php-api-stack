<?php

require_once('router.php');
require_once('database.php');

$router = new Router();

// GET /blog
$router->get('/blog', function() use ($db) {
    $blogPosts = $db->getBlogPosts();
    header('Content-Type: application/json');
    echo json_encode($blogPosts);
});

// GET /blog/:id
$router->get('/blog/:id', function($id) use ($db) {
    $blogPost = $db->getBlogPost($id);
    if ($blogPost) {
        header('Content-Type: application/json');
        echo json_encode($blogPost);
    } else {
        header('HTTP/1.1 404 Not Found');
        echo 'Blog post not found.';
    }
});

// POST /blog
$router->post('/blog', function() use ($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $db->createBlogPost($data['title'], $data['content']);
    header('HTTP/1.1 201 Created');
    header('Content-Type: application/json');
    echo json_encode(['id' => $id]);
});

// PUT /blog/:id
$router->put('/blog/:id', function($id) use ($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    $success = $db->updateBlogPost($id, $data['title'], $data['content']);
    if ($success) {
        header('HTTP/1.1 204 No Content');
    } else {
        header('HTTP/1.1 404 Not Found');
        echo 'Blog post not found.';
    }
});

// DELETE /blog/:id
$router->delete('/blog/:id', function($id) use ($db) {
    $success = $db->deleteBlogPost($id);
    if ($success) {
        header('HTTP/1.1 204 No Content');
    } else {
        header('HTTP/1.1 404 Not Found');
        echo 'Blog post not found.';
    }
});

?>
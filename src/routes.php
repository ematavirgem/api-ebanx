<?php

use App\Controllers\EventController;

try {
    $controller = new EventController();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_SERVER['REQUEST_URI'] === '/reset') {
            $controller->reset();
        } elseif ($_SERVER['REQUEST_URI'] === '/event') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->event($data);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/\/balance\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
        $controller->balance($matches[1]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);
}
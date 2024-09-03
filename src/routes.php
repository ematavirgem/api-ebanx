<?php

use App\Controllers\EventController;

try {
    $controller = new EventController();

    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $response = match ($method) {
        'POST' => match ($uri) {
            '/reset' => $controller->reset(),
            '/event' => $controller->event(json_decode(file_get_contents('php://input'), true)),
            default => fn() => [404, ['error' => 'Not Found'], true],
        },
        'GET' => match (true) {
            strpos($uri, '/balance') === 0 => function() use ($controller) {
                $accountId = $_GET['account_id'] ?? null;
                if ($accountId !== null) {
                    return $controller->balance($accountId);
                }
                return [400, ['error' => 'Missing account_id in query parameters', true]];
            },
            default => fn() => [404, ['error' => 'Not Found'], true],
        },
        default => fn() => [404, 0, false],
    };

    if (is_callable($response)) {
        $response = $response();
    }

    if (is_array($response)) {
        [$statusCode, $data, $json] = $response;
        http_response_code($statusCode);
        echo $json === true ? json_encode($data) : $data;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);
}

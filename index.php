<?php

session_start();

require_once 'database/Database.php';
require_once 'controllers/BalanceController.php';
require_once 'controllers/EventController.php';

$db = new Database();
$balanceController = new BalanceController($db);
$eventController = new EventController($db);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');
header('Content-Security-Policy: default-src \'self\';');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Autenticação com um token de API
function authenticate() {
    $apiToken = $_SERVER['HTTP_X_API_TOKEN'] ?? '';

    // Em um cenário real, confirmar esse token contra um banco de dados ou outra fonte segura
    $validToken = 'your-secure-token';

    if ($apiToken !== $validToken) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
}

authenticate();

// Rate limiting
function rateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "rate_limit_$ip";
    $limit = 100; // Limite de 100 requisições
    $window = 60 * 10; // Em 10 minutos

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'start' => time()];
    }

    $data = $_SESSION[$key];

    if ($data['start'] + $window < time()) {
        // Redefinir o limite de taxa
        $data = ['count' => 0, 'start' => time()];
    }

    if ($data['count'] >= $limit) {
        http_response_code(429);
        echo json_encode(['error' => 'Too many requests']);
        exit();
    }

    $data['count']++;
    $_SESSION[$key] = $data;
}

rateLimit();

if ($method === 'GET' && $uri === '/balance') {
    $accountId = $_GET['account_id'] ?? null;
    if ($accountId) {
        $balanceController->getBalance($accountId);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Account ID is required']);
    }
} elseif ($method === 'POST' && $uri === '/event') {
    // Proteção contra CSRF
    $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    function checkCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
    }

    if (!checkCsrfToken($csrfToken)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit();
    }

    $event = json_decode(file_get_contents('php://input'), true);
    if ($event) {
        $eventController->handleEvent($event);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}

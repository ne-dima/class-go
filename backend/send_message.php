<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// JWT проверка
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Отсутствует токен']);
    exit;
}

$token = str_replace('Bearer ', '', $authHeader);
try {
    $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $payload->sub;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Неверный токен']);
    exit;
}

// Чтение JSON
$data = json_decode(file_get_contents('php://input'), true);
$courseId = $data['course_id'] ?? null;
$text = trim($data['text'] ?? '');

if (!$courseId || $text === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Неверные данные']);
    exit;
}

try {
    $pdo = getConnection();

    $stmt = $pdo->prepare("INSERT INTO messages (user_id, course_id, text, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $courseId, $text]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных', 'details' => $e->getMessage()]);
}

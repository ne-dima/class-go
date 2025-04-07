<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

// Авторизация
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Требуется авторизация']);
    exit;
}

$token = substr($authHeader, 7);

try {
    $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $payload->sub;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Неверный токен']);
    exit;
}

// Получаем данные
$data = json_decode(file_get_contents("php://input"), true);
$messageId = $data['message_id'] ?? null;

if (!$messageId) {
    http_response_code(400);
    echo json_encode(['error' => 'Не передан ID сообщения']);
    exit;
}

try {
    $pdo = getConnection();

    // Проверим, что сообщение принадлежит текущему пользователю
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ? AND user_id = ?");
    $stmt->execute([$messageId, $userId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        http_response_code(403);
        echo json_encode(['error' => 'Вы не можете удалить это сообщение']);
        exit;
    }

    // Удаляем сообщение
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$messageId]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера', 'details' => $e->getMessage()]);
}

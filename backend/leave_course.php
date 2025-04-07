<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Не передан токен']);
    exit;
}

$token = substr($authHeader, 7);

try {
    $pdo = getConnection();
    $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $payload->sub;

    $input = json_decode(file_get_contents('php://input'), true);
    $courseId = $input['course_id'] ?? null;

    if (!$courseId) {
        http_response_code(400);
        echo json_encode(['error' => 'Не указан course_id']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM course_members WHERE course_id = ? AND user_id = ?");
    $stmt->execute([$courseId, $userId]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера', 'details' => $e->getMessage()]);
}

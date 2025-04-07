<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
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

    $courseId = $_GET['id'] ?? null;
    if (!$courseId) {
        http_response_code(400);
        echo json_encode(['error' => 'Не указан ID курса']);
        exit;
    }

    // Проверка: владелец курса?
    $stmt = $pdo->prepare("SELECT teacher_id FROM courses WHERE id = ?");
    $stmt->execute([$courseId]);
    $ownerId = $stmt->fetchColumn();

    if ($ownerId != $userId) {
        http_response_code(403);
        echo json_encode(['error' => 'Вы не являетесь владельцем курса']);
        exit;
    }

    // Удаление: сначала связи, потом курс
    $pdo->beginTransaction();

    $pdo->prepare("DELETE FROM course_members WHERE course_id = ?")->execute([$courseId]);
    $pdo->prepare("DELETE FROM assignments WHERE course_id = ?")->execute([$courseId]);
    $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([$courseId]);

    $pdo->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера', 'details' => $e->getMessage()]);
}

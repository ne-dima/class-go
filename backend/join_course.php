<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['message' => 'Необходим токен']);
    exit;
}

$token = substr($authHeader, 7);

try {
    $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $payload->sub;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['message' => 'Неверный токен']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$code = $data['code'] ?? null;

if (!$code) {
    http_response_code(400);
    echo json_encode(['message' => 'Код курса не передан']);
    exit;
}

try {
    $pdo = getConnection();

    // Найти курс по коду
    $stmt = $pdo->prepare("SELECT id FROM courses WHERE code = ?");
    $stmt->execute([$code]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        http_response_code(404);
        echo json_encode(['message' => 'Курс с таким кодом не найден']);
        exit;
    }

    // Проверить, не присоединился ли уже
    $stmt = $pdo->prepare("SELECT * FROM course_members WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$userId, $course['id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['alreadyJoined' => true]);
        exit;
    }

    // Присоединить к курсу
    $stmt = $pdo->prepare("INSERT INTO course_members (user_id, course_id, role) VALUES (?, ?, 'student')");
    $stmt->execute([$userId, $course['id']]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Ошибка сервера',
        'error' => $e->getMessage()
      ]);
}

<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Получение токена из заголовков
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Отсутствует токен']);
    exit;
}

$token = substr($authHeader, 7);

// Проверка и декод токена
try {
    $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $payload->sub;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Невалидный токен', 'details' => $e->getMessage()]);
    exit;
}

// Получение данных из тела запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный формат данных']);
    exit;
}

$title = trim($data['title'] ?? '');
$group = trim($data['group'] ?? '');
$description = trim($data['description'] ?? '');
$resources = trim($data['resources'] ?? '');

if (!$title || !$group) {
    http_response_code(400);
    echo json_encode(['error' => 'Название и группа обязательны']);
    exit;
}

try {
    $pdo = getConnection();

    // Генерация уникального кода курса
    do {
        $code = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE code = ?");
        $stmt->execute([$code]);
    } while ($stmt->fetch());

    // Вставка курса
    $stmt = $pdo->prepare("
        INSERT INTO courses (title, group_name, description, resources, code, teacher_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $group, $description, $resources, $code, $userId]);
    $courseId = $pdo->lastInsertId();

    // Добавление преподавателя как участника
    $stmt = $pdo->prepare("
        INSERT INTO course_members (course_id, user_id, role)
        VALUES (?, ?, 'teacher')
    ");
    $stmt->execute([$courseId, $userId]);

    echo json_encode(['success' => true, 'course_id' => $courseId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Ошибка сервера',
        'details' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

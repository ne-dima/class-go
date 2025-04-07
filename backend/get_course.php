<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Проверка токена
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
    echo json_encode(['error' => 'Неверный токен', 'details' => $e->getMessage()]);
    exit;
}

// Получение ID курса из параметров запроса
$courseId = $_GET['id'] ?? null;
if (!$courseId || !is_numeric($courseId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный ID курса']);
    exit;
}

try {
    $pdo = getConnection();

    // 1. Получение основной информации о курсе
    $stmt = $pdo->prepare("
        SELECT 
            c.id, 
            c.title, 
            c.group_name, 
            c.code,
            c.description,
            c.resources,
            c.created_at,
            u.id AS teacher_id,
            u.name,
            u.surname,
            u.patronymic,
            u.avatar_url AS teacher_avatar
        FROM courses c
        JOIN users u ON c.teacher_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        http_response_code(404);
        echo json_encode(['error' => 'Курс не найден']);
        exit;
    }

    // 2. Получение участников курса
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.name,
            u.surname,
            u.patronymic,
            u.avatar_url,
            cm.role
        FROM course_members cm
        JOIN users u ON cm.user_id = u.id
        WHERE cm.course_id = ?
        ORDER BY 
            CASE WHEN cm.role = 'teacher' THEN 0 ELSE 1 END,
            u.surname
    ");
    $stmt->execute([$courseId]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Получение сообщений курса
    $stmt = $pdo->prepare("
        SELECT 
            m.id,
            m.text,
            m.created_at,
            u.name,
            u.surname,
            u.patronymic,
            u.avatar_url
        FROM messages m
        JOIN users u ON m.user_id = u.id
        WHERE m.course_id = ?
        ORDER BY m.created_at DESC
        LIMIT 50
    ");
    $stmt->execute([$courseId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Проверка, является ли пользователь участником курса
    $stmt = $pdo->prepare("
        SELECT role FROM course_members 
        WHERE course_id = ? AND user_id = ?
    ");
    $stmt->execute([$courseId, $userId]);
    $membership = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$membership) {
        http_response_code(403);
        echo json_encode(['error' => 'Доступ запрещен. Вы не участник курса']);
        exit;
    }

    // Формирование ответа
    echo json_encode([
        'success' => true,
        'course' => $course,
        'students' => $students,
        'messages' => $messages,
        'user_role' => $membership['role']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Ошибка сервера',
        'details' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
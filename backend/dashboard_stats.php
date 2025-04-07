<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


// Проверка JWT токена
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Отсутствует токен']);
    exit;
}

$token = str_replace('Bearer ', '', $authHeader);

try {
    $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $payload->sub;

    $pdo = getConnection();

    // Количество курсов, на которые записан пользователь
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM course_members WHERE user_id = ?");
    $stmt->execute([$userId]);
    $courses = (int) $stmt->fetchColumn();

    // Если пользователь не состоит ни в одном курсе
    if ($courses === 0) {
        echo json_encode([
            'grade' => null,
            'completed' => 0,
            'total' => 0,
            'pending' => 0,
            'courses' => 0
        ]);
        exit;
    }

    // Количество всех заданий по курсам пользователя
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM assignments 
        WHERE course_id IN (
            SELECT course_id FROM course_members WHERE user_id = ?
        )
    ");
    $stmt->execute([$userId]);
    $total = (int) $stmt->fetchColumn();

    // Количество выполненных заданий (есть оценки)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM grades 
        WHERE student_id = ?
    ");
    $stmt->execute([$userId]);
    $completed = (int) $stmt->fetchColumn();

    // Средняя оценка
    $stmt = $pdo->prepare("
        SELECT AVG(grade) FROM grades WHERE student_id = ?
    ");
    $stmt->execute([$userId]);
    $avg = $stmt->fetchColumn();
    $avg = $avg !== null ? round((float) $avg) : null;

    $pending = max(0, $total - $completed);

    echo json_encode([
        'grade' => $avg,
        'completed' => $completed,
        'total' => $total,
        'pending' => $pending,
        'courses' => $courses
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Ошибка авторизации', 'details' => $e->getMessage()]);
}

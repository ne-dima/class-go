<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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

    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $role = $stmt->fetchColumn();

    if (!$role) {
        http_response_code(403);
        echo json_encode(['error' => 'Пользователь не найден']);
        exit;
    }

    if ($role === 'teacher') {
        $stmt = $pdo->prepare("
            SELECT c.*, u.name AS teacher_name, u.surname AS teacher_surname, u.patronymic AS teacher_patronymic
            FROM courses c
            JOIN users u ON c.teacher_id = u.id
            WHERE c.teacher_id = ?
        ");
        $stmt->execute([$userId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT c.*, u.name AS teacher_name, u.surname AS teacher_surname, u.patronymic AS teacher_patronymic
            FROM courses c
            JOIN users u ON c.teacher_id = u.id
            JOIN course_members cm ON c.id = cm.course_id
            WHERE cm.user_id = ?
        ");
        $stmt->execute([$userId]);
    }

    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($courses)) {
        echo json_encode(['success' => true, 'courses' => []]);
        exit;
    }

    foreach ($courses as &$course) {
        $course['fullName'] = $course['teacher_surname'] . ' ' .
            mb_substr($course['teacher_name'], 0, 1) . '.' .
            mb_substr($course['teacher_patronymic'], 0, 1) . '.';
        $course['teacher'] = $course['teacher_surname'] . ' ' . $course['teacher_name'];
        $course['color'] = '#' . substr(md5($course['code']), 0, 6);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM assignments WHERE course_id = ?");
        $stmt->execute([$course['id']]);
        $total = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM assignment_submissions s
            JOIN assignments a ON s.assignment_id = a.id
            WHERE a.course_id = ? AND s.user_id = ?
        ");
        $stmt->execute([$course['id'], $userId]);
        $done = (int)$stmt->fetchColumn();

        $course['progress'] = $total > 0 ? round($done / $total * 100) : null;
    }

    echo json_encode(['success' => true, 'courses' => $courses]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера', 'details' => $e->getMessage()]);
}

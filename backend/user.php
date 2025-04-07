<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Загрузка .env переменных
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing or invalid token']);
    exit;
}

$token = substr($authHeader, 7);

try {
    $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $decoded->sub;

    $pdo = getConnection();

    $stmt = $pdo->prepare('
        SELECT id, name, surname, patronymic, email, role, group_name, university, vk, avatar_url
        FROM users
        WHERE id = ?
    ');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'id' => $user['id'],
            'name' => $user['name'],
            'surname' => $user['surname'],
            'patronymic' => $user['patronymic'],
            'email' => $user['email'],
            'role' => $user['role'],
            'group_name' => $user['group_name'],
            'university' => $user['university'],
            'vk' => $user['vk'],
            'avatar_url' => $user['avatar_url']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Token decode failed',
        'message' => $e->getMessage()
    ]);
}

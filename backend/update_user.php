<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Загружаем .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

// Проверка токена
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$token = substr($authHeader, 7);

try {
    $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    $userId = $decoded->sub;

    $pdo = getConnection();

    // Получаем данные из тела запроса
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $data['name'] ?? '';
    $surname = $data['surname'] ?? '';
    $patronymic = $data['patronymic'] ?? '';
    $group_name = $data['group_name'] ?? '';
    $university = $data['university'] ?? '';
    $vk = $data['vk'] ?? '';

    // Обновление данных в БД
    $stmt = $pdo->prepare("UPDATE users SET name = ?, surname = ?, patronymic = ?, group_name = ?, university = ?, vk = ? WHERE id = ?");
    $stmt->execute([$name, $surname, $patronymic, $group_name, $university, $vk, $userId]);

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Token error or DB error',
        'message' => $e->getMessage()
    ]);
}

<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

// ⬅️ ВАЖНО: получить подключение к БД
$pdo = getConnection();

// Получаем данные из JSON
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing credentials']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Проверка пароля
if ($user && password_verify($password, $user['password_hash'])) {
    $payload = [
        'sub' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'exp' => time() + $_ENV['JWT_EXPIRE']
    ];

    $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    echo json_encode([
        'token' => $token,
        'name' => $user['name'],
        'avatar_url' => $user['avatar_url'],
        'role' => $user['role']
    ]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid email or password']);
}

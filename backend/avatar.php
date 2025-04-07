<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

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

    $data = json_decode(file_get_contents('php://input'), true);
    $base64 = $data['avatar'] ?? null;

    if (!$base64 || !str_starts_with($base64, 'data:image/')) {
        http_response_code(400);
        echo json_encode(['error' => 'Неверный формат изображения']);
        exit;
    }

    $folder = __DIR__ . '/uploads/';
    if (!file_exists($folder)) mkdir($folder, 0777, true);

    $ext = explode('/', mime_content_type($base64))[1];
    $filename = uniqid('avatar_', true) . '.' . $ext;
    $filePath = $folder . $filename;

    $imageData = explode(',', $base64)[1];
    file_put_contents($filePath, base64_decode($imageData));

    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/uploads/' . $filename;

    $pdo = getConnection();
    $stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
    $stmt->execute([$url, $userId]);

    echo json_encode(['success' => true, 'avatar_url' => $url]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Ошибка авторизации']);
}

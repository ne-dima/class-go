<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Отладка: лог входящих данных
file_put_contents('php://stderr', print_r($data, true));

$surname = trim($data['surname'] ?? '');
$name = trim($data['name'] ?? '');
$patronymic = trim($data['patronymic'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';
$group = $data['group'] ?? null;
$university = trim($data['university'] ?? '');

if (!$surname || !$name || !$email || !$password || !$role) {
    http_response_code(400);
    echo json_encode(['error' => 'Обязательные поля не заполнены']);
    exit;
}

try {
    $pdo = getConnection();

    // Проверка на уникальность email
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Пользователь с таким email уже существует']);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Генерация аватара
    $initials = urlencode($surname . ' ' . $name);
    $avatarUrl = "https://api.dicebear.com/6.x/initials/svg?seed={$initials}";

    $stmt = $pdo->prepare('
        INSERT INTO users (surname, name, patronymic, email, password_hash, role, group_name, university, avatar_url)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $stmt->execute([
        $surname,
        $name,
        $patronymic,
        $email,
        $passwordHash,
        $role,
        $group,
        $university ?: null,
        $avatarUrl
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Ошибка базы данных',
        'details' => $e->getMessage()
    ]);
}

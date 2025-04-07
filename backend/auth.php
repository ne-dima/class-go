<?php
require 'db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!$auth || !str_starts_with($auth, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing or invalid token']);
    exit;
}

$token = str_replace('Bearer ', '', $auth);

try {
    $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    echo json_encode(['message' => 'Access granted', 'user' => $decoded]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Token is invalid']);
}

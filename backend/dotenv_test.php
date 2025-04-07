<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "📦 Проверка Composer и Dotenv<br>";

require_once __DIR__ . '/vendor/autoload.php';

echo "✅ Autoload подключен<br>";

use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "✅ Dotenv работает, переменные загружены!<br>";
    echo "🎯 DB_NAME из .env: " . $_ENV['DB_NAME'];
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}

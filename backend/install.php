<?php
// Данные подключения
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'classgo';

// Подключение к MySQL
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Создание базы, если не существует
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// SQL-дамп (упрощённый пример)
$sql = file_get_contents(__DIR__ . '/../sql/classgo_schema.sql');

// Выполняем SQL-команды
if ($conn->multi_query($sql)) {
    echo "✅ База данных и таблицы успешно созданы.";
} else {
    echo "❌ Ошибка при создании: " . $conn->error;
}

$conn->close();
?>

<?php
// Configuração de conexão com banco de dados
$host = $_ENV['DB_HOST'];// ?? 'db';
$db = $_ENV['DB_NAME'];// 'mini_erp';
$user = $_ENV['DB_USER'];// 'erp';
$pass = $_ENV['DB_PASS'];//'toor';
$charset = 'utf8mb4';
$port = $_ENV['DB_PORT'];// '3306';

$dsn = "mysql:host=$host;dbname=$db;port={$port};charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];


try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int) $e->getCode());
}

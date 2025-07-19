<?php
// Configuração de conexão com banco de dados
$host = 'db';
$db = 'mini_erp';
$user = 'erp';
$pass = 'toor';
$charset = 'utf8mb4';
$port = '3306';

$dsn = "mysql:host=$host;dbname=$db;port={$port};charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];


try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // $pdo = new \PDO(
    //     "mysql:host={$host};dbname={$db};port={$port};charset=utf8",
    //     $user,
    //     $pass,
    //     [
    //         PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    //         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    //         PDO::ATTR_CASE => PDO::CASE_NATURAL
    //     ]
    // );
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int) $e->getCode());
}

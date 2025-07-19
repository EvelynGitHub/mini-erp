<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\EmailService;

// Conexão com o banco (ajuste host/usuário/senha se necessário)
$pdo = new PDO(
    'mysql:host=db;dbname=mini_erp;port=3306;charset=utf8mb4',
    'erp',
    'toor'
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Dispara o email de teste
$service = new EmailService($pdo);
$service->enviarPedido(
    5,                                   // ID do pedido (ajuste se necessário)
    'evelynbrandao15@gmail.com',         // Destinatário
    'Rua ABC, 123',                      // Endereço
    150.00                               // Total
);

echo "Email enviado!\n";

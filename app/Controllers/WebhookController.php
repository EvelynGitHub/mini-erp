<?php

namespace App\Controllers;

use PDO;

class WebhookController
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle()
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        $id = $dados['id'] ?? null;
        $status = $dados['status'] ?? null;

        if (!$id || !$status) {
            http_response_code(400);
            echo "Dados inválidos";
            return;
        }

        if ($status === 'cancelado') {
            // Restaura o estoque
            $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id=?");
            $stmt->execute([$id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE pedidos SET status=? WHERE id=?");
            $stmt->execute([$status, $id]);
        }

        echo "OK";
    }
}

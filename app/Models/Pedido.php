<?php
namespace App\Models;

use PDO;

class Pedido
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function criar(float $subtotal, float $frete, float $total, string $email, string $endereco): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO pedidos (subtotal, frete, total, cliente_email, cliente_endereco, status) 
                                     VALUES (?, ?, ?, ?, ?, 'pendente')");
        $stmt->execute([$subtotal, $frete, $total, $email, $endereco]);
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizarStatus(int $pedidoId, string $status): bool
    {
        $stmt = $this->pdo->prepare("UPDATE pedidos SET status=? WHERE id=?");
        return $stmt->execute([$status, $pedidoId]);
    }

    public function deletar(int $pedidoId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id=?");
        return $stmt->execute([$pedidoId]);
    }

    public function find(int $pedidoId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE id=?");
        $stmt->execute([$pedidoId]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        return $pedido ?: null;
    }

    public function listar(): array
    {
        $sql = "SELECT id, total, status, cliente_endereco as endereco, created_at as criado_em FROM pedidos ORDER BY created_at DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}

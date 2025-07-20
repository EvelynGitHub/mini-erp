<?php
namespace App\Models;

use PDO;

class PedidoItem
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function adicionarItem(int $pedidoId, int $produtoId, int $grupoId, int $quantidade, float $preco): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, grupo_id, quantidade, preco_unitario) 
                                     VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$pedidoId, $produtoId, $grupoId, $quantidade, $preco]);
    }

    public function listarPorPedido(int $pedidoId): array
    {
        $sql = "SELECT i.*, p.nome 
                FROM pedido_itens i
                JOIN produtos p ON p.id = i.produto_id
                WHERE i.pedido_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

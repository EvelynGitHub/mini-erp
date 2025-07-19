<?php
namespace App\Models;

use PDO;

class Estoque
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateQuantidade(int $produtoId, int $quantidade): bool
    {
        $stmt = $this->pdo->prepare("UPDATE estoque SET quantidade = ? WHERE produto_id = ?");
        return $stmt->execute([$quantidade, $produtoId]);
    }

    public function decrementar(int $produtoId, int $quantidade): bool
    {
        $stmt = $this->pdo->prepare("UPDATE estoque SET quantidade = quantidade - ? WHERE produto_id = ?");
        return $stmt->execute([$quantidade, $produtoId]);
    }

    public function getQuantidade(int $produtoId): int
    {
        $stmt = $this->pdo->prepare("SELECT quantidade FROM estoque WHERE produto_id = ?");
        $stmt->execute([$produtoId]);
        return (int) $stmt->fetchColumn();
    }
}

<?php

namespace App\Models;

use PDO;

class Produto
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $sql = "SELECT p.*, e.variacao, e.quantidade
                FROM produtos p
                LEFT JOIN estoque e ON e.produto_id = p.id";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        return (array) $produto ?: null;
    }

    public function create(string $nome, float $preco, ?string $variacao, int $quantidade): int
    {
        $this->pdo->beginTransaction();
        $stmt = $this->pdo->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
        $stmt->execute([$nome, $preco]);
        $produtoId = (int) $this->pdo->lastInsertId();

        $stmtEstoque = $this->pdo->prepare("INSERT INTO estoque (produto_id, variacao, quantidade) VALUES (?, ?, ?)");
        $stmtEstoque->execute([$produtoId, $variacao, $quantidade]);

        $this->pdo->commit();
        return $produtoId;
    }

    public function update(int $id, string $nome, float $preco): bool
    {
        $stmt = $this->pdo->prepare("UPDATE produtos SET nome=?, preco=? WHERE id=?");
        return $stmt->execute([$nome, $preco, $id]);
    }
}

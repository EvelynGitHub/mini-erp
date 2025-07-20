<?php

namespace App\Models;

use PDO;
use PDOException;

class Produto
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retorna todos os produtos com suas informações de estoque e grupo de variação.
     * Futuramente, pode adicionar filtros e/ou paginação.
     * @return array
     */
    public function all(): array
    {
        $sql = "SELECT
                    p.id AS produto_id,
                    p.nome AS produto_nome,
                    p.ativo AS produto_ativo,
                    e.id AS estoque_id,
                    e.quantidade,
                    e.preco,
                    gv.id AS grupo_id,
                    gv.nome AS grupo_nome
                FROM
                    produtos p
                LEFT JOIN
                    estoque e ON e.produto_id = p.id
                LEFT JOIN
                    grupos_variacoes gv ON e.grupo_id = gv.id
                ORDER BY
                    p.nome, gv.nome";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Retorna os dados do produto solicitado caso exista.
     * @return array
     */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        return (array) $produto ?: null;
    }

    /**
     * Cria um novo produto e sua entrada de estoque.
     * @param string $nome Nome do produto.
     * @return int ID do produto criado.
     * @throws PDOException Se a transação falhar.
     */
    public function create(string $nome): int
    {
        $this->pdo->beginTransaction();
        try {
            // Insere o produto
            $stmtProduto = $this->pdo->prepare("INSERT INTO produtos (nome) VALUES (?)");
            $stmtProduto->execute([$nome]);
            $produtoId = (int) $this->pdo->lastInsertId();

            $this->pdo->commit();
            return $produtoId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e; // Relança a exceção para ser tratada no controller
        }
    }

    /**
     * Atualiza um produto.
     * @param int $produtoId ID do produto.
     * @param string $nome Novo nome do produto.
     * @return bool True se a atualização for bem-sucedida, false caso contrário.
     */
    public function update(int $produtoId, string $nome): bool
    {
        $this->pdo->beginTransaction();
        try {
            // Atualiza o nome do produto
            $stmtProduto = $this->pdo->prepare("UPDATE produtos SET nome = ? WHERE id = ?");
            $stmtProduto->execute([$nome, $produtoId]);
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            return false;
        }
    }
}

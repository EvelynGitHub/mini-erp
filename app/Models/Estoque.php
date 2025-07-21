<?php
namespace App\Models;

use PDO;
use PDOException;

class Estoque
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Cria um novo produto e sua entrada de estoque.
     * @param int $produtoId ID do produto.
     * @param float $preco Preço do produto.
     * @param int $quantidade Quantidade em estoque.
     * @param int|null $grupoId ID do grupo de variação (opcional).
     * @return int ID do estoque criado.
     * @throws PDOException Se a transação falhar.
     */
    public function create(int $produtoId, float $preco, int $quantidade, ?int $grupoId): int
    {
        $this->pdo->beginTransaction();
        try {
            // Insere a entrada de estoque
            $stmt = $this->pdo->prepare("INSERT INTO estoque (produto_id, grupo_id, quantidade, preco) VALUES (?, ?, ?, ?)");
            $stmt->execute([$produtoId, $grupoId, $quantidade, $preco]);
            $estoqueId = (int) $this->pdo->lastInsertId();

            $this->pdo->commit();

            return $estoqueId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e; // Relança a exceção para ser tratada no controller
        }
    }

    /**
     * Atualiza entrada de estoque.
     * @param int $estoqueId ID da entrada de estoque.
     * @param float $preco Novo preço da entrada de estoque.
     * @param int $quantidade Nova quantidade da entrada de estoque.
     * @return bool True se a atualização for bem-sucedida, false caso contrário.
     */
    public function update(int $estoqueId, float $preco, int $quantidade): bool
    {
        $this->pdo->beginTransaction();
        try {
            // Atualiza a entrada de estoque
            $stmt = $this->pdo->prepare("UPDATE estoque SET quantidade = ?, preco = ? WHERE id = ?");
            $stmt->execute([$quantidade, $preco, $estoqueId]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao atualizar estoque: " . $e->getMessage());
            return false;
        }
    }

    public function decrementar(int $produtoId, ?int $grupoId, int $quantidade): bool
    {
        if (is_null($grupoId)) {
            $stmt = $this->pdo->prepare("UPDATE estoque SET quantidade = (quantidade - ?) WHERE produto_id = ? AND grupo_id IS NULL");
            return $stmt->execute([$quantidade, $produtoId]);
        }

        $stmt = $this->pdo->prepare("UPDATE estoque SET quantidade = (quantidade - ?) WHERE produto_id = ? AND grupo_id = ?");
        return $stmt->execute([$quantidade, $produtoId, $grupoId]);
    }

    public function getQuantidadePreco(int $produtoId, ?int $grupoId): array
    {
        if (is_null($grupoId)) {
            $stmt = $this->pdo->prepare("SELECT quantidade, preco FROM estoque WHERE produto_id = ? AND grupo_id IS NULL");
            $stmt->execute([$produtoId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT quantidade, preco FROM estoque WHERE produto_id = ? AND grupo_id = ?");
            $stmt->execute([$produtoId, $grupoId]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['quantidade' => 0, 'preco' => 0.0];
    }

    /**
     * Exclui uma entrada de estoque e, se for a última, o produto associado.
     * @param int $estoqueId ID da entrada de estoque a ser excluída.
     * @return bool True se a exclusão for bem-sucedida, false caso contrário.
     */
    public function deleteEstoqueAndProdutoIfLast(int $estoqueId): bool
    {
        $this->pdo->beginTransaction();
        try {
            // Pega o produto_id antes de deletar a entrada de estoque
            $stmt = $this->pdo->prepare("SELECT produto_id FROM estoque WHERE id = ?");
            $stmt->execute([$estoqueId]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                $this->pdo->rollBack();
                return false; // Entrada de estoque não encontrada
            }
            $produtoId = $produto['produto_id'];

            // Deleta a entrada de estoque
            $stmtDeleteEstoque = $this->pdo->prepare("DELETE FROM estoque WHERE id = ?");
            $stmtDeleteEstoque->execute([$estoqueId]);

            // Verifica se este produto tem mais alguma entrada de estoque
            $stmtCountEstoque = $this->pdo->prepare("SELECT COUNT(*) FROM estoque WHERE produto_id = ?");
            $stmtCountEstoque->execute([$produtoId]);
            $count = $stmtCountEstoque->fetchColumn();

            // Se não houver mais entradas de estoque para este produto, deleta o produto
            if ($count == 0) {
                $stmtDeleteProduto = $this->pdo->prepare("DELETE FROM produtos WHERE id = ?");
                $stmtDeleteProduto->execute([$produtoId]);
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao excluir estoque/produto: " . $e->getMessage());
            return false;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

class Variacao
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retorna todas as variações.
     * @return array
     */
    public function getAllVariacoes(): array
    {
        $sql = "SELECT id, nome FROM variacoes ORDER BY nome";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna todos os grupos de variações com suas variações associadas.
     * @return array
     */
    public function getAllGruposVariacoes(): array
    {
        $sql = "SELECT gv.id, gv.nome, GROUP_CONCAT(gvv.variacao_id) AS variacao_ids
                FROM grupos_variacoes gv
                LEFT JOIN grupo_variacao_variacao gvv ON gv.id = gvv.grupo_id
                GROUP BY gv.id, gv.nome
                ORDER BY gv.nome";
        $stmt = $this->pdo->query($sql);
        $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Converte a string de variacao_ids para um array de inteiros
        foreach ($grupos as &$grupo) {
            $grupo['variacao_ids'] = $grupo['variacao_ids'] ? array_map('intval', explode(',', $grupo['variacao_ids'])) : [];
        }
        return $grupos;
    }

    /**
     * Cria uma nova variação.
     * @param string $nome Nome da variação.
     * @return int ID da variação criada.
     * @throws PDOException
     */
    public function createVariacao(string $nome): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO variacoes (nome) VALUES (?)");
        $stmt->execute([$nome]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Atualiza uma variação existente.
     * @param int $id ID da variação.
     * @param string $nome Novo nome da variação.
     * @return bool
     */
    public function updateVariacao(int $id, string $nome): bool
    {
        $stmt = $this->pdo->prepare("UPDATE variacoes SET nome = ? WHERE id = ?");
        return $stmt->execute([$nome, $id]);
    }

    /**
     * Exclui uma variação.
     * @param int $id ID da variação.
     * @return bool
     */
    public function deleteVariacao(int $id): bool
    {
        // O ON DELETE CASCADE no DDL de `grupo_variacao_variacao` cuidará das associações.
        // No entanto, se a variação estiver sendo usada em `estoque` ou `pedido_itens`,
        // a exclusão pode falhar devido a restrições de chave estrangeira (se ON DELETE RESTRICT).
        // Para este DDL, `estoque` e `pedido_itens` usam `ON DELETE SET NULL` para `grupo_id`,
        // mas a variação não está diretamente ligada a eles, apenas via `grupo_variacao_variacao`.
        // Se a variação estiver associada a um grupo que, por sua vez, está em estoque,
        // a exclusão da variação não impedirá a exclusão do grupo ou do estoque.
        $stmt = $this->pdo->prepare("DELETE FROM variacoes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Cria um novo grupo de variação e suas associações.
     * @param string $nome Nome do grupo.
     * @param array $variacaoIds IDs das variações a serem associadas.
     * @return int ID do grupo criado.
     * @throws PDOException
     */
    public function createGrupoVariacao(string $nome, array $variacaoIds): int
    {
        $this->pdo->beginTransaction();
        try {
            $stmtGrupo = $this->pdo->prepare("INSERT INTO grupos_variacoes (nome) VALUES (?)");
            $stmtGrupo->execute([$nome]);
            $grupoId = (int) $this->pdo->lastInsertId();

            if (!empty($variacaoIds)) {
                $sql = "INSERT INTO grupo_variacao_variacao (grupo_id, variacao_id) VALUES ";
                $values = [];
                $placeholders = [];
                foreach ($variacaoIds as $variacaoId) {
                    $placeholders[] = "(?, ?)";
                    $values[] = $grupoId;
                    $values[] = $variacaoId;
                }
                $sql .= implode(", ", $placeholders);
                $stmtAssociacao = $this->pdo->prepare($sql);
                $stmtAssociacao->execute($values);
            }

            $this->pdo->commit();
            return $grupoId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Atualiza um grupo de variação e suas associações.
     * @param int $id ID do grupo.
     * @param string $nome Novo nome do grupo.
     * @param array $variacaoIds Novas IDs das variações a serem associadas.
     * @return bool
     * @throws PDOException
     */
    public function updateGrupoVariacao(int $id, string $nome, array $variacaoIds): bool
    {
        $this->pdo->beginTransaction();
        try {
            // Atualiza o nome do grupo
            $stmtGrupo = $this->pdo->prepare("UPDATE grupos_variacoes SET nome = ? WHERE id = ?");
            $stmtGrupo->execute([$nome, $id]);

            // Remove associações antigas
            $stmtDeleteAssoc = $this->pdo->prepare("DELETE FROM grupo_variacao_variacao WHERE grupo_id = ?");
            $stmtDeleteAssoc->execute([$id]);

            // Adiciona novas associações
            if (!empty($variacaoIds)) {
                $sql = "INSERT INTO grupo_variacao_variacao (grupo_id, variacao_id) VALUES ";
                $values = [];
                $placeholders = [];
                foreach ($variacaoIds as $variacaoId) {
                    $placeholders[] = "(?, ?)";
                    $values[] = $id;
                    $values[] = $variacaoId;
                }
                $sql .= implode(", ", $placeholders);
                $stmtAssociacao = $this->pdo->prepare($sql);
                $stmtAssociacao->execute($values);
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao atualizar grupo de variação: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um grupo de variação.
     * @param int $id ID do grupo.
     * @return bool
     */
    public function deleteGrupoVariacao(int $id): bool
    {
        // O ON DELETE CASCADE no DDL de `grupo_variacao_variacao` cuidará das associações.
        // O ON DELETE SET NULL no DDL de `estoque` e `pedido_itens` cuidará das referências ao grupo.
        $stmt = $this->pdo->prepare("DELETE FROM grupos_variacoes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
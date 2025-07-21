<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Variacao;
use PDO;

class VariacaoController
{
    private Variacao $variacaoModel;

    public function __construct(PDO $pdo)
    {
        $this->variacaoModel = new Variacao($pdo);
    }

    /**
     * Exibe a página de gerenciamento de variações e grupos de variações.
     */
    public function index()
    {
        $variacoes = $this->variacaoModel->getAllVariacoes();
        $gruposVariacoes = $this->variacaoModel->getAllGruposVariacoes();

        renderizarView(
            __DIR__ . '/../Views/variacoes.php',
            null,
            [
                'variacoes' => $variacoes,
                'gruposVariacoes' => $gruposVariacoes,
            ]
        );
    }

    /**
     * Lida com a criação de uma nova variação via requisição AJAX (POST).
     */
    public function storeVariacao()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $nome = $input['nome'] ?? null;

        if (!$nome) {
            echo json_encode(['success' => false, 'message' => 'Nome da variação é obrigatório.']);
            return;
        }

        try {
            $id = $this->variacaoModel->createVariacao($nome);
            echo json_encode(['success' => true, 'message' => 'Variação cadastrada com sucesso!', 'id' => $id]);
        } catch (\PDOException $e) {
            error_log("Erro ao cadastrar variação: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar variação.']);
        }
    }

    /**
     * Lida com a atualização de uma variação via requisição AJAX (PUT).
     */
    public function updateVariacao()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id'] ?? null;
        $nome = $input['nome'] ?? null;

        if (!$id || !$nome) {
            echo json_encode(['success' => false, 'message' => 'ID e nome da variação são obrigatórios para atualização.']);
            return;
        }

        if ($this->variacaoModel->updateVariacao((int) $id, $nome)) {
            echo json_encode(['success' => true, 'message' => 'Variação atualizada com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar variação.']);
        }
    }

    /**
     * Lida com a exclusão de uma variação via requisição AJAX (DELETE).
     */
    public function deleteVariacao()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID da variação é obrigatório para exclusão.']);
            return;
        }

        try {
            if ($this->variacaoModel->deleteVariacao((int) $id)) {
                echo json_encode(['success' => true, 'message' => 'Variação excluída com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir variação.']);
            }
        } catch (\PDOException $e) {
            // Pode ocorrer erro se houver restrição de chave estrangeira (ex: se um grupo ainda referencia esta variação)
            error_log("Erro ao excluir variação: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Não foi possível excluir a variação. Verifique se ela está associada a algum grupo.']);
        }
    }

    /**
     * Lida com a criação de um novo grupo de variação via requisição AJAX (POST).
     */
    public function storeGrupoVariacao()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $nome = $input['nome'] ?? null;
        $variacaoIds = $input['variacao_ids'] ?? [];

        if (!$nome) {
            echo json_encode(['success' => false, 'message' => 'Nome do grupo de variação é obrigatório.']);
            return;
        }

        try {
            $id = $this->variacaoModel->createGrupoVariacao($nome, $variacaoIds);
            echo json_encode(['success' => true, 'message' => 'Grupo de variação cadastrado com sucesso!', 'id' => $id]);
        } catch (\PDOException $e) {
            error_log("Erro ao cadastrar grupo de variação: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar grupo de variação.']);
        }
    }

    /**
     * Lida com a atualização de um grupo de variação via requisição AJAX (PUT).
     */
    public function updateGrupoVariacao()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id'] ?? null;
        $nome = $input['nome'] ?? null;
        $variacaoIds = $input['variacao_ids'] ?? [];

        if (!$id || !$nome) {
            echo json_encode(['success' => false, 'message' => 'ID e nome do grupo de variação são obrigatórios para atualização.']);
            return;
        }

        if ($this->variacaoModel->updateGrupoVariacao((int) $id, $nome, $variacaoIds)) {
            echo json_encode(['success' => true, 'message' => 'Grupo de variação atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar grupo de variação.']);
        }
    }

    /**
     * Lida com a exclusão de um grupo de variação via requisição AJAX (DELETE).
     */
    public function deleteGrupoVariacao()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID do grupo de variação é obrigatório para exclusão.']);
            return;
        }

        try {
            if ($this->variacaoModel->deleteGrupoVariacao((int) $id)) {
                echo json_encode(['success' => true, 'message' => 'Grupo de variação excluído com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir grupo de variação.']);
            }
        } catch (\PDOException $e) {
            // Pode ocorrer erro se o grupo estiver sendo usado em estoque ou pedido_itens
            error_log("Erro ao excluir grupo de variação: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Não foi possível excluir o grupo de variação. Verifique se ele está associado a algum produto em estoque ou pedido.']);
        }
    }
}
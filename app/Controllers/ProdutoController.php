<?php

namespace App\Controllers;

use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Variacao;
use PDO;

class ProdutoController
{
    protected Produto $produtoModel;
    protected Estoque $estoqueModel;
    protected Variacao $variacaoModel;

    public function __construct(PDO $pdo)
    {
        $this->produtoModel = new Produto($pdo);
        $this->estoqueModel = new Estoque($pdo);
        $this->variacaoModel = new Variacao($pdo);
    }

    /**
     * Exibe a página de produtos com a lista de produtos e grupos de variações.
     */
    public function index()
    {
        $produtos = $this->produtoModel->all();
        $gruposVariacoes = $this->variacaoModel->getAllGruposVariacoes();
        $produtosSimples = [];

        foreach ($produtos as $key => $produto) {
            $produtosSimples[$produto['produto_id']] = [
                'id' => $produto['produto_id'],
                'nome' => $produto['produto_nome']
            ];
        }

        $produtosSimples = array_values($produtosSimples);

        renderizarView(
            __DIR__ . '/../Views/produtos.php',
            null,
            [
                'produtos' => $produtos,
                'gruposVariacoes' => $gruposVariacoes,
                'produtosSimples' => $produtosSimples
            ]
        );
    }

    /**
     * Lida com a criação de um novo produto e sua entrada de estoque via requisição AJAX.
     */
    public function store(array $input = [])
    {
        $nome = $input['nome'] ?? null;
        $preco = $input['preco'] ?? null;
        $quantidade = $input['quantidade'] ?? null;
        $grupoId = $input['grupo_id'] ?? null;

        if (!$nome || !is_numeric($preco) || !is_numeric($quantidade)) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
            return;
        }

        try {
            $produtoId = $this->produtoModel->create($nome);
            $estoqueId = $this->estoqueModel->create(
                $produtoId,
                (float) $preco,
                (int) $quantidade,
                $grupoId
            );
            echo json_encode(['success' => true, 'message' => 'Produto cadastrado com sucesso!', 'produto_id' => $produtoId]);
        } catch (\PDOException $e) {
            // Verifica se é uma exceção de chave única (produto_id, grupo_id)
            if ($e->getCode() === '23000') { // Código SQLSTATE para violação de integridade
                echo json_encode(['success' => false, 'message' => 'Já existe uma entrada de estoque para este produto com o grupo de variação selecionado. Edite a existente.']);
            } else {
                error_log("Erro ao cadastrar produto: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar produto.']);
            }
        }
    }

    /**
     * Lida com a atualização de um produto e sua entrada de estoque via requisição AJAX.
     */
    public function update(array $input)
    {
        $produtoId = $input['produto_id'] ?? null;
        $estoqueId = $input['estoque_id'] ?? null;
        $nome = $input['nome'] ?? null;
        $preco = $input['preco'] ?? null;
        $quantidade = $input['quantidade'] ?? null;

        if (!$produtoId || !$estoqueId || !$nome || !is_numeric($preco) || !is_numeric($quantidade)) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos para atualização.']);
            return;
        }

        // Atualiza o produto e o estoque
        $produto = $this->produtoModel->update((int) $produtoId, $nome);
        $estoque = $this->estoqueModel->update((int) $estoqueId, (float) $preco, (int) $quantidade);

        if ($produto && $estoque) {
            echo json_encode(['success' => true, 'message' => 'Produto e estoque atualizados com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar produto e estoque.']);
        }
    }

    /**
     * Lida com a exclusão de uma entrada de estoque (e produto se for a última) via requisição AJAX.
     */
    public function delete(array $input)
    {
        $estoqueId = $input['estoque_id'] ?? null;

        if (!$estoqueId) {
            echo json_encode(['success' => false, 'message' => 'ID da entrada de estoque inválido para exclusão.']);
            return;
        }

        if ($this->estoqueModel->deleteEstoqueAndProdutoIfLast((int) $estoqueId)) {
            echo json_encode(['success' => true, 'message' => 'Entrada de estoque excluída com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir entrada de estoque.']);
        }
    }
}

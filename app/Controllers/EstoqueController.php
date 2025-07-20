<?php

namespace App\Controllers;

use App\Models\Estoque;
use PDO;

class EstoqueController
{

    private Estoque $estoqueModel;

    public function __construct(PDO $pdo)
    {
        $this->estoqueModel = new Estoque($pdo);
    }

    public function create(array $input)
    {
        $produtoId = $input['produto_id'];
        $preco = $input['preco'];
        $quantidade = $input['quantidade'];
        $grupoId = $input['grupo_id'];

        try {
            $estoqueId = $this->estoqueModel->create($produtoId, $preco, $quantidade, $grupoId);

            echo json_encode(['success' => true, 'message' => 'Estoque cadastrado com sucesso!', 'estoque_id' => $estoqueId]);
        } catch (\PDOException $e) {
            error_log("Erro ao cadastrar estoque: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar estoque.']);
        }
    }
}

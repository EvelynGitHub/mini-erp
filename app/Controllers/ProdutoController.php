<?php

namespace App\Controllers;

use App\Models\Produto;
use PDO;

class ProdutoController
{
    protected $produtoModel;

    public function __construct(PDO $pdo)
    {
        $this->produtoModel = new Produto($pdo);
    }

    public function index()
    {
        $produtos = $this->produtoModel->all();
        include __DIR__ . '/../Views/produtos.php';
    }

    public function store()
    {
        $this->produtoModel->create($_POST['nome'], $_POST['preco'], $_POST['variacao'] ?? null, $_POST['quantidade'] ?? 0);
        header("Location: /index.php?page=produtos&sucesso=1");
    }
}

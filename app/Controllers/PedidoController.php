<?php

namespace App\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Estoque;
use App\Models\Produto;
use PDO;

class PedidoController
{
    protected PDO $pdo;
    protected Pedido $pedidoModel;
    protected PedidoItem $pedidoItemModel;
    protected Estoque $estoqueModel;
    protected Produto $produtoModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pedidoModel = new Pedido($pdo);
        $this->pedidoItemModel = new PedidoItem($pdo);
        $this->estoqueModel = new Estoque($pdo);
        $this->produtoModel = new Produto($pdo);

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
    }

    /**
     * Adiciona produto ao carrinho
     */
    public function add(int $produtoId, int $quantidade = 1): void
    {
        $produto = $this->produtoModel->find($produtoId);
        if (!$produto) {
            echo "Produto não encontrado";
            return;
        }

        // Verifica se tem estoque
        $estoqueAtual = $this->estoqueModel->getQuantidade($produtoId);
        if ($estoqueAtual <= 0) {
            echo "Produto sem estoque disponível.";
            return;
        }

        // Atualiza carrinho
        if (!isset($_SESSION['carrinho'][$produtoId])) {
            $_SESSION['carrinho'][$produtoId] = [
                'quantidade' => 0,
                'preco' => (float) $produto['preco'],
                'nome' => $produto['nome']
            ];
        }

        $_SESSION['carrinho'][$produtoId]['quantidade'] += $quantidade;

        header("Location: /index.php?page=carrinho");
        exit;
    }

    /**
     * Exibe o carrinho
     */
    public function carrinho(): void
    {
        $carrinho = $_SESSION['carrinho'] ?? [];
        $subtotal = 0;
        foreach ($carrinho as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }
        $frete = $this->calcularFrete($subtotal);
        $total = $subtotal + $frete;

        // Valida estoque (alerta visual apenas)
        $errosEstoque = [];
        foreach ($carrinho as $pid => $item) {
            $stmt = $this->pdo->prepare("SELECT quantidade FROM estoque WHERE produto_id = ?");
            $stmt->execute([$pid]);
            $qtdEstoque = (int) $stmt->fetchColumn();

            if ($qtdEstoque < $item['quantidade']) {
                $errosEstoque[$pid] = "Estoque insuficiente (Disponível: {$qtdEstoque})";
            }
        }

        include __DIR__ . '/../Views/carrinho.php';
    }

    /**
     * Finaliza a compra
     */
    public function checkout(): void
    {
        $carrinho = $_SESSION['carrinho'] ?? [];
        if (empty($carrinho)) {
            echo "Seu carrinho está vazio.";
            return;
        }

        // Valida estoque de todos os itens antes de processar
        foreach ($carrinho as $pid => $item) {
            $qtdEstoque = $this->estoqueModel->getQuantidade($pid);
            if ($qtdEstoque < $item['quantidade']) {
                echo "Estoque insuficiente para o produto: {$item['nome']} (Disponível: $qtdEstoque)";
                return;
            }
        }

        // Calcula valores
        $subtotal = 0;
        foreach ($carrinho as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }
        $frete = $this->calcularFrete($subtotal);
        $total = $subtotal + $frete;

        // Salva pedido
        $pedidoId = $this->pedidoModel->criar(
            $subtotal,
            $frete,
            $total,
            $_POST['email'] ?? 'teste@teste.com',
            $_POST['endereco'] ?? 'Endereço não informado'
        );

        // Salva itens e decrementa estoque
        foreach ($carrinho as $pid => $item) {
            $this->pedidoItemModel->adicionarItem($pedidoId, $pid, $item['quantidade'], $item['preco']);
            $this->estoqueModel->decrementar($pid, $item['quantidade']);
        }

        // Dispara e-mail em background
        $this->enviarEmailAsync($pedidoId, $_POST['email'] ?? 'teste@teste.com', $_POST['endereco'] ?? '', $total);

        // Limpa carrinho
        $_SESSION['carrinho'] = [];

        echo "Pedido #$pedidoId finalizado com sucesso!";
    }

    /**
     * Calcula frete baseado no subtotal
     */
    private function calcularFrete(float $subtotal): float
    {
        if ($subtotal >= 200.00) {
            return 0.0;
        } elseif ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.0;
        }
        return 20.0;
    }

    /**
     * Dispara e-mail em segundo plano usando exec
     */
    private function enviarEmailAsync(int $pedidoId, string $email, string $endereco, float $total): void
    {
        $cmd = sprintf(
            'php %s/app/jobs/send_email.php %d %s %s %f > /dev/null 2>&1 &',
            __DIR__ . '/../..',
            $pedidoId,
            escapeshellarg($email),
            escapeshellarg($endereco),
            $total
        );
        exec($cmd);
    }
}

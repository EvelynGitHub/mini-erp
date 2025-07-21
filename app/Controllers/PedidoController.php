<?php

namespace App\Controllers;

use App\Models\Cupom;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Estoque;
use App\Models\Produto;
use App\Services\JobRunner;
use App\Services\EmailService;

use PDO;

class PedidoController
{
    protected PDO $pdo;
    protected Pedido $pedidoModel;
    protected PedidoItem $pedidoItemModel;
    protected Estoque $estoqueModel;
    protected Produto $produtoModel;
    protected Cupom $cupomModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pedidoModel = new Pedido($pdo);
        $this->pedidoItemModel = new PedidoItem($pdo);
        $this->estoqueModel = new Estoque($pdo);
        $this->produtoModel = new Produto($pdo);
        $this->cupomModel = new Cupom($pdo);

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
    }

    /**
     * Adiciona produto ao carrinho
     */
    public function add(int $produtoId, ?int $variacaoId, int $quantidade = 1): void
    {
        $produto = $this->produtoModel->find($produtoId);
        if (!$produto) {
            echo "Produto não encontrado";
            return;
        }

        // Verifica se tem estoque
        $estoqueAtual = $this->estoqueModel->getQuantidadePreco($produtoId, $variacaoId);
        if ($estoqueAtual['quantidade'] <= 0) {
            echo "Produto sem estoque disponível.";
            return;
        }

        // Atualiza carrinho
        if (!isset($_SESSION['carrinho']["$produtoId+$variacaoId"])) {
            $_SESSION['carrinho']["$produtoId+$variacaoId"] = [
                'quantidade' => 0,
                'preco' => (float) $estoqueAtual['preco'],
                'nome' => $produto['nome'],
                'variacao_id' => $variacaoId,
                'produto_id' => $produtoId
            ];
        }

        $_SESSION['carrinho']["$produtoId+$variacaoId"]['quantidade'] += $quantidade;

        header("Location: /index.php?page=carrinho");
        exit;
    }


    /**
     * Remoove produto do carrinho
     */
    public function remove(int $produtoId, ?int $variacaoId): void
    {
        $produto = $this->produtoModel->find($produtoId);
        if (!$produto) {
            echo "Produto não encontrado";
            return;
        }

        if ($_SESSION['carrinho']["$produtoId+$variacaoId"]['quantidade'] > 0) {
            $_SESSION['carrinho']["$produtoId+$variacaoId"]['quantidade'] -= 1;
        } else {
            unset($_SESSION['carrinho']["$produtoId+$variacaoId"]);
        }

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
        foreach ($carrinho as $p_id_va => $item) {
            if (is_null($item['variacao_id'])) {
                $stmt = $this->pdo->prepare("SELECT quantidade FROM estoque WHERE produto_id = ? AND grupo_id IS NULL");
                $stmt->execute([$item['produto_id']]);
            } else {
                $stmt = $this->pdo->prepare("SELECT quantidade FROM estoque WHERE produto_id = ? AND grupo_id = ?");
                $stmt->execute([$item['produto_id'], $item['variacao_id']]);
            }

            $qtdEstoque = (int) $stmt->fetchColumn();

            $chave = "{$item['produto_id']}+{$item['variacao_id']}";

            if ($qtdEstoque < $item['quantidade']) {
                $errosEstoque[$chave] = "Estoque insuficiente (Disponível: {$qtdEstoque})";
            }
        }

        include __DIR__ . '/../Views/carrinho.php';
    }

    /**
     * Finaliza a compra
     */
    public function checkout(array $input): void
    {
        $carrinho = $_SESSION['carrinho'] ?? [];
        if (empty($carrinho)) {
            echo "Seu carrinho está vazio.";
            return;
        }

        // Valida estoque de todos os itens antes de processar
        $subtotal = 0;
        foreach ($carrinho as $p_id_va => $item) {
            $qtdEstoque = $this->estoqueModel->getQuantidadePreco($item['produto_id'], $item['variacao_id']);
            if ($qtdEstoque['quantidade'] < $item['quantidade']) {
                echo "Estoque insuficiente para o produto: {$item['nome']} (Disponível: {$qtdEstoque['quantidade']})";
                return;
            }
            $subtotal += $qtdEstoque['preco'] * $item['quantidade'];
        }

        // // Calcula valores
        // foreach ($carrinho as $item) {
        // }

        // Aplica cupom se existir
        $cupom = $input['cupom'] ?? null;
        if ($cupom) {
            $desconto = $this->cupomModel->validar($cupom, $subtotal);

            if ($desconto['valido']) {
                $subtotal -= $desconto['desconto'];
            }
        }

        $frete = $this->calcularFrete($subtotal);
        $total = $subtotal + $frete;

        // Salva pedido
        $pedidoId = $this->pedidoModel->criar(
            $subtotal,
            $frete,
            $total,
            $input['email'] ?? 'teste@teste.com',
            $input['endereco'] ?? 'Endereço não informado'
        );

        // Salva itens e decrementa estoque
        foreach ($carrinho as $p_id_va => $item) {
            $this->pedidoItemModel->adicionarItem(
                $pedidoId,
                $item['produto_id'],
                $item['variacao_id'],
                $item['quantidade'],
                $item['preco']
            );
            $this->estoqueModel->decrementar(
                $item['produto_id'],
                $item['variacao_id'],
                $item['quantidade']
            );
        }

        // Dispara e-mail em background
        $this->enviarEmailAsync($pedidoId, $input['email'] ?? 'teste@teste.com', $input['endereco'] ?? '', $total);

        // Limpa carrinho
        $_SESSION['carrinho'] = [];

        $msg = "Pedido #$pedidoId finalizado com sucesso!";
        header("Location: /index.php?page=pedidos&msg=" . urlencode($msg));
    }


    public function listarPedidos(?string $msg = ""): void
    {
        $pedidos = $this->pedidoModel->listar();
        include __DIR__ . '/../Views/pedidos.php';
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
        JobRunner::dispatch(EmailService::class, 'enviarPedido', [
            $pedidoId,
            $email,
            $endereco,
            $total
        ]);
    }
}

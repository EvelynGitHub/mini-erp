<?php

$PAGE_TITLE = "Seu Carrinho de Compras";

$PAGE_CSS = <<<HTML
    <link rel="stylesheet" href="/css/custom.css">

    <style>
        /* Exemplo: Ajustes para a tabela do carrinho */
        .table-bordered th, .table-bordered td {
            vertical-align: middle;
        }
        .form-control {
            border-radius: 0.5rem;
        }
        .input-group label {
            width: 100%; /* Garante que a label ocupe a largura total acima do input */
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .input-group .form-control {
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }
        .input-group .btn {
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
    </style>
HTML;

$PAGE_SCRIPTS = <<<HTML
    <script>
        const subtotal = {$subtotal};
        const frete = {$frete};
    </script>
    <script src="/js/carrinho.js"></script>
HTML;

?>

<div class="container mt-4">
    <h1>Seu Carrinho</h1>

    <?php if (empty($carrinho)): ?>
        <div class="alert alert-info">Seu carrinho está vazio.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço Unitário</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrinho as $pid => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                        <td><?= $item['quantidade'] ?></td>
                        <td>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                        <td>
                            <?php if (isset($errosEstoque[$pid])): ?>
                                <span class="text-danger"><?= $errosEstoque[$pid] ?></span>
                            <?php else: ?>
                                <span class="text-success">OK</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/index.php?page=carrinho&action=remove&id=<?= $item['produto_id'] ?>&variacao=<?= $item['variacao_id'] ?? '' ?>"
                                class="btn btn-danger btn-sm">Remover</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4>Subtotal: R$ <?= number_format($subtotal, 2, ',', '.') ?></h4>
        <h4>Frete: R$ <?= number_format($frete, 2, ',', '.') ?></h4>
        <h3 id="total">Total: R$ <?= number_format($total, 2, ',', '.') ?></h3>

        <?php if (!empty($errosEstoque)): ?>
            <div class="alert alert-danger">
                Corrija os problemas de estoque antes de finalizar a compra.
            </div>
        <?php else: ?>
            <form method="post" action="/index.php?page=carrinho&action=checkout">
                <div class="mb-3">
                    <label for="emailContato" class="form-label">Email para contato</label>
                    <input type="email" name="email" id="emailContato" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="cep" class="form-label">CEP</label>
                    <div class="input-group">
                        <input name="cep" id="cep" class="form-control" placeholder="Ex: 00000-000" aria-describedby="btnCep">
                        <button id="btnCep" class="btn btn-primary" type="button">Pesquisar</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço de entrega</label>
                    <textarea name="endereco" id="endereco" class="form-control" rows="3" required></textarea>
                </div>
                <h4>Aplicar Cupom</h4>
                <div class="input-group mb-3">
                    <input type="text" id="codigoCupom" name="cupom" placeholder="Digite o código do cupom" class="form-control"
                        aria-describedby="btnCupom">
                    <button id="btnCupom" class="btn btn-primary" type="button">Aplicar</button>
                </div>
                <p id="msgCupom" style="color: red;"></p>


                <div class="mb-3">
                    <button type="submit" class="btn btn-success w-100">Finalizar Compra</button>
                </div>

            </form>
        <?php endif; ?>

    <?php endif; ?>

    <a href="/index.php?page=produtos" class="btn btn-secondary mt-3">Continuar Comprando</a>
</div>
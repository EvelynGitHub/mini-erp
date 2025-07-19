<?php


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Carrinho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4>Subtotal: R$ <?= number_format($subtotal, 2, ',', '.') ?></h4>
        <h4>Frete: R$ <?= number_format($frete, 2, ',', '.') ?></h4>
        <h3>Total: R$ <?= number_format($total, 2, ',', '.') ?></h3>

        <?php if (!empty($errosEstoque)): ?>
            <div class="alert alert-danger">
                Corrija os problemas de estoque antes de finalizar a compra.
            </div>
        <?php else: ?>
            <form method="post" action="/index.php?page=carrinho&action=checkout">
                <div class="mb-3">
                    <label>Email para contato</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>CEP</label>
                    <input name="cep" id="cep" class="form-control" />
                </div>
                <div class="mb-3">
                    <label>Endereço de entrega</label>
                    <textarea name="endereco" id="endereco" class="form-control" required></textarea>
                </div>
                <h4>Aplicar Cupom</h4>
                <div class="mb-3">
                    <input type="text" id="codigoCupom" placeholder="Digite o código" class="form-control inline-block">
                    <button id="btnCupom" class="btn btn-success btn-sm">Aplicar</button>
                </div>
                <p id="msgCupom" style="color: red;"></p>
                <button class="btn btn-success">Finalizar Compra</button>
            </form>
        <?php endif; ?>

    <?php endif; ?>
    <a href="/index.php?page=produtos" class="btn btn-secondary mt-3">Continuar Comprando</a>

    <script src="../js/jquery.min.js"></script>
    <script>
        // Aplicar Cupom
        $("#btnCupom").on('click', function () {

            const codigo = $("#codigoCupom").val();
            const subtotal = <?= $subtotal ?>;
            $.post('/index.php?page=aplicar-cupom', { codigo, subtotal }, function (res) {
                const data = JSON.parse(res);
                if (data.success) {
                    const desconto = data.desconto;
                    const novoTotal = <?= $subtotal ?> + <?= $frete ?> - desconto;
                    $("#total").text("R$" + novoTotal.toFixed(2).replace('.', ','));
                    $("#msgCupom").css("color", "green").text("Cupom aplicado! Desconto: R$" + desconto.toFixed(2));
                } else {
                    $("#msgCupom").css("color", "red").text(data.msg);
                }
            });
        });

        // Consulta CEP com ViaCEP
        $("#cep").on('blur', function () {
            const cep = $(this).val().replace(/\D/g, '');
            if (cep.length === 8) {
                $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function (data) {
                    if (!data.erro) {
                        $("#endereco").val(`${data.logradouro}, ${data.bairro}, ${data.localidade}-${data.uf}`);
                    } else {
                        alert("CEP não encontrado.");
                    }
                });
            }
        });
    </script>
</body>

</html>
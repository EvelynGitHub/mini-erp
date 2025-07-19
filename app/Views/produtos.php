<!DOCTYPE html>
<html>

<head>
    <title>Produtos</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
</head>

<body class="p-4">
    <header><a href="/index.php?page=carrinho">Ver carrinho</a></header>
    <h2>Cadastrar Produto</h2>
    <form method="POST">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="number" step="0.01" name="preco" placeholder="Preço" required>
        <input type="text" name="variacao" placeholder="Variação (opcional)">
        <input type="number" name="quantidade" placeholder="Qtd" required>
        <button type="submit">Salvar</button>
    </form>

    <hr>
    <h3>Produtos</h3>
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Variação</th>
            <th>Preço</th>
            <th>Qtd</th>
            <th>Ação</th>
        </tr>
        <?php foreach ($produtos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['nome'] ?></td>
                <td><?= $p['variacao'] ?></td>
                <td>R$<?= $p['preco'] ?></td>
                <td><?= $p['quantidade'] ?></td>
                <td><a href="/index.php?page=carrinho&action=add&id=<?= $p['id'] ?>">Comprar</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
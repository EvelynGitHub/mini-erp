<?php
$PAGE_CSS = <<<HTML
    <link href="/css/custom.css" rel="stylesheet">
HTML;

$PAGE_SCRIPTS = <<<HTML
<script>
    // Não há necessidade de JavaScript complexo para esta tela,
    // já que a listagem é feita via PHP e não há operações AJAX diretas aqui.
    // Se futuras funcionalidades de edição/exclusão de pedidos forem adicionadas,
    // o JavaScript será implementado de forma similar às telas de produtos e variações.
</script>
HTML;

$PAGE_TITLE = "Pedidos";
?>


<div class="container-fluid py-4">
    <h1 class="text-center"><?= $msg ?></h1>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Card para Listagem de Pedidos -->
            <div class="card mt-4">
                <div class="card-header">
                    Pedidos Existentes
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID Pedido</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Endereço</th>
                                    <th scope="col">Criado Em</th>
                                    <!-- Adicione mais colunas se PedidoModel->listar() retornar mais dados relevantes -->
                                </tr>
                            </thead>
                            <tbody id="tabelaPedidos">
                                <?php if (empty($pedidos)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Nenhum pedido cadastrado.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pedidos as $pedido): ?>
                                        <tr>
                                            <td><?= $pedido['id'] ?></td>
                                            <td>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></td>
                                            <td><?= ucfirst($pedido['status']) ?></td>
                                            <td><?= $pedido['endereco'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                                            <!-- Renderize mais dados aqui -->
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$PAGE_CSS = <<<HTML
    <link href="/css/custom.css" rel="stylesheet">
HTML;

$PAGE_TITLE = "Gerenciamento de Produtos";
?>


<div class="container-fluid py-4 container-md ">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="row">
                <!-- Card para Cadastrar Novo Produto -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Cadastrar Novo Produto
                        </div>
                        <div class="card-body">
                            <form id="formCadastrarProduto">
                                <div class="mb-3">
                                    <label for="nomeProduto" class="form-label">Nome do Produto</label>
                                    <input type="text" class="form-control" id="nomeProduto"
                                        placeholder="Ex: Camiseta Básica" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="precoProduto" class="form-label">Preço</label>
                                        <input type="number" step="0.01" class="form-control" id="precoProduto"
                                            placeholder="Ex: 59.90" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="quantidadeProduto" class="form-label">Quantidade em
                                            Estoque</label>
                                        <input type="number" class="form-control" id="quantidadeProduto"
                                            placeholder="Ex: 10" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="grupoVariacaoProduto" class="form-label">Grupo de Variação / SKU
                                        (Opcional)</label>
                                    <select class="form-select" id="grupoVariacaoProduto">
                                        <option value="">Nenhum</option>
                                        <?php foreach ($gruposVariacoes as $grupo): ?>
                                            <option value="<?= $grupo['id'] ?>"><?= $grupo['nome'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Criar Produto e Estoque</button>
                            </form>
                            <div id="alertaProduto" class="alert mt-3 d-none" role="alert"></div>
                        </div>
                    </div>
                </div>

                <!-- Card para Adicionar Estoque para Produto Existente -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Adicionar Estoque para Produto Existente
                        </div>
                        <div class="card-body">
                            <form id="formAdicionarEstoque">
                                <div class="mb-3">
                                    <label for="selectProdutoExistente" class="form-label">Selecionar
                                        Produto</label>
                                    <select class="form-select" id="selectProdutoExistente" required>
                                        <option value="">Selecione um produto</option>
                                        <?php foreach ($produtosSimples as $prod): ?>
                                            <option value="<?= $prod['id'] ?>"><?= $prod['nome'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="selectGrupoVariacaoEstoque" class="form-label">Selecionar Grupo de
                                        Variação / SKU</label>
                                    <select class="form-select" id="selectGrupoVariacaoEstoque" required>
                                        <option value="">Selecione um SKU</option>
                                        <?php foreach ($gruposVariacoes as $grupo): ?>
                                            <option value="<?= $grupo['id'] ?>"><?= $grupo['nome'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="precoEstoqueExistente" class="form-label">Preço</label>
                                        <input type="number" step="0.01" class="form-control" id="precoEstoqueExistente"
                                            placeholder="Ex: 59.90" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="quantidadeEstoqueExistente" class="form-label">Quantidade em
                                            Estoque</label>
                                        <input type="number" class="form-control" id="quantidadeEstoqueExistente"
                                            placeholder="Ex: 10" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Adicionar Estoque</button>
                            </form>
                            <div id="alertaAdicionarEstoque" class="alert mt-3 d-none" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card para Listagem de Produtos -->
            <div class="card mt-4">
                <div class="card-header">
                    Produtos Existentes
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID Produto</th>
                                    <th scope="col">Nome Produto</th>
                                    <th scope="col">Grupo Variação / SKU</th>
                                    <th scope="col">Preço</th>
                                    <th scope="col">Estoque</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabelaProdutos">
                                <?php if (empty($produtos)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Nenhum produto cadastrado.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($produtos as $p): ?>
                                        <tr>
                                            <td><?= $p['produto_id'] ?></td>
                                            <td><?= $p['produto_nome'] ?></td>
                                            <td><?= $p['grupo_nome'] ?? 'N/A (Sem SKU)' ?></td>
                                            <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                                            <td><?= $p['quantidade'] ?></td>
                                            <td>
                                                <button class="btn btn-info btn-sm me-2 btn-editar-produto"
                                                    data-produto-id="<?= $p['produto_id'] ?>"
                                                    data-estoque-id="<?= $p['estoque_id'] ?>"
                                                    data-grupo-id="<?= $p['grupo_id'] ?? '' ?>"
                                                    data-nome-produto="<?= $p['produto_nome'] ?>"
                                                    data-preco="<?= $p['preco'] ?>" data-quantidade="<?= $p['quantidade'] ?>"
                                                    data-nome-grupo="<?= $p['grupo_nome'] ?? 'N/A (Sem SKU)' ?>">
                                                    Editar
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-excluir-produto"
                                                    data-produto-id="<?= $p['produto_id'] ?>"
                                                    data-estoque-id="<?= $p['estoque_id'] ?>"
                                                    data-nome-produto="<?= $p['produto_nome'] ?>"
                                                    data-nome-grupo="<?= $p['grupo_nome'] ?? 'N/A (Sem SKU)' ?>">
                                                    Excluir
                                                </button>
                                                <a class="btn btn-primary"
                                                    href="?page=carrinho&action=add&id=<?= $p['produto_id'] ?>&variacao=<?= $p['grupo_id'] ?? '' ?>">
                                                    Comprar
                                                </a>
                                            </td>
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

<!-- Modal de Edição de Produto/Estoque -->
<div class="modal fade" id="modalEditarProduto" tabindex="-1" aria-labelledby="modalEditarProdutoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarProdutoLabel">Editar Produto e Estoque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editProdutoId">
                <input type="hidden" id="editEstoqueId">
                <input type="hidden" id="editGrupoId"> <!-- Para manter o grupo_id associado ao estoque -->

                <div class="mb-3">
                    <label for="editNomeProduto" class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" id="editNomeProduto" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="editPrecoProduto" class="form-label">Preço</label>
                        <input type="number" step="0.01" class="form-control" id="editPrecoProduto" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editQuantidadeProduto" class="form-label">Quantidade em Estoque</label>
                        <input type="number" class="form-control" id="editQuantidadeProduto" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Grupo de Variação / SKU Associado</label>
                    <input type="text" class="form-control" id="editGrupoVariacaoAssociado" readonly>
                    <small class="form-text text-muted">Este campo não é editável, pois define a entrada de estoque
                        específica.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarEdicaoProduto">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão (Genérico) -->
<div class="modal fade" id="modalConfirmacaoExclusao" tabindex="-1" aria-labelledby="modalConfirmacaoExclusaoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacaoExclusaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir <strong id="itemExcluirNome"></strong>? Esta ação não pode ser
                desfeita.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir</button>
            </div>
        </div>
    </div>
</div>

<?php
$PAGE_SCRIPTS = <<<HTML

<script src="/js/produtos.js"></script>

HTML;
?>
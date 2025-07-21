<?php

$PAGE_CSS = <<<HTML
    <link href="/css/custom.css" rel="stylesheet">
HTML;

$variacoesIniciais = json_encode($variacoes);
$gruposVariacoesIniciais = json_encode($gruposVariacoes);

$PAGE_SCRIPTS = <<<HTML
    <script>
        // Dados iniciais injetados pelo PHP
        const variacoesIniciais = {$variacoesIniciais};
        const gruposVariacoesIniciais = {$gruposVariacoesIniciais};
    </script>
    <script src="/js/variacoes.js"></script>
HTML;

$PAGE_TITLE = "Variações/SKUs";
?>

<div class="container-fluid py-4 container-md">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="row">
                <!-- Card para Cadastrar Variação -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Cadastrar Nova Variação
                        </div>
                        <div class="card-body">
                            <form id="formCadastrarVariacao">
                                <div class="mb-3">
                                    <label for="nomeVariacao" class="form-label">Nome da Variação</label>
                                    <input type="text" class="form-control" id="nomeVariacao"
                                        placeholder="Ex: Preto, P, M" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Adicionar Variação</button>
                            </form>
                            <div id="alertaVariacao" class="alert mt-3 d-none" role="alert"></div>
                        </div>
                    </div>
                </div>

                <!-- Card para Cadastrar Grupo de Variação -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Cadastrar Novo Grupo de Variação
                        </div>
                        <div class="card-body">
                            <form id="formCadastrarGrupoVariacao">
                                <div class="mb-3">
                                    <label for="nomeGrupoVariacao" class="form-label">Nome do Grupo</label>
                                    <input type="text" class="form-control" id="nomeGrupoVariacao"
                                        placeholder="Ex: Cor, Tamanho" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Selecione as Variações</label>
                                    <div id="listaVariacoesCheckboxes" class="border p-3 rounded-3"
                                        style="max-height: 200px; overflow-y: auto;">
                                        <!-- Checkboxes de variações serão carregados aqui via PHP -->
                                        <?php if (empty($variacoes)): ?>
                                            <p class="text-muted text-center" id="noVariationsMsg">Nenhuma variação
                                                disponível. Cadastre variações primeiro.</p>
                                        <?php else: ?>
                                            <?php foreach ($variacoes as $variacao): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="<?= $variacao['id'] ?>" id="variacaoCheck<?= $variacao['id'] ?>">
                                                    <label class="form-check-label" for="variacaoCheck<?= $variacao['id'] ?>">
                                                        <?= $variacao['nome'] ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Adicionar Grupo de
                                    Variação</button>
                            </form>
                            <div id="alertaGrupoVariacao" class="alert mt-3 d-none" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Card para Listagem de Variações -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Variações Existentes
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Nome</th>
                                            <th scope="col">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabelaVariacoes">
                                        <?php if (empty($variacoes)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Nenhuma variação
                                                    cadastrada.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($variacoes as $variacao): ?>
                                                <tr>
                                                    <td><?= $variacao['id'] ?></td>
                                                    <td><?= $variacao['nome'] ?></td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm me-2 btn-editar-variacao"
                                                            data-id="<?= $variacao['id'] ?>"
                                                            data-nome="<?= $variacao['nome'] ?>">Editar</button>
                                                        <button class="btn btn-danger btn-sm btn-excluir-variacao"
                                                            data-id="<?= $variacao['id'] ?>"
                                                            data-nome="<?= $variacao['nome'] ?>">Excluir</button>
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

                <!-- Card para Listagem de Grupos de Variações -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Grupos de Variações Existentes
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Nome do Grupo</th>
                                            <th scope="col">Variações Associadas</th>
                                            <th scope="col">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabelaGruposVariacoes">
                                        <?php if (empty($gruposVariacoes)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Nenhum grupo de variação
                                                    cadastrado.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($gruposVariacoes as $grupo): ?>
                                                <tr>
                                                    <td><?= $grupo['id'] ?></td>
                                                    <td><?= $grupo['nome'] ?></td>
                                                    <td>
                                                        <?php
                                                        $nomesVariacoes = [];
                                                        foreach ($grupo['variacao_ids'] as $variacaoId) {
                                                            $foundVariacao = null;
                                                            foreach ($variacoes as $v) {
                                                                if ($v['id'] == $variacaoId) {
                                                                    $foundVariacao = $v;
                                                                    break;
                                                                }
                                                            }
                                                            if ($foundVariacao) {
                                                                $nomesVariacoes[] = $foundVariacao['nome'];
                                                            } else {
                                                                $nomesVariacoes[] = "ID Desconhecido (" . $variacaoId . ")";
                                                            }
                                                        }
                                                        echo implode(', ', $nomesVariacoes) ?: '<span class="text-muted">Nenhuma</span>';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm me-2 btn-editar-grupo"
                                                            data-id="<?= $grupo['id'] ?>" data-nome="<?= $grupo['nome'] ?>"
                                                            data-variacao-ids="<?= implode(',', $grupo['variacao_ids']) ?>">Editar</button>
                                                        <button class="btn btn-danger btn-sm btn-excluir-grupo"
                                                            data-id="<?= $grupo['id'] ?>"
                                                            data-nome="<?= $grupo['nome'] ?>">Excluir</button>
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
    </div>
</div>

<!-- Modal de Edição de Variação -->
<div class="modal fade" id="modalEditarVariacao" tabindex="-1" aria-labelledby="modalEditarVariacaoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarVariacaoLabel">Editar Variação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editVariacaoId">
                <div class="mb-3">
                    <label for="editNomeVariacao" class="form-label">Nome da Variação</label>
                    <input type="text" class="form-control" id="editNomeVariacao" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarEdicaoVariacao">Salvar
                    Alterações</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Grupo de Variação -->
<div class="modal fade" id="modalEditarGrupoVariacao" tabindex="-1" aria-labelledby="modalEditarGrupoVariacaoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarGrupoVariacaoLabel">Editar Grupo de Variação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editGrupoVariacaoId">
                <div class="mb-3">
                    <label for="editNomeGrupoVariacao" class="form-label">Nome do Grupo</label>
                    <input type="text" class="form-control" id="editNomeGrupoVariacao" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Selecione as Variações</label>
                    <div id="editListaVariacoesCheckboxes" class="border p-3 rounded-3"
                        style="max-height: 200px; overflow-y: auto;">
                        <!-- Checkboxes de variações serão carregados aqui via JS para edição -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarEdicaoGrupoVariacao">Salvar
                    Alterações</button>
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
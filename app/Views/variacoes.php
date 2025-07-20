<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Variações</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            /* Um cinza claro para o fundo */
            color: #333;
        }

        .navbar {
            background-color: #007bff;
            /* Azul primário do Bootstrap */
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
        }

        .card {
            border-radius: 1rem;
            /* Cantos mais arredondados */
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
            /* Sombra mais suave */
            border: none;
            /* Remove a borda padrão do card */
        }

        .card-header {
            background-color: #ffffff;
            /* Fundo branco para o cabeçalho do card */
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
            /* Efeito de elevação ao passar o mouse */
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-info:hover {
            background-color: #117a8b;
            border-color: #117a8b;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #bd2130;
            border-color: #bd2130;
            transform: translateY(-1px);
        }

        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .list-group-item {
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid #e9ecef;
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list-group-item:last-child {
            margin-bottom: 0;
        }

        .badge {
            font-size: 0.85em;
            padding: 0.5em 0.75em;
            border-radius: 0.5rem;
        }

        .alert {
            border-radius: 0.75rem;
            margin-top: 1.5rem;
            font-weight: 500;
        }

        .table {
            border-radius: 1rem;
            overflow: hidden;
            /* Garante que os cantos arredondados funcionem com a tabela */
        }

        .table thead th {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table td,
        .table th {
            padding: 1rem;
        }

        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }

        .modal-content {
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem 1.5rem 0.5rem 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 0.5rem 1.5rem 1.5rem 1.5rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark mb-5">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Mini-ERP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="?page=variacao">Variações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=carrinho">
                            Carrinho
                            <span class="badge rounded-pill bg-danger">
                                <?= count($_SESSION["carrinho"]) ?>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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
                                                            value="<?= $variacao['id'] ?>"
                                                            id="variacaoCheck<?= $variacao['id'] ?>">
                                                        <label class="form-check-label"
                                                            for="variacaoCheck<?= $variacao['id'] ?>">
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

    <!-- Bootstrap JS e Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Dados iniciais injetados pelo PHP
        const variacoesIniciais = <?= json_encode($variacoes); ?>;
        const gruposVariacoesIniciais = <?= json_encode($gruposVariacoes); ?>;

        // Variáveis globais para o JS (agora populadas pelo PHP)
        let variacoes = variacoesIniciais;
        let gruposVariacoes = gruposVariacoesIniciais;

        // Instâncias dos modais Bootstrap
        const modalEditarVariacao = new bootstrap.Modal(document.getElementById('modalEditarVariacao'));
        const modalEditarGrupoVariacao = new bootstrap.Modal(document.getElementById('modalEditarGrupoVariacao'));
        const modalConfirmacaoExclusao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));

        // Variáveis para controlar qual item está sendo excluído
        let itemExcluirTipo = ''; // 'variacao' ou 'grupo'
        let itemExcluirId = null;

        // --- Funções Auxiliares ---

        /**
         * Exibe uma mensagem de alerta na UI.
         * @param {string} message - A mensagem a ser exibida.
         * @param {string} type - O tipo de alerta (ex: 'success', 'danger', 'warning', 'info').
         * @param {HTMLElement} element - O elemento HTML onde o alerta será exibido.
         */
        function showAlert(message, type, element) {
            element.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning', 'alert-info');
            element.classList.add(`alert-${type}`);
            element.textContent = message;
            setTimeout(() => {
                element.classList.add('d-none');
            }, 3000); // Esconde o alerta após 3 segundos
        }

        /**
         * Atualiza os checkboxes de variações no formulário de grupo e no modal de edição de grupo.
         * @param {Array<number>} [selectedVariations=[]] - IDs das variações que devem estar marcadas (para edição).
         * @param {HTMLElement} [targetElement=document.getElementById('listaVariacoesCheckboxes')] - O elemento onde os checkboxes serão renderizados.
         */
        function atualizarCheckboxesVariacoes(selectedVariations = [], targetElement = document.getElementById('listaVariacoesCheckboxes')) {
            targetElement.innerHTML = ''; // Limpa os checkboxes existentes
            const noVariationsMsg = document.getElementById('noVariationsMsg');

            if (variacoes.length === 0) {
                if (noVariationsMsg) noVariationsMsg.classList.remove('d-none');
                return;
            } else {
                if (noVariationsMsg) noVariationsMsg.classList.add('d-none');
            }

            variacoes.forEach(variacao => {
                const div = document.createElement('div');
                div.classList.add('form-check');
                const isChecked = selectedVariations.includes(variacao.id) ? 'checked' : '';
                div.innerHTML = `
                    <input class="form-check-input" type="checkbox" value="${variacao.id}" id="variacaoCheck${variacao.id}_${targetElement.id}" ${isChecked}>
                    <label class="form-check-label" for="variacaoCheck${variacao.id}_${targetElement.id}">
                        ${variacao.nome}
                    </label>
                `;
                targetElement.appendChild(div);
            });
        }

        /**
         * Adiciona listeners aos botões de ação das variações.
         */
        function adicionarListenersAcoesVariacao() {
            document.querySelectorAll('.btn-editar-variacao').forEach(button => {
                button.onclick = (event) => abrirModalEditarVariacao(parseInt(event.target.dataset.id));
            });
            document.querySelectorAll('.btn-excluir-variacao').forEach(button => {
                button.onclick = (event) => {
                    itemExcluirTipo = 'variacao';
                    itemExcluirId = parseInt(event.target.dataset.id);
                    document.getElementById('itemExcluirNome').textContent = `a variação "${event.target.dataset.nome}"`;
                    modalConfirmacaoExclusao.show();
                };
            });
        }

        /**
         * Adiciona listeners aos botões de ação dos grupos de variações.
         */
        function adicionarListenersAcoesGrupo() {
            document.querySelectorAll('.btn-editar-grupo').forEach(button => {
                button.onclick = (event) => {
                    const variacaoIdsStr = event.target.dataset.variacaoIds;
                    const variacaoIds = variacaoIdsStr ? variacaoIdsStr.split(',').map(Number) : [];
                    abrirModalEditarGrupoVariacao(parseInt(event.target.dataset.id), variacaoIds);
                };
            });
            document.querySelectorAll('.btn-excluir-grupo').forEach(button => {
                button.onclick = (event) => {
                    itemExcluirTipo = 'grupo';
                    itemExcluirId = parseInt(event.target.dataset.id);
                    document.getElementById('itemExcluirNome').textContent = `o grupo "${event.target.dataset.nome}"`;
                    modalConfirmacaoExclusao.show();
                };
            });
        }

        /**
         * Abre o modal de edição de variação e preenche com os dados.
         * @param {number} id - ID da variação a ser editada.
         */
        function abrirModalEditarVariacao(id) {
            const variacao = variacoes.find(v => v.id === id);
            if (variacao) {
                document.getElementById('editVariacaoId').value = variacao.id;
                document.getElementById('editNomeVariacao').value = variacao.nome;
                modalEditarVariacao.show();
            }
        }

        /**
         * Salva as alterações de uma variação via AJAX.
         */
        document.getElementById('btnSalvarEdicaoVariacao').addEventListener('click', async function () {
            const id = parseInt(document.getElementById('editVariacaoId').value);
            const novoNome = document.getElementById('editNomeVariacao').value.trim();
            const alertaVariacao = document.getElementById('alertaVariacao');

            if (novoNome === '') {
                showAlert('Por favor, insira o nome da variação.', 'warning', alertaVariacao);
                return;
            }

            try {
                const response = await fetch('/index.php?page=variacao', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id, nome: novoNome, type: 'variacao' }) // Adiciona 'type' para roteamento
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success', alertaVariacao);
                    location.reload(); // Recarrega a página para atualizar a tabela
                } else {
                    showAlert(result.message, 'danger', alertaVariacao);
                }
                modalEditarVariacao.hide();
            } catch (error) {
                console.error('Erro ao salvar edição da variação:', error);
                showAlert('Erro ao conectar com o servidor.', 'danger', alertaVariacao);
            }
        });

        /**
         * Abre o modal de edição de grupo de variação e preenche com os dados.
         * @param {number} id - ID do grupo a ser editado.
         * @param {Array<number>} variacaoIds - IDs das variações associadas ao grupo.
         */
        function abrirModalEditarGrupoVariacao(id, variacaoIds) {
            const grupo = gruposVariacoes.find(g => g.id === id);
            if (grupo) {
                document.getElementById('editGrupoVariacaoId').value = grupo.id;
                document.getElementById('editNomeGrupoVariacao').value = grupo.nome;
                // Atualiza os checkboxes no modal de edição com as variações do grupo
                atualizarCheckboxesVariacoes(variacaoIds, document.getElementById('editListaVariacoesCheckboxes'));
                modalEditarGrupoVariacao.show();
            }
        }

        /**
         * Salva as alterações de um grupo de variação via AJAX.
         */
        document.getElementById('btnSalvarEdicaoGrupoVariacao').addEventListener('click', async function () {
            const id = parseInt(document.getElementById('editGrupoVariacaoId').value);
            const novoNomeGrupo = document.getElementById('editNomeGrupoVariacao').value.trim();
            const alertaGrupoVariacao = document.getElementById('alertaGrupoVariacao');

            if (novoNomeGrupo === '') {
                showAlert('Por favor, insira o nome do grupo de variação.', 'warning', alertaGrupoVariacao);
                return;
            }

            const checkboxes = document.querySelectorAll('#editListaVariacoesCheckboxes input[type="checkbox"]:checked');
            const variacao_ids_selecionadas = Array.from(checkboxes).map(cb => parseInt(cb.value));

            try {
                const response = await fetch('/index.php?page=variacao', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id, nome: novoNomeGrupo, variacao_ids: variacao_ids_selecionadas, type: 'grupo' }) // Adiciona 'type'
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success', alertaGrupoVariacao);
                    location.reload(); // Recarrega a página para atualizar a tabela
                } else {
                    showAlert(result.message, 'danger', alertaGrupoVariacao);
                }
                modalEditarGrupoVariacao.hide();
            } catch (error) {
                console.error('Erro ao salvar edição do grupo:', error);
                showAlert('Erro ao conectar com o servidor.', 'danger', alertaGrupoVariacao);
            }
        });

        /**
         * Confirma e executa a exclusão do item selecionado via AJAX.
         */
        document.getElementById('btnConfirmarExclusao').addEventListener('click', async function () {
            const alertaElement = itemExcluirTipo === 'variacao' ? document.getElementById('alertaVariacao') : document.getElementById('alertaGrupoVariacao');
            const endpoint = '/index.php?page=variacao';
            let payload = { id: itemExcluirId };
            let method = 'DELETE';

            if (itemExcluirTipo === 'variacao') {
                payload.type = 'variacao';
            } else if (itemExcluirTipo === 'grupo') {
                payload.type = 'grupo';
            }

            try {
                const response = await fetch(endpoint, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success', alertaElement);
                    location.reload(); // Recarrega a página para atualizar a tabela
                } else {
                    showAlert(result.message, 'danger', alertaElement);
                }
                modalConfirmacaoExclusao.hide();
            } catch (error) {
                console.error('Erro ao excluir:', error);
                showAlert('Erro ao conectar com o servidor.', 'danger', alertaElement);
            } finally {
                itemExcluirTipo = ''; // Reseta as variáveis de controle
                itemExcluirId = null;
            }
        });

        // --- Event Listeners de Cadastro ---

        // Formulário de Cadastro de Variação
        document.getElementById('formCadastrarVariacao').addEventListener('submit', async function (event) {
            event.preventDefault();
            const nomeVariacaoInput = document.getElementById('nomeVariacao');
            const nome = nomeVariacaoInput.value.trim();
            const alertaVariacao = document.getElementById('alertaVariacao');

            if (nome === '') {
                showAlert('Por favor, insira o nome da variação.', 'warning', alertaVariacao);
                return;
            }

            try {
                const response = await fetch('/index.php?page=variacao', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ nome: nome, type: 'variacao' }) // Adiciona 'type' para roteamento
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success', alertaVariacao);
                    nomeVariacaoInput.value = '';
                    location.reload(); // Recarrega a página para atualizar a tabela
                } else {
                    showAlert(result.message, 'danger', alertaVariacao);
                }
            } catch (error) {
                console.error('Erro ao cadastrar variação:', error);
                showAlert('Erro ao conectar com o servidor.', 'danger', alertaVariacao);
            }
        });

        // Formulário de Cadastro de Grupo de Variação
        document.getElementById('formCadastrarGrupoVariacao').addEventListener('submit', async function (event) {
            event.preventDefault();
            const nomeGrupoVariacaoInput = document.getElementById('nomeGrupoVariacao');
            const nomeGrupo = nomeGrupoVariacaoInput.value.trim();
            const alertaGrupoVariacao = document.getElementById('alertaGrupoVariacao');

            if (nomeGrupo === '') {
                showAlert('Por favor, insira o nome do grupo de variação.', 'warning', alertaGrupoVariacao);
                return;
            }

            const checkboxes = document.querySelectorAll('#listaVariacoesCheckboxes input[type="checkbox"]:checked');
            const variacao_ids_selecionadas = Array.from(checkboxes).map(cb => parseInt(cb.value));

            try {
                const response = await fetch('/index.php?page=variacao', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ nome: nomeGrupo, variacao_ids: variacao_ids_selecionadas, type: 'grupo' }) // Adiciona 'type'
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success', alertaGrupoVariacao);
                    nomeGrupoVariacaoInput.value = '';
                    checkboxes.forEach(cb => cb.checked = false); // Desmarca todos os checkboxes
                    location.reload(); // Recarrega a página para atualizar a tabela
                } else {
                    showAlert(result.message, 'danger', alertaGrupoVariacao);
                }
            } catch (error) {
                console.error('Erro ao cadastrar grupo de variação:', error);
                showAlert('Erro ao conectar com o servidor.', 'danger', alertaGrupoVariacao);
            }
        });

        // --- Inicialização ---
        document.addEventListener('DOMContentLoaded', () => {
            // Os dados iniciais já são carregados pelo PHP.
            // Apenas adicionamos os listeners para os botões de ação que são renderizados pelo PHP.
            adicionarListenersAcoesVariacao();
            adicionarListenersAcoesGrupo();
        });
    </script>
</body>

</html>
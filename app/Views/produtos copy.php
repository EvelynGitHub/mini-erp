<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Produtos</title>
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

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
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
            <a class="navbar-brand" href="#">Gerenciamento de Produtos ERP</a>
        </div>
    </nav>

    <div class="container-fluid py-4 container-md">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Card para Cadastrar Produto -->
                <div class="card mb-4 ">
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
                                    <label for="quantidadeProduto" class="form-label">Quantidade em Estoque</label>
                                    <input type="number" class="form-control" id="quantidadeProduto"
                                        placeholder="Ex: 10" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="grupoVariacaoProduto" class="form-label">Grupo de Variação
                                    (Opcional)</label>
                                <select class="form-select" id="grupoVariacaoProduto">
                                    <option value="">Nenhum</option>
                                    <!-- Grupos de variações serão carregados aqui via JS ou PHP -->
                                    <?php foreach ($gruposVariacoes as $grupo): ?>
                                        <option value="<?= $grupo['id'] ?>"><?= $grupo['nome'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Adicionar Produto</button>
                        </form>
                        <div id="alertaProduto" class="alert mt-3 d-none" role="alert"></div>
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
                                        <th scope="col">Grupo Variação</th>
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
                                                <td><?= $p['grupo_nome'] ?? 'N/A (Sem Variação)' ?></td>
                                                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                                                <td><?= $p['quantidade'] ?></td>
                                                <td>
                                                    <button class="btn btn-info btn-sm me-2 btn-editar-produto"
                                                        data-produto-id="<?= $p['produto_id'] ?>"
                                                        data-estoque-id="<?= $p['estoque_id'] ?>"
                                                        data-grupo-id="<?= $p['grupo_id'] ?? '' ?>"
                                                        data-nome-produto="<?= $p['produto_nome'] ?>"
                                                        data-preco="<?= $p['preco'] ?>"
                                                        data-quantidade="<?= $p['quantidade'] ?>"
                                                        data-nome-grupo="<?= $p['grupo_nome'] ?? 'N/A (Sem Variação)' ?>">
                                                        Editar
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-excluir-produto"
                                                        data-produto-id="<?= $p['produto_id'] ?>"
                                                        data-estoque-id="<?= $p['estoque_id'] ?>"
                                                        data-nome-produto="<?= $p['produto_nome'] ?>"
                                                        data-nome-grupo="<?= $p['grupo_nome'] ?? 'N/A (Sem Variação)' ?>">
                                                        Excluir
                                                    </button>
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
                        <label class="form-label">Grupo de Variação Associado</label>
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

    <!-- Bootstrap JS e Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Instâncias dos modais Bootstrap
        const modalEditarProduto = new bootstrap.Modal(document.getElementById('modalEditarProduto'));
        const modalConfirmacaoExclusao = new bootstrap.Modal(document.getElementById('modalConfirmacaoExclusao'));

        // Variáveis para controlar qual item está sendo excluído
        let itemExcluirTipo = ''; // 'produto'
        let itemExcluirProdutoId = null; // ID do produto a ser excluído
        let itemExcluirEstoqueId = null; // ID da entrada de estoque a ser excluída (se for uma linha específica da tabela)

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
         * Adiciona listeners aos botões de ação dos produtos.
         */
        function adicionarListenersAcoesProduto() {
            document.querySelectorAll('.btn-editar-produto').forEach(button => {
                button.onclick = (event) => abrirModalEditarProduto(event.target.dataset);
            });
            document.querySelectorAll('.btn-excluir-produto').forEach(button => {
                button.onclick = (event) => {
                    itemExcluirTipo = 'produto';
                    itemExcluirProdutoId = parseInt(event.target.dataset.produtoId);
                    itemExcluirEstoqueId = parseInt(event.target.dataset.estoqueId); // Usar para identificar a linha de estoque
                    const nomeProduto = event.target.dataset.nomeProduto;
                    const nomeGrupo = event.target.dataset.nomeGrupo;
                    let nomeCompleto = nomeProduto;
                    if (nomeGrupo && nomeGrupo !== 'N/A (Sem Variação)') {
                        nomeCompleto += ` (Grupo: ${nomeGrupo})`;
                    }
                    document.getElementById('itemExcluirNome').textContent = `o produto "${nomeCompleto}"`;
                    modalConfirmacaoExclusao.show();
                };
            });
        }

        /**
         * Abre o modal de edição de produto e preenche com os dados.
         * @param {object} data - Objeto contendo os dados do produto e estoque.
         */
        function abrirModalEditarProduto(data) {
            document.getElementById('editProdutoId').value = data.produtoId;
            document.getElementById('editEstoqueId').value = data.estoqueId;
            document.getElementById('editGrupoId').value = data.grupoId; // Guarda o grupo_id para o update do estoque

            document.getElementById('editNomeProduto').value = data.nomeProduto;
            document.getElementById('editPrecoProduto').value = parseFloat(data.preco);
            document.getElementById('editQuantidadeProduto').value = parseInt(data.quantidade);
            document.getElementById('editGrupoVariacaoAssociado').value = data.nomeGrupo; // Exibe o nome do grupo

            modalEditarProduto.show();
        }

        /**
         * Salva as alterações de um produto e sua entrada de estoque.
         */
        document.getElementById('btnSalvarEdicaoProduto').addEventListener('click', async function () {
            const produtoId = parseInt(document.getElementById('editProdutoId').value);
            const estoqueId = parseInt(document.getElementById('editEstoqueId').value);
            // const grupoId = document.getElementById('editGrupoId').value === '' ? null : parseInt(document.getElementById('editGrupoId').value);

            const novoNomeProduto = document.getElementById('editNomeProduto').value.trim();
            const novoPreco = parseFloat(document.getElementById('editPrecoProduto').value);
            const novaQuantidade = parseInt(document.getElementById('editQuantidadeProduto').value);
            const alertaProduto = document.getElementById('alertaProduto');

            if (novoNomeProduto === '' || isNaN(novoPreco) || isNaN(novaQuantidade)) {
                showAlert('Por favor, preencha todos os campos corretamente.', 'warning', alertaProduto);
                return;
            }

            // Atualiza produto e estoque
            try {
                const response = await fetch('/index.php?page=produtos', {
                    method: 'PUT', // Usamos PUT para atualização
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        produto_id: produtoId,
                        estoque_id: estoqueId,
                        nome: novoNomeProduto,
                        preco: novoPreco,
                        quantidade: novaQuantidade
                    })
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success', alertaProduto);
                    location.reload(); // Recarrega a página para atualizar a tabela (simplificado por agora)
                } else {
                    showAlert(result.message, 'danger', alertaProduto);
                }
                modalEditarProduto.hide();
            } catch (error) {
                console.error('Erro ao salvar edição:', error);
                showAlert('Erro ao conectar com o servidor.', 'danger', alertaProduto);
            }
        });

        /**
         * Confirma e executa a exclusão do produto/entrada de estoque selecionado.
         * Nota: Excluir um produto aqui significa remover a entrada de estoque associada.
         * Se um produto tiver múltiplas entradas de estoque (para diferentes grupos),
         * esta função excluirá apenas a entrada de estoque da linha clicada.
         * Para remover o produto completamente, todas as suas entradas de estoque devem ser removidas.
         */
        document.getElementById('btnConfirmarExclusao').addEventListener('click', async function () {
            if (itemExcluirTipo === 'produto') {
                const alertaProduto = document.getElementById('alertaProduto');

                // Remove a entrada de estoque específica
                try {
                    const response = await fetch('/index.php?page=produtos', {
                        method: 'DELETE', // Usamos DELETE para exclusão
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            estoque_id: itemExcluirEstoqueId
                        })
                    });
                    const result = await response.json();

                    if (result.success) {
                        showAlert(result.message, 'success', alertaProduto);
                        location.reload(); // Recarrega a página para atualizar a tabela
                    } else {
                        showAlert(result.message, 'danger', alertaProduto);
                    }
                    modalConfirmacaoExclusao.hide();
                } catch (error) {
                    console.error('Erro ao excluir:', error);
                    showAlert('Erro ao conectar com o servidor.', 'danger', alertaProduto);
                }
            }
            itemExcluirTipo = ''; // Reseta as variáveis de controle
            itemExcluirProdutoId = null;
            itemExcluirEstoqueId = null;
        });

        // --- Event Listener de Cadastro ---

        // Formulário de Cadastro de Produto
        document.getElementById('formCadastrarProduto').addEventListener('submit', async function (event) {
            event.preventDefault(); // Evita o recarregamento da página
            const nomeProdutoInput = document.getElementById('nomeProduto');
            const precoProdutoInput = document.getElementById('precoProduto');
            const quantidadeProdutoInput = document.getElementById('quantidadeProduto');
            const grupoVariacaoProdutoSelect = document.getElementById('grupoVariacaoProduto');
            const alertaProduto = document.getElementById('alertaProduto');

            const nome = nomeProdutoInput.value.trim();
            const preco = parseFloat(precoProdutoInput.value);
            const quantidade = parseInt(quantidadeProdutoInput.value);
            const grupoId = grupoVariacaoProdutoSelect.value === '' ? null : parseInt(grupoVariacaoProdutoSelect.value);

            if (nome === '' || isNaN(preco) || isNaN(quantidade)) {
                showAlert('Por favor, preencha todos os campos corretamente.', 'warning', alertaProduto);
                return;
            }

            // Cadastra estoque para este produto e grupo
            try {
                const response = await fetch('/index.php?page=produtos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nome: nome,
                        preco: preco,
                        quantidade: quantidade,
                        grupo_id: grupoId
                    })
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success', alertaProduto);
                    nomeProdutoInput.value = '';
                    precoProdutoInput.value = '';
                    quantidadeProdutoInput.value = '';
                    grupoVariacaoProdutoSelect.value = ''; // Reseta a seleção
                    location.reload(); // Recarrega a página para atualizar a tabela
                } else {
                    showAlert(result.message, 'danger', alertaProduto);
                }
            } catch (error) {
                console.error('Erro ao cadastrar produto:', error);
                showAlert('Erro ao conectar com o servidor.', 'danger', alertaProduto);
            }
        });

        // --- Inicialização ---
        document.addEventListener('DOMContentLoaded', () => {
            adicionarListenersAcoesProduto();
        });
    </script>
</body>

</html>
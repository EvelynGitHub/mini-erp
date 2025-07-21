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
            if (nomeGrupo && nomeGrupo !== 'N/A (Sem SKU)') {
                nomeCompleto += ` (SKU: ${nomeGrupo})`;
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
    document.getElementById('editGrupoVariacaoAssociado').value = data.nomeGrupo; // Exibe o nome do grupo/SKU

    modalEditarProduto.show();
}

/**
 * Salva as alterações de um produto e sua entrada de estoque.
 */
document.getElementById('btnSalvarEdicaoProduto').addEventListener('click', async function () {
    const produtoId = parseInt(document.getElementById('editProdutoId').value);
    const estoqueId = parseInt(document.getElementById('editEstoqueId').value);
    const novoNomeProduto = document.getElementById('editNomeProduto').value.trim();
    const novoPreco = parseFloat(document.getElementById('editPrecoProduto').value);
    const novaQuantidade = parseInt(document.getElementById('editQuantidadeProduto').value);
    const alertaProduto = document.getElementById('alertaProduto'); // Usar o alerta principal da tela

    if (novoNomeProduto === '' || isNaN(novoPreco) || isNaN(novaQuantidade)) {
        showAlert('Por favor, preencha todos os campos corretamente.', 'warning', alertaProduto);
        return;
    }

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
            location.reload(); // Recarrega a página para atualizar a tabela
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
 */
document.getElementById('btnConfirmarExclusao').addEventListener('click', async function () {
    if (itemExcluirTipo === 'produto') {
        const alertaProduto = document.getElementById('alertaProduto'); // Usar o alerta principal da tela
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

// --- Event Listener de Cadastro de NOVO Produto ---
document.getElementById('formCadastrarProduto').addEventListener('submit', async function (event) {
    event.preventDefault();
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
                grupo_id: grupoId,
                // Não envia produto_id, indicando que é um novo produto
            })
        });
        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success', alertaProduto);
            nomeProdutoInput.value = '';
            precoProdutoInput.value = '';
            quantidadeProdutoInput.value = '';
            grupoVariacaoProdutoSelect.value = '';
            location.reload(); // Recarrega a página para atualizar a tabela
        } else {
            showAlert(result.message, 'danger', alertaProduto);
        }
    } catch (error) {
        console.error('Erro ao cadastrar produto:', error);
        showAlert('Erro ao conectar com o servidor.', 'danger', alertaProduto);
    }
});

// --- Event Listener de Adicionar Estoque para Produto EXISTENTE ---
document.getElementById('formAdicionarEstoque').addEventListener('submit', async function (event) {
    event.preventDefault();
    const selectProdutoExistente = document.getElementById('selectProdutoExistente');
    const selectGrupoVariacaoEstoque = document.getElementById('selectGrupoVariacaoEstoque');
    const precoEstoqueExistente = document.getElementById('precoEstoqueExistente');
    const quantidadeEstoqueExistente = document.getElementById('quantidadeEstoqueExistente');
    const alertaAdicionarEstoque = document.getElementById('alertaAdicionarEstoque'); // Alerta específico para este formulário

    const produtoId = selectProdutoExistente.value === '' ? null : parseInt(selectProdutoExistente.value);
    const grupoId = selectGrupoVariacaoEstoque.value === '' ? null : parseInt(selectGrupoVariacaoEstoque.value);
    const preco = parseFloat(precoEstoqueExistente.value);
    const quantidade = parseInt(quantidadeEstoqueExistente.value);

    if (produtoId === null || isNaN(preco) || isNaN(quantidade)) {
        showAlert('Por favor, selecione um produto e preencha preço/quantidade corretamente.', 'warning', alertaAdicionarEstoque);
        return;
    }

    // O grupoId pode ser null se o SKU for "Nenhum"
    if (grupoId === null && selectGrupoVariacaoEstoque.value !== '') {
        showAlert('Por favor, selecione um SKU válido ou "Nenhum".', 'warning', alertaAdicionarEstoque);
        return;
    }


    try {
        const response = await fetch('/index.php?page=estoque', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                produto_id: produtoId, // Envia o ID do produto existente
                grupo_id: grupoId, // Envia o ID do grupo de variação (SKU)
                preco: preco,
                quantidade: quantidade
                // Não envia 'nome', indicando que não é um novo produto
            })
        });
        const result = await response.json();

        if (result.success) {
            showAlert(result.message, 'success', alertaAdicionarEstoque);
            selectProdutoExistente.value = '';
            selectGrupoVariacaoEstoque.value = '';
            precoEstoqueExistente.value = '';
            quantidadeEstoqueExistente.value = '';
            location.reload(); // Recarrega a página para atualizar a tabela
        } else {
            showAlert(result.message, 'danger', alertaAdicionarEstoque);
        }
    } catch (error) {
        console.error('Erro ao adicionar estoque:', error);
        showAlert('Erro ao conectar com o servidor.', 'danger', alertaAdicionarEstoque);
    }
});


// --- Inicialização ---
document.addEventListener('DOMContentLoaded', () => {
    adicionarListenersAcoesProduto(); // Adiciona listeners aos botões de ação da tabela
});
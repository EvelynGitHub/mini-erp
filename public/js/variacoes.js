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
 * @param {Array < number >} [selectedVariations=[]] - IDs das variações que devem estar marcadas (para edição).
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
    * @param {Array < number >} variacaoIds - IDs das variações associadas ao grupo.
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
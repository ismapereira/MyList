// Função de toast (se não existir)
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    
    toast.classList.add('toast', 'fixed', 'top-4', 'right-4', 'z-50', 'p-4', 'rounded-lg', 'shadow-lg', 'transition-all', 'duration-300', 'ease-in-out');
    
    switch(type) {
        case 'success':
            toast.classList.add('bg-green-500', 'text-white');
            break;
        case 'error':
            toast.classList.add('bg-red-500', 'text-white');
            break;
        case 'warning':
            toast.classList.add('bg-yellow-500', 'text-black');
            break;
        default:
            toast.classList.add('bg-blue-500', 'text-white');
    }
    
    toast.textContent = message;
    toastContainer.appendChild(toast);
    
    // Remover toast após 3 segundos
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-full');
        setTimeout(() => {
            toastContainer.removeChild(toast);
        }, 300);
    }, 3000);
}

// Criar container de toast se não existir
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.classList.add('fixed', 'top-4', 'right-4', 'z-50', 'space-y-2');
    document.body.appendChild(container);
    return container;
}

// Funções para manipulação de listas
let editarListaModal, editarListaForm, fecharModalEditar;

document.addEventListener('DOMContentLoaded', function() {
    // Elementos do modal de edição
    editarListaModal = document.getElementById('editarListaModal');
    editarListaForm = document.getElementById('editarListaForm');
    fecharModalEditar = document.getElementById('fecharModalEditar');

    // Fechar modal de edição
    fecharModalEditar?.addEventListener('click', () => {
        editarListaModal.classList.add('hidden');
    });

    // Submeter formulário de edição
    editarListaForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'editar_lista',
            lista_id: document.getElementById('editarListaId').value,
            nome: document.getElementById('editarNomeLista').value.trim(),
            descricao: document.getElementById('editarDescricaoLista').value.trim()
        };

        fetch('lista_crud_ajax.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                editarListaModal.classList.add('hidden');
                recarregarListas();
            } else {
                showToast(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao editar lista', 'error');
        });
    });
});

// Variáveis globais para gerenciamento de itens
let listaIdAtual = null;
let itensLista = [];

// Função para abrir modal de edição de lista com itens
window.abrirModalEditar = function(listaId) {
    console.log('Abrindo modal para lista ID:', listaId);
    listaIdAtual = listaId;
    
    fetch(`lista_crud_ajax.php?action=obter_lista&lista_id=${listaId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Resposta recebida:', response);
        console.log('Status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Dados recebidos:', data);
        if (data.success) {
            // Preencher dados da lista
            document.getElementById('editarListaId').value = data.lista.id;
            document.getElementById('editarNomeLista').value = data.lista.nome;
            document.getElementById('editarDescricaoLista').value = data.lista.descricao || '';

            // Popular lista de itens
            itensLista = data.itens || [];
            atualizarListaItens();

            // Abrir modal
            editarListaModal.classList.remove('hidden');
        } else {
            showToast(data.error || 'Erro ao carregar dados da lista', 'error');
        }
    })
    .catch(error => {
        console.error('Erro completo:', error);
        showToast('Erro ao carregar dados da lista: ' + error.message, 'error');
    });
};

// Função para atualizar lista de itens no modal
function atualizarListaItens() {
    const listaItensContainer = document.getElementById('listaItensEditar');
    listaItensContainer.innerHTML = ''; // Limpar lista atual

    if (itensLista.length === 0) {
        listaItensContainer.innerHTML = '<p class="text-gray-500 text-center">Nenhum item na lista</p>';
        return;
    }

    itensLista.forEach((item, index) => {
        const itemElemento = document.createElement('div');
        itemElemento.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg mb-2';
        itemElemento.innerHTML = `
            <div class="flex items-center space-x-3 flex-grow">
                <input 
                    type="checkbox" 
                    class="item-checkbox" 
                    ${item.comprado ? 'checked' : ''}
                    data-item-id="${item.id}"
                >
                <div class="flex-grow">
                    <span class="font-medium">${item.nome}</span>
                    <span class="text-gray-500 ml-2">${item.quantidade || ''} ${item.unidade || ''}</span>
                </div>
            </div>
            <div>
                <button class="text-blue-500 hover:text-blue-700 mr-2 editar-item" data-index="${index}">
                    <i data-feather="edit-2" class="w-4 h-4"></i>
                </button>
                <button class="text-red-500 hover:text-red-700 remover-item" data-item-id="${item.id}">
                    <i data-feather="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        listaItensContainer.appendChild(itemElemento);
    });

    // Recarregar ícones do Feather
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Adicionar event listeners para edição e remoção de itens
    document.querySelectorAll('.editar-item').forEach(botao => {
        botao.addEventListener('click', abrirModalEditarItem);
    });

    document.querySelectorAll('.remover-item').forEach(botao => {
        botao.addEventListener('click', removerItem);
    });

    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', marcarItemComprado);
    });
}

// Modal para adicionar/editar item
const modalItemLista = document.getElementById('modalItemLista');
const formItemLista = document.getElementById('formItemLista');
let itemEditandoIndex = null;

// Abrir modal para adicionar item
document.getElementById('adicionarItemBtn').addEventListener('click', () => {
    formItemLista.reset();
    itemEditandoIndex = null;
    document.getElementById('tituloModalItem').textContent = 'Adicionar Item';
    modalItemLista.classList.remove('hidden');
});

// Abrir modal para editar item
function abrirModalEditarItem(evento) {
    const index = parseInt(evento.currentTarget.dataset.index);
    const item = itensLista[index];

    document.getElementById('nomeItem').value = item.nome;
    document.getElementById('quantidadeItem').value = item.quantidade || '';
    document.getElementById('unidadeItem').value = item.unidade || '';

    itemEditandoIndex = index;
    document.getElementById('tituloModalItem').textContent = 'Editar Item';
    modalItemLista.classList.remove('hidden');
}

// Salvar item (adicionar ou editar)
formItemLista.addEventListener('submit', function(e) {
    e.preventDefault();

    const nome = document.getElementById('nomeItem').value.trim();
    const quantidade = document.getElementById('quantidadeItem').value.trim() || null;
    const unidade = document.getElementById('unidadeItem').value.trim() || null;

    if (itemEditandoIndex !== null) {
        // Editar item existente
        const itemAntigo = itensLista[itemEditandoIndex];
        
        fetch('lista_crud_ajax.php?action=editar_item', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                lista_id: listaIdAtual,
                item_id: itemAntigo.id,
                nome: nome,
                quantidade: quantidade,
                unidade: unidade
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar item na lista local
                itensLista[itemEditandoIndex] = {
                    ...itemAntigo,
                    nome: nome,
                    quantidade: quantidade,
                    unidade: unidade
                };
                atualizarListaItens();
                showToast('Item editado com sucesso', 'success');
                modalItemLista.classList.add('hidden');
            } else {
                showToast(data.error || 'Erro ao editar item', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao editar item', 'error');
        });
    } else {
        // Adicionar novo item
        fetch('lista_crud_ajax.php?action=adicionar_item', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                lista_id: listaIdAtual,
                nome: nome,
                quantidade: quantidade,
                unidade: unidade
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Adicionar item à lista local
                itensLista.push({
                    id: data.item_id,
                    nome: nome,
                    quantidade: quantidade,
                    unidade: unidade,
                    comprado: 0
                });
                atualizarListaItens();
                showToast('Item adicionado com sucesso', 'success');
                modalItemLista.classList.add('hidden');
            } else {
                showToast(data.error || 'Erro ao adicionar item', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao adicionar item', 'error');
        });
    }
});

// Remover item da lista
function removerItem(evento) {
    const itemId = evento.currentTarget.dataset.itemId;
    
    fetch('lista_crud_ajax.php?action=remover_item', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            lista_id: listaIdAtual,
            item_id: itemId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover item da lista local
            itensLista = itensLista.filter(item => item.id != itemId);
            atualizarListaItens();
            showToast('Item removido com sucesso', 'success');
        } else {
            showToast(data.error || 'Erro ao remover item', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao remover item', 'error');
    });
}

// Marcar item como comprado/não comprado
function marcarItemComprado(evento) {
    const itemId = evento.target.dataset.itemId;
    const comprado = evento.target.checked ? 1 : 0;

    fetch('lista_crud_ajax.php?action=marcar_item', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            lista_id: listaIdAtual,
            item_id: itemId,
            comprado: comprado
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar item na lista local
            const itemIndex = itensLista.findIndex(item => item.id == itemId);
            if (itemIndex !== -1) {
                itensLista[itemIndex].comprado = comprado;
                atualizarListaItens();
            }
            showToast(comprado ? 'Item marcado como comprado' : 'Item desmarcado', 'success');
        } else {
            showToast(data.error || 'Erro ao marcar item', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao marcar item', 'error');
    });
}

// Fechar modais
document.querySelectorAll('.fechar-modal').forEach(botao => {
    botao.addEventListener('click', function() {
        this.closest('.modal').classList.add('hidden');
    });
});

// Marcar lista como concluída
window.marcarComoConcluida = function(listaId) {
    if (confirm('Deseja realmente marcar esta lista como concluída? Todos os itens serão marcados como concluídos.')) {
        fetch('lista_crud_ajax.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                action: 'marcar_concluida',
                lista_id: listaId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                recarregarListas();
            } else {
                showToast(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao marcar lista como concluída', 'error');
        });
    }
};

// Excluir lista
window.excluirLista = function(listaId) {
    if (confirm('Deseja realmente excluir esta lista? Esta ação não pode ser desfeita.')) {
        fetch('lista_crud_ajax.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                action: 'excluir_lista',
                lista_id: listaId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                recarregarListas();
            } else {
                showToast(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao excluir lista', 'error');
        });
    }
};

// Função para recarregar listas
function recarregarListas() {
    fetch('lista_crud_ajax.php?action=listar_listas', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar contadores
            document.getElementById('totalListas').textContent = data.listas.length;
            
            // Atualizar container de listas
            const container = document.getElementById('listasContainer');
            if (data.listas.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center bg-white p-6 rounded-xl shadow-md">
                        <i data-feather="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                        <h2 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma lista encontrada</h2>
                        <p class="text-gray-500 mb-6">Crie sua primeira lista para começar!</p>
                        <button onclick="document.getElementById('novaListaBtn').click()" class="btn-primary flex items-center space-x-2 px-4 py-2 rounded-lg mx-auto">
                            <i data-feather="plus" class="w-5 h-5"></i>
                            <span>Criar Primeira Lista</span>
                        </button>
                    </div>
                `;
            } else {
                container.innerHTML = data.listas.map(lista => `
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">${lista.nome}</h3>
                                <p class="text-gray-600">${lista.descricao || 'Sem descrição'}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="abrirModalEditar(${lista.id})" class="text-gray-500 hover:text-blue-600">
                                    <i data-feather="edit-2" class="w-5 h-5"></i>
                                </button>
                                <button onclick="marcarComoConcluida(${lista.id})" class="text-gray-500 hover:text-green-600">
                                    <i data-feather="check-circle" class="w-5 h-5"></i>
                                </button>
                                <button onclick="excluirLista(${lista.id})" class="text-gray-500 hover:text-red-600">
                                    <i data-feather="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Criada em ${new Date(lista.data_criacao).toLocaleDateString()}</span>
                            <span class="px-3 py-1 rounded-full ${getStatusClass(lista.status)}">${getStatusText(lista.status)}</span>
                        </div>
                    </div>
                `).join('');
            }
            feather.replace();
        } else {
            showToast('Erro ao carregar listas', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao carregar listas', 'error');
    });
}

// Função para obter classe CSS do status
function getStatusClass(status) {
    switch (status) {
        case 'concluida':
            return 'bg-green-100 text-green-800';
        case 'em_andamento':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Função para obter texto do status
function getStatusText(status) {
    switch (status) {
        case 'concluida':
            return 'Concluída';
        case 'em_andamento':
            return 'Em Andamento';
        default:
            return 'Nova';
    }
}

// Carregar listas ao iniciar
recarregarListas();

<?php
session_start();
require_once 'config/database.php';
require_once 'models/Lista.php';

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    error_log("Usuário não autenticado tentando acessar lista.php");
    header('Location: login.php');
    exit();
}

// Verificar se ID da lista foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    error_log("Tentativa de acesso à lista.php sem ID");
    header('Location: dashboard.php');
    exit();
}

error_log("Acessando lista.php - ID: " . $_GET['id'] . " - Usuário: " . $_SESSION['user_id']);

// Inicializar lista
$lista = new Lista();
$lista->id = $_GET['id'];
$lista->usuario_id = $_SESSION['user_id'];

// Buscar detalhes da lista
$detalhesLista = $lista->obterDetalhesLista();
error_log("Detalhes da lista: " . print_r($detalhesLista, true));

if (!$detalhesLista || $detalhesLista['usuario_id'] != $_SESSION['user_id']) {
    error_log("Lista não encontrada ou sem permissão - ID: " . $_GET['id'] . " - Usuário: " . $_SESSION['user_id']);
    header('Location: dashboard.php');
    exit();
}

// Buscar itens da lista
$itens = $lista->buscarItens();
error_log("Itens da lista: " . print_r($itens, true));
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($detalhesLista['nome']); ?> - MyList</title>
    <link href="assets/css/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body class="bg-gray-50 antialiased">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <?php echo htmlspecialchars($detalhesLista['nome']); ?>
                    </h1>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($detalhesLista['descricao'] ?? 'Sem descrição'); ?>
                    </p>
                </div>
                <a href="dashboard.php" class="text-blue-600 hover:text-blue-800">
                    <i data-feather="arrow-left" class="w-6 h-6"></i>
                </a>
            </div>

            <!-- Adicionar Item -->
            <form id="formulario-adicionar-item" class="mb-6 flex space-x-2">
                <input type="hidden" name="lista_id" value="<?php echo htmlspecialchars($detalhesLista['id']); ?>">
                <input 
                    type="text" 
                    name="nome" 
                    id="nomeItem" 
                    placeholder="Nome do item" 
                    required 
                    class="flex-grow px-4 py-2 rounded-lg bg-gray-100 border focus:border-blue-500 focus:outline-none"
                >
                <input 
                    type="number" 
                    name="quantidade" 
                    id="quantidadeItem" 
                    placeholder="Qtd" 
                    min="0.1" 
                    step="0.1" 
                    required 
                    class="w-24 px-4 py-2 rounded-lg bg-gray-100 border focus:border-blue-500 focus:outline-none"
                >
                <select 
                    name="unidade" 
                    id="unidadeItem" 
                    required 
                    class="w-32 px-4 py-2 rounded-lg bg-gray-100 border focus:border-blue-500 focus:outline-none"
                >
                    <option value="un">Unidade</option>
                    <option value="kg">Kg</option>
                    <option value="g">Gramas</option>
                    <option value="l">Litros</option>
                    <option value="ml">Mililitros</option>
                </select>
                <button 
                    type="submit" 
                    class="btn-primary px-4 py-2 rounded-lg flex items-center space-x-2"
                >
                    <i data-feather="plus" class="w-5 h-5"></i>
                </button>
            </form>

            <!-- Lista de Itens -->
            <div id="listaItens" class="space-y-2">
                <?php if(empty($itens)): ?>
                    <div class="text-center text-gray-500 py-4">
                        Nenhum item adicionado ainda
                    </div>
                <?php else: ?>
                    <?php foreach($itens as $item): ?>
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <input 
                                    type="checkbox" 
                                    class="item-checkbox" 
                                    data-item-id="<?php echo $item['id']; ?>"
                                    <?php echo $item['comprado'] ? 'checked' : ''; ?>
                                >
                                <span class="<?php echo $item['comprado'] ? 'line-through text-gray-500' : ''; ?>">
                                    <?php echo htmlspecialchars($item['nome']); ?>
                                </span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-600">
                                    <?php echo htmlspecialchars($item['quantidade'] . ' ' . $item['unidade']); ?>
                                </span>
                                <button class="text-red-500 hover:text-red-700 excluir-item" data-item-id="<?php echo $item['id']; ?>">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Contadores -->
            <div class="mt-6 flex justify-between text-sm text-gray-600">
                <div>
                    Total de itens: <span id="totalItens"><?php echo count($itens); ?></span>
                </div>
                <div>
                    Itens comprados: <span id="itensConcluidos"><?php echo array_reduce($itens, function($carry, $item) { 
                        return $carry + ($item['comprado'] ? 1 : 0); 
                    }, 0); ?></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inicializar ícones Feather
        feather.replace();

        const listaId = <?php echo $detalhesLista['id']; ?>;
        const totalItensElement = document.getElementById('totalItens');
        const itensConcluidosElement = document.getElementById('itensConcluidos');
        const listaItensElement = document.getElementById('listaItens');

        // Função para mostrar toast de notificação
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        }

        // Adicionar item
        document.getElementById('formulario-adicionar-item').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Log para debug
            console.log('Enviando dados:', {
                lista_id: formData.get('lista_id'),
                nome: formData.get('nome'),
                quantidade: formData.get('quantidade'),
                unidade: formData.get('unidade')
            });

            fetch('ajax/adicionar_item.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // Se a resposta não for JSON, lê como texto para debug
                    const text = await response.text();
                    console.error('Resposta não-JSON recebida:', text);
                    throw new Error('Resposta inválida do servidor');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Resposta:', data);
                if (data.sucesso) {
                    // Criar novo elemento de lista
                    const novoItem = document.createElement('div');
                    novoItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg';
                    novoItem.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" 
                                   class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 item-checkbox" 
                                   data-item-id="${data.item_id}"
                            >
                            <span class="text-gray-700">${formData.get('nome')}</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-600">
                                ${formData.get('quantidade')} ${formData.get('unidade')}
                            </span>
                            <button class="text-red-500 hover:text-red-700 excluir-item" data-item-id="${data.item_id}">
                                <i data-feather="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    `;
                    
                    // Remover mensagem de "nenhum item" se existir
                    const mensagemVazia = document.querySelector('.text-gray-500.py-4');
                    if (mensagemVazia) {
                        mensagemVazia.remove();
                    }
                    
                    // Adicionar à lista
                    const listaItens = document.querySelector('#listaItens');
                    if (!listaItens) {
                        console.error('Elemento #listaItens não encontrado');
                        throw new Error('Elemento #listaItens não encontrado');
                    }
                    listaItens.appendChild(novoItem);
                    
                    // Atualizar contadores
                    const totalItens = document.getElementById('totalItens');
                    if (totalItens) {
                        totalItens.textContent = parseInt(totalItens.textContent || '0') + 1;
                    }
                    
                    // Reinicializar ícones
                    feather.replace();
                    
                    // Limpar formulário
                    this.reset();
                    
                    // Configurar listeners para o novo item
                    setupItemListeners(novoItem);
                    
                    // Mostrar mensagem de sucesso
                    showToast(data.mensagem || 'Item adicionado com sucesso', 'success');
                } else {
                    throw new Error(data.mensagem || 'Erro ao adicionar item');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast(error.message, 'error');
            });
        });

        // Função para configurar listeners de itens
        function setupItemListeners(itemElement) {
            if (!itemElement) {
                console.error('Item element não encontrado');
                return;
            }

            // Marcar/desmarcar item
            const checkbox = itemElement.querySelector('.item-checkbox');
            if (!checkbox) {
                console.error('Checkbox não encontrado no item');
                return;
            }

            const span = itemElement.querySelector('span');
            const excluirBotao = itemElement.querySelector('.excluir-item');

            checkbox.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                const comprado = this.checked;
                
                fetch('ajax/marcar_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `item_id=${itemId}&comprado=${comprado ? 1 : 0}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.sucesso) {
                        // Atualizar visual do item
                        if (comprado) {
                            span.classList.add('line-through', 'text-gray-500');
                            const itensConcluidos = document.getElementById('itensConcluidos');
                            if (itensConcluidos) {
                                itensConcluidos.textContent = parseInt(itensConcluidos.textContent) + 1;
                            }
                        } else {
                            span.classList.remove('line-through', 'text-gray-500');
                            const itensConcluidos = document.getElementById('itensConcluidos');
                            if (itensConcluidos) {
                                itensConcluidos.textContent = parseInt(itensConcluidos.textContent) - 1;
                            }
                        }
                        showToast('Status do item atualizado');
                    } else {
                        throw new Error(data.mensagem || 'Erro ao atualizar status');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast(error.message, 'error');
                    // Reverter checkbox para estado anterior
                    this.checked = !comprado;
                });
            });

            if (excluirBotao) {
                excluirBotao.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    if (confirm('Tem certeza que deseja excluir este item?')) {
                        fetch('ajax/remover_item.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `item_id=${itemId}`
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.sucesso) {
                                itemElement.remove();
                                const totalItens = document.getElementById('totalItens');
                                if (totalItens) {
                                    totalItens.textContent = parseInt(totalItens.textContent) - 1;
                                }
                                showToast('Item removido com sucesso');
                            } else {
                                throw new Error(data.mensagem || 'Erro ao remover item');
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            showToast(error.message, 'error');
                        });
                    }
                });
            }
        }

        // Configurar listeners para itens existentes
        document.querySelectorAll('#listaItens > div').forEach(setupItemListeners);
    </script>
</body>
</html>

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
            <form id="adicionarItemForm" class="mb-6 flex space-x-2">
                <input 
                    type="text" 
                    id="nomeItem" 
                    placeholder="Nome do item" 
                    required 
                    class="flex-grow px-4 py-2 rounded-lg bg-gray-100 border focus:border-blue-500 focus:outline-none"
                >
                <input 
                    type="number" 
                    id="quantidadeItem" 
                    placeholder="Qtd" 
                    min="0.1" 
                    step="0.1" 
                    required 
                    class="w-24 px-4 py-2 rounded-lg bg-gray-100 border focus:border-blue-500 focus:outline-none"
                >
                <select 
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
                    <span>Adicionar</span>
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
        document.getElementById('adicionarItemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const nome = document.getElementById('nomeItem').value.trim();
            const quantidade = document.getElementById('quantidadeItem').value;
            const unidade = document.getElementById('unidadeItem').value;

            if (nome && quantidade && unidade) {
                fetch('lista_ajax.php?action=adicionar_item', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        lista_id: listaId,
                        nome: nome,
                        quantidade: quantidade,
                        unidade: unidade
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Adicionar item à lista visualmente
                        const novoItem = document.createElement('div');
                        novoItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg';
                        novoItem.innerHTML = `
                            <div class="flex items-center space-x-3">
                                <input 
                                    type="checkbox" 
                                    class="item-checkbox" 
                                    data-item-id="${data.item_id}"
                                >
                                <span>${nome}</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-600">
                                    ${quantidade} ${unidade}
                                </span>
                                <button class="text-red-500 hover:text-red-700 excluir-item" data-item-id="${data.item_id}">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `;
                        
                        listaItensElement.appendChild(novoItem);
                        feather.replace(); // Recarregar ícones
                        
                        // Atualizar contadores
                        totalItensElement.textContent = parseInt(totalItensElement.textContent) + 1;
                        
                        // Limpar formulário
                        document.getElementById('nomeItem').value = '';
                        document.getElementById('quantidadeItem').value = '';
                        document.getElementById('unidadeItem').value = 'un';
                        
                        showToast(data.message);
                        
                        // Adicionar event listeners para novos elementos
                        setupItemListeners(novoItem);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro ao adicionar item', 'error');
                });
            }
        });

        // Função para configurar listeners de itens
        function setupItemListeners(itemElement) {
            // Marcar/desmarcar item
            const checkbox = itemElement.querySelector('.item-checkbox');
            const span = itemElement.querySelector('span');
            const excluirBotao = itemElement.querySelector('.excluir-item');

            checkbox.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                const status = this.checked ? 'comprado' : 'pendente';
                
                fetch('lista_ajax.php?action=atualizar_status', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        lista_id: listaId,
                        item_id: itemId,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar visual do item
                        if (status === 'comprado') {
                            span.classList.add('line-through', 'text-gray-500');
                            itensConcluidosElement.textContent = 
                                parseInt(itensConcluidosElement.textContent) + 1;
                        } else {
                            span.classList.remove('line-through', 'text-gray-500');
                            itensConcluidosElement.textContent = 
                                parseInt(itensConcluidosElement.textContent) - 1;
                        }
                        showToast(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro ao atualizar status', 'error');
                    checkbox.checked = !checkbox.checked; // Reverter checkbox
                });
            });

            // Excluir item
            excluirBotao.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                
                fetch('lista_ajax.php?action=excluir_item', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover item da lista
                        itemElement.remove();
                        
                        // Atualizar contadores
                        const totalItensElement = document.getElementById('totalItens');
                        totalItensElement.textContent = parseInt(totalItensElement.textContent) - 1;
                        
                        if (checkbox.checked) {
                            const itensConcluidosElement = document.getElementById('itensConcluidos');
                            itensConcluidosElement.textContent = parseInt(itensConcluidosElement.textContent) - 1;
                        }
                    } else {
                        throw new Error(data.error || 'Erro ao excluir item');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir item. Por favor, tente novamente.');
                });
            });
        }

        // Configurar listeners para itens existentes
        document.querySelectorAll('#listaItens > div').forEach(setupItemListeners);
    </script>
</body>
</html>

<?php
session_start();
require_once 'config/database.php';
require_once 'models/Lista.php';

// Verificar autenticação
if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se ID da lista foi passado
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

// Inicializar lista
$lista = new Lista();
$lista->id = $_GET['id'];
$lista->usuario_id = $_SESSION['usuario_id'];

// Buscar detalhes da lista
$detalhesLista = $lista->obterDetalhesLista();
if(!$detalhesLista) {
    header('Location: dashboard.php');
    exit();
}

// Buscar itens da lista
$itens = $lista->buscarItens();
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
                        Nenhum item na lista
                    </div>
                <?php else: ?>
                    <?php foreach($itens as $item): ?>
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <input 
                                    type="checkbox" 
                                    class="item-checkbox" 
                                    data-item-id="<?php echo $item['id']; ?>"
                                    <?php echo $item['status'] == 'concluido' ? 'checked' : ''; ?>
                                >
                                <span class="<?php echo $item['status'] == 'concluido' ? 'line-through text-gray-500' : ''; ?>">
                                    <?php echo htmlspecialchars($item['nome']); ?>
                                </span>
                            </div>
                            <div class="text-gray-600">
                                <?php echo $item['quantidade'] . ' ' . $item['unidade']; ?>
                                <button class="ml-2 text-red-500 hover:text-red-700 excluir-item" data-item-id="<?php echo $item['id']; ?>">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Resumo da Lista -->
            <div class="mt-6 pt-4 border-t flex justify-between items-center">
                <div>
                    <span class="text-gray-600">Total de itens:</span>
                    <span id="totalItens" class="font-bold"><?php echo count($itens); ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Itens comprados:</span>
                    <span id="itensConcluidos" class="font-bold">
                        <?php 
                        $concluidos = array_filter($itens, function($item) {
                            return $item['status'] == 'concluido';
                        });
                        echo count($concluidos); 
                        ?>
                    </span>
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
                            <div class="text-gray-600">
                                ${quantidade} ${unidade}
                                <button class="ml-2 text-red-500 hover:text-red-700 excluir-item" data-item-id="${data.item_id}">
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
                const status = this.checked ? 'concluido' : 'pendente';
                
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
                        if (status === 'concluido') {
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
                
                fetch('lista_ajax.php?action=remover_item', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        lista_id: listaId,
                        item_id: itemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover item da lista
                        listaItensElement.removeChild(itemElement);
                        
                        // Atualizar contadores
                        totalItensElement.textContent = parseInt(totalItensElement.textContent) - 1;
                        
                        // Se o item estava concluído, atualizar contador de concluídos
                        const checkbox = itemElement.querySelector('.item-checkbox');
                        if (checkbox.checked) {
                            itensConcluidosElement.textContent = 
                                parseInt(itensConcluidosElement.textContent) - 1;
                        }
                        
                        showToast(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro ao remover item', 'error');
                });
            });
        }

        // Configurar listeners para itens existentes
        document.querySelectorAll('#listaItens > div').forEach(setupItemListeners);
    </script>
</body>
</html>

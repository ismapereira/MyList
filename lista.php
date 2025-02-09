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

        // Adicionar item
        document.getElementById('adicionarItemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const nome = document.getElementById('nomeItem').value.trim();
            const quantidade = document.getElementById('quantidadeItem').value;
            const unidade = document.getElementById('unidadeItem').value;

            if (nome && quantidade && unidade) {
                // Aqui você faria uma chamada AJAX para adicionar o item
                alert(`Item adicionado: ${nome} (${quantidade} ${unidade})`);
                
                // Limpar formulário
                document.getElementById('nomeItem').value = '';
                document.getElementById('quantidadeItem').value = '';
                document.getElementById('unidadeItem').value = 'un';
            }
        });

        // Marcar/desmarcar item
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                const status = this.checked ? 'concluido' : 'pendente';
                
                // Aqui você faria uma chamada AJAX para atualizar o status
                alert(`Item ${itemId} marcado como ${status}`);
            });
        });

        // Excluir item
        document.querySelectorAll('.excluir-item').forEach(botao => {
            botao.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                
                // Aqui você faria uma chamada AJAX para excluir o item
                alert(`Excluir item ${itemId}`);
            });
        });
    </script>
</body>
</html>

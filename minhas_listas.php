<?php
session_start();
require_once 'config/database.php';
require_once 'models/Lista.php';
require_once 'models/Usuario.php';

// Verificar autenticação
if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Recuperar informações do usuário
$usuario = new Usuario();
$usuario->id = $_SESSION['usuario_id'];
$usuario->obterPorId();

// Recuperar listas do usuário
$lista = new Lista();
$lista->usuario_id = $_SESSION['usuario_id'];
$listas = $lista->listarListasUsuario();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Listas - MyList</title>
    <link href="assets/css/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 bg-gray-50">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Minhas Listas</h1>
                    <p class="text-gray-600">Gerencie e organize suas listas</p>
                </div>
                <button id="novaListaBtn" class="btn-primary flex items-center space-x-2 px-4 py-2 rounded-lg">
                    <i data-feather="plus" class="w-5 h-5"></i>
                    <span>Nova Lista</span>
                </button>
            </header>

            <!-- Estatísticas Rápidas -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-500 text-sm">Total de Listas</h3>
                            <p id="totalListas" class="text-2xl font-bold text-blue-600"><?php echo count($listas); ?></p>
                        </div>
                        <i data-feather="clipboard" class="w-8 h-8 text-blue-500"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-500 text-sm">Itens Pendentes</h3>
                            <p id="itensPendentes" class="text-2xl font-bold text-yellow-600">0</p>
                        </div>
                        <i data-feather="clock" class="w-8 h-8 text-yellow-500"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-500 text-sm">Listas Concluídas</h3>
                            <p id="listasConcluidas" class="text-2xl font-bold text-green-600">0</p>
                        </div>
                        <i data-feather="check-circle" class="w-8 h-8 text-green-500"></i>
                    </div>
                </div>
            </section>

            <!-- Filtros e Ordenação -->
            <section class="mb-6">
                <div class="bg-white p-4 rounded-xl shadow-md">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block text-gray-700 mb-2">Filtrar por Status</label>
                            <select class="form-select w-full rounded-lg border-gray-300">
                                <option value="">Todos</option>
                                <option value="pendente">Pendentes</option>
                                <option value="concluida">Concluídas</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-gray-700 mb-2">Ordenar por</label>
                            <select class="form-select w-full rounded-lg border-gray-300">
                                <option value="data_desc">Mais Recentes</option>
                                <option value="data_asc">Mais Antigas</option>
                                <option value="nome">Nome</option>
                                <option value="pendentes">Itens Pendentes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Lista de Listas -->
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Suas Listas</h2>
                    <a href="#" class="text-blue-600 hover:underline">Ver Todas</a>
                </div>
                <div id="listasContainer" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- O conteúdo será carregado dinamicamente via JavaScript -->
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Criar Lista -->
    <?php include 'components/modal_criar_lista.php'; ?>
    
    <!-- Modal Editar Lista -->
    <?php include 'components/modal_editar_lista.php'; ?>

    <script src="https://unpkg.com/feather-icons"></script>
    <script src="assets/js/listas.js"></script>
    <script>
        feather.replace();
        
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
    </script>
</body>
</html>

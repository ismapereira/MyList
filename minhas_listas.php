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
                            <p id="totalListas" class="text-2xl font-bold text-blue-600">0</p>
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

    <script>
        // Função de notificação Toast
        function showToast(message, type = 'success') {
            // Criar container de toast se não existir
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
                document.body.appendChild(toastContainer);
            }

            // Criar elemento de toast
            const toast = document.createElement('div');
            toast.className = `
                px-4 py-2 rounded-lg shadow-lg text-white transition-all duration-300 
                ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}
                transform translate-x-full opacity-0
            `;
            toast.textContent = message;

            // Adicionar ao container
            toastContainer.appendChild(toast);

            // Animação de entrada
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 10);

            // Remover após alguns segundos
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    toastContainer.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Função para recarregar listas
        function recarregarListas() {
            console.log('Iniciando recarregamento de listas');  // Log de diagnóstico
            
            // Adicionar indicador de carregamento
            const listasContainer = document.getElementById('listasContainer');
            listasContainer.innerHTML = `
                <div class="col-span-full text-center">
                    <div class="spinner"></div>
                    <p class="text-gray-600 mt-2">Carregando listas...</p>
                </div>
            `;
            
            fetch('lista_crud_ajax.php?action=listar_listas', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Dados recebidos:', data);  // Log de diagnóstico
                
                if (data.success) {
                    // Limpar container
                    listasContainer.innerHTML = '';
                    
                    // Verificar se há listas
                    if (data.listas.length === 0) {
                        listasContainer.innerHTML = `
                            <div class="col-span-full text-center bg-white p-6 rounded-xl shadow-md">
                                <i data-feather="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                                <h2 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma lista encontrada</h2>
                                <p class="text-gray-500 mb-6">Crie sua primeira lista para começar!</p>
                                <button id="primeiraListaBtn" class="btn-primary flex items-center space-x-2 px-4 py-2 rounded-lg mx-auto">
                                    <i data-feather="plus" class="w-5 h-5"></i>
                                    <span>Criar Primeira Lista</span>
                                </button>
                            </div>
                        `;
                    } else {
                        // Renderizar listas
                        data.listas.forEach(lista => {
                            const listaElement = document.createElement('div');
                            listaElement.className = 'bg-white p-6 rounded-xl shadow-md';
                            listaElement.innerHTML = `
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">${lista.nome}</h3>
                                    <div class="flex space-x-2">
                                        <button class="text-blue-500 hover:text-blue-600">
                                            <i data-feather="edit-2" class="w-5 h-5"></i>
                                        </button>
                                        <button class="text-red-500 hover:text-red-600 excluir-lista" data-lista-id="${lista.id}">
                                            <i data-feather="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-gray-600 mb-4">${lista.descricao || 'Sem descrição'}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">
                                        Criada em ${new Date(lista.data_criacao).toLocaleDateString()}
                                    </span>
                                    <span class="text-sm font-semibold ${lista.itens_pendentes > 0 ? 'text-yellow-600' : 'text-green-600'}">
                                        ${lista.itens_pendentes > 0 ? lista.itens_pendentes + ' pendentes' : 'Concluída'}
                                    </span>
                                </div>
                            `;
                            listasContainer.appendChild(listaElement);
                        });
                    }

                    // Atualizar contadores
                    document.getElementById('totalListas').textContent = data.listas.length;
                    
                    let itensPendentes = 0;
                    let listasConcluidas = 0;
                    data.listas.forEach(lista => {
                        itensPendentes += parseInt(lista.itens_pendentes) || 0;
                        if (lista.itens_pendentes == 0) listasConcluidas++;
                    });
                    
                    document.getElementById('itensPendentes').textContent = itensPendentes;
                    document.getElementById('listasConcluidas').textContent = listasConcluidas;

                    // Adicionar eventos de exclusão
                    document.querySelectorAll('.excluir-lista').forEach(botao => {
                        botao.addEventListener('click', function() {
                            const listaId = this.getAttribute('data-lista-id');
                            
                            console.log('Tentando excluir lista:', listaId);  // Log de diagnóstico
                            
                            // Confirmar exclusão
                            if (confirm('Tem certeza que deseja excluir esta lista?')) {
                                fetch('lista_crud_ajax.php?action=excluir_lista', {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        lista_id: listaId
                                    })
                                })
                                .then(response => {
                                    console.log('Resposta do servidor:', response);  // Log de diagnóstico
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Dados recebidos:', data);  // Log de diagnóstico
                                    
                                    if (data.success) {
                                        // Mostrar notificação
                                        showToast('Lista excluída com sucesso!');

                                        // Recarregar listas
                                        console.log('Chamando recarregarListas após exclusão');  // Log de diagnóstico
                                        recarregarListas();
                                    } else {
                                        // Mostrar erro
                                        console.error('Erro na exclusão:', data.message);  // Log de erro
                                        showToast(data.message || 'Erro ao excluir lista', 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Erro completo:', error);  // Log de erro completo
                                    showToast('Erro ao excluir lista', 'error');
                                });
                            }
                        });
                    });

                    // Inicializar ícones Feather
                    feather.replace();
                } else {
                    // Mostrar erro
                    listasContainer.innerHTML = `
                        <div class="col-span-full text-center bg-white p-6 rounded-xl shadow-md">
                            <i data-feather="alert-triangle" class="w-16 h-16 mx-auto text-red-400 mb-4"></i>
                            <h2 class="text-xl font-semibold text-red-600 mb-2">Erro ao carregar listas</h2>
                            <p class="text-gray-500">${data.message || 'Não foi possível carregar suas listas'}</p>
                            <button onclick="recarregarListas()" class="btn-primary mt-4">
                                Tentar Novamente
                            </button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erro ao recarregar listas:', error);
                listasContainer.innerHTML = `
                    <div class="col-span-full text-center bg-white p-6 rounded-xl shadow-md">
                        <i data-feather="alert-triangle" class="w-16 h-16 mx-auto text-red-400 mb-4"></i>
                        <h2 class="text-xl font-semibold text-red-600 mb-2">Erro de Conexão</h2>
                        <p class="text-gray-500">Verifique sua conexão de internet</p>
                        <button onclick="recarregarListas()" class="btn-primary mt-4">
                            Tentar Novamente
                        </button>
                    </div>
                `;
            });
        }

        // Chamar recarregamento de listas ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Carregamento inicial da página');  // Log de diagnóstico
            recarregarListas();
        });
    </script>
</body>
</html>

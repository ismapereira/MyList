<?php
session_start();
require_once 'config/database.php';
require_once 'models/Lista.php';
require_once 'models/Usuario.php';

// Log para debug
error_log("Dashboard - Iniciando...");
error_log("Sessão atual: " . print_r($_SESSION, true));

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    error_log("Dashboard - Usuário não autenticado. Redirecionando para login...");
    header('Location: login.php');
    exit();
}

// Recuperar informações do usuário
$usuario = new Usuario();
$usuario->id = $_SESSION['user_id'];

error_log("Dashboard - Buscando informações do usuário ID: " . $usuario->id);
if (!$usuario->obterPorId()) {
    error_log("Dashboard - Erro ao buscar informações do usuário ID: " . $usuario->id);
    session_destroy();
    header('Location: login.php');
    exit();
}

// Recuperar listas do usuário
$lista = new Lista();
$lista->usuario_id = $_SESSION['user_id'];
error_log("Dashboard - Buscando listas do usuário ID: " . $lista->usuario_id);

$listas = $lista->listarListasUsuario();
error_log("Dashboard - Listas encontradas: " . print_r($listas, true));

// Calcular estatísticas
$totalListas = count($listas);
$itensPendentes = 0;
$listasConcluidas = 0;

foreach ($listas as $l) {
    $itensPendentes += $l['itens_pendentes'];
    if ($l['itens_pendentes'] == 0 && $l['total_itens'] > 0) {
        $listasConcluidas++;
    }
}

error_log("Dashboard - Estatísticas: Total Listas: $totalListas, Itens Pendentes: $itensPendentes, Listas Concluídas: $listasConcluidas");
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MyList</title>
    <link href="assets/css/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 overflow-x-hidden">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                    <p class="text-gray-600">Bem-vindo de volta, <?php echo htmlspecialchars(explode(' ', $usuario->nome)[0]); ?>!</p>
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
                            <p id="totalListas" class="text-2xl font-bold text-blue-600"><?php echo $totalListas; ?></p>
                        </div>
                        <i data-feather="clipboard" class="w-8 h-8 text-blue-500"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-500 text-sm">Itens Pendentes</h3>
                            <p id="itensPendentes" class="text-2xl font-bold text-yellow-600"><?php echo $itensPendentes; ?></p>
                        </div>
                        <i data-feather="clock" class="w-8 h-8 text-yellow-500"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-500 text-sm">Listas Concluídas</h3>
                            <p id="listasConcluidas" class="text-2xl font-bold text-green-600"><?php echo $listasConcluidas; ?></p>
                        </div>
                        <i data-feather="check-circle" class="w-8 h-8 text-green-500"></i>
                    </div>
                </div>
            </section>

            <!-- Listas Recentes -->
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Suas Listas</h2>
                    <a href="#" class="text-blue-600 hover:underline">Ver Todas</a>
                </div>

                <div class="grid gap-4" id="listasContainer">
                    <?php if(empty($listas)): ?>
                        <div class="bg-white p-6 rounded-xl shadow-md text-center">
                            <i data-feather="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                            <p class="text-gray-600">Você ainda não tem nenhuma lista.</p>
                            <button id="primeiraListaBtn" class="btn-primary mt-4 px-4 py-2 rounded-lg">
                                Criar Primeira Lista
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach($listas as $lista): ?>
                            <div class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition">
                                <div class="flex flex-col space-y-2">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col">
                                            <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($lista['nome']); ?></h3>
                                            <p class="text-sm text-gray-500">
                                                <?php echo $lista['itens_pendentes']; ?> itens pendentes
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <a href="lista.php?id=<?php echo $lista['id']; ?>" class="text-blue-600 hover:text-blue-800" title="Editar">
                                                <i data-feather="edit" class="w-5 h-5"></i>
                                            </a>
                                            <a href="export_pdf.php?lista_id=<?php echo $lista['id']; ?>" class="text-blue-600 hover:text-blue-800" title="Exportar PDF">
                                                <i data-feather="file-text" class="w-5 h-5"></i>
                                            </a>
                                            <button class="text-red-600 hover:text-red-800 excluir-lista" data-lista-id="<?php echo $lista['id']; ?>" title="Excluir">
                                                <i data-feather="trash-2" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Criar Lista -->
    <div id="criarListaModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg w-96 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Criar Nova Lista</h2>
            </div>
            
            <form id="novaListaForm" class="space-y-4">
                <div>
                    <label for="nomeLista" class="block text-gray-700 mb-2">Nome da Lista</label>
                    <input 
                        type="text" 
                        id="nomeLista" 
                        required 
                        class="w-full px-4 py-2 rounded-lg bg-gray-100 border focus:border-blue-500 focus:outline-none"
                        placeholder="Ex: Compras de Supermercado"
                    >
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelarModal" class="btn-secondary px-4 py-2 rounded-lg">
                        <span>Cancelar</span>
                    </button>
                    <button type="submit" class="btn-primary px-4 py-2 rounded-lg">
                        <span>Criar Lista</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

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

        // Função para recarregar as listas
        function recarregarListas() {
            console.log('Iniciando recarregamento de listas');  // Log de diagnóstico
            
            // Adicionar indicador de carregamento
            const listasContainer = document.getElementById('listasContainer');
            listasContainer.innerHTML = `
                <div class="flex justify-center items-center p-6">
                    <div class="spinner-border animate-spin inline-block w-8 h-8 border-4 border-blue-500 rounded-full" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            `;
            
            fetch('lista_crud_ajax.php?action=listar_listas', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'  // Evitar cache
                }
            })
            .then(response => {
                console.log('Resposta do servidor:', response);  // Log de diagnóstico
                console.log('Status da resposta:', response.status);  // Log de status
                
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data);  // Log de diagnóstico
                
                // Limpar container atual
                listasContainer.innerHTML = '';

                // Verificar se há listas
                if (!data.listas || data.listas.length === 0) {
                    console.log('Nenhuma lista encontrada');  // Log de diagnóstico
                    listasContainer.innerHTML = `
                        <div class="bg-white p-6 rounded-xl shadow-md text-center">
                            <i data-feather="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                            <p class="text-gray-600">Você ainda não tem nenhuma lista.</p>
                            <button id="primeiraListaBtn" class="btn-primary mt-4 px-4 py-2 rounded-lg">
                                Criar Primeira Lista
                            </button>
                        </div>
                    `;
                    feather.replace();
                    adicionarEventoPrimeiraLista();
                } else {
                    console.log(`Encontradas ${data.listas.length} listas`);  // Log de diagnóstico
                    
                    // Criar container de grid para as listas
                    const gridContainer = document.createElement('div');
                    gridContainer.className = 'grid gap-4';
                    listasContainer.appendChild(gridContainer);
                    
                    // Adicionar listas
                    data.listas.forEach(lista => {
                        const novaLista = document.createElement('div');
                        novaLista.className = 'bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition';
                        
                        // Formatar data
                        const data = new Date(lista.data_criacao);
                        const dataFormatada = data.toLocaleDateString('pt-BR');

                        novaLista.innerHTML = `
                            <div class="flex flex-col space-y-2">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <h3 class="text-lg font-semibold text-gray-900">${lista.nome}</h3>
                                        <p class="text-sm text-gray-500">
                                            ${lista.itens_pendentes} itens pendentes
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <a href="lista.php?id=${lista.id}" class="text-blue-600 hover:text-blue-800" title="Editar">
                                            <i data-feather="edit" class="w-5 h-5"></i>
                                        </a>
                                        <a href="export_pdf.php?lista_id=${lista.id}" class="text-blue-600 hover:text-blue-800" title="Exportar PDF">
                                            <i data-feather="file-text" class="w-5 h-5"></i>
                                        </a>
                                        <button class="text-red-600 hover:text-red-800 excluir-lista" data-lista-id="${lista.id}" title="Excluir">
                                            <i data-feather="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        gridContainer.appendChild(novaLista);
                        
                        // Adicionar evento de exclusão
                        const botaoExcluir = novaLista.querySelector('.excluir-lista');
                        adicionarEventoExclusaoLista(novaLista);
                    });

                    // Atualizar contadores
                    document.getElementById('totalListas').textContent = data.listas.length;
                    
                    let itensPendentes = 0;
                    data.listas.forEach(lista => {
                        itensPendentes += parseInt(lista.itens_pendentes) || 0;
                    });
                    document.getElementById('itensPendentes').textContent = itensPendentes;
                    
                    let listasConcluidas = 0;
                    data.listas.forEach(lista => {
                        if (lista.itens_pendentes == 0) {
                            listasConcluidas++;
                        }
                    });
                    document.getElementById('listasConcluidas').textContent = listasConcluidas;
                }

                // Recarregar ícones
                feather.replace();
            })
            .catch(error => {
                console.error('Erro ao recarregar listas:', error);  // Log de erro
                
                // Mostrar mensagem de erro no container
                listasContainer.innerHTML = `
                    <div class="bg-red-100 p-6 rounded-xl text-center">
                        <i data-feather="alert-triangle" class="w-16 h-16 mx-auto text-red-500 mb-4"></i>
                        <p class="text-red-700">Erro ao carregar listas. Tente novamente.</p>
                        <button onclick="recarregarListas()" class="btn-primary mt-4 px-4 py-2 rounded-lg">
                            Tentar Novamente
                        </button>
                    </div>
                `;
                
                feather.replace();
                showToast('Erro ao atualizar listas', 'error');
            });
        }

        // Função para adicionar evento de primeira lista
        function adicionarEventoPrimeiraLista() {
            const primeiraListaBtn = document.getElementById('primeiraListaBtn');
            if (primeiraListaBtn) {
                primeiraListaBtn.addEventListener('click', () => {
                    const novaListaModal = document.getElementById('criarListaModal');
                    novaListaModal.classList.remove('hidden');
                });
            }
        }

        // Função para adicionar evento de exclusão de lista
        function adicionarEventoExclusaoLista(elementoLista) {
            const botaoExcluir = elementoLista.querySelector('.excluir-lista');
            if (!botaoExcluir) return; // Garantir que o botão existe
            
            // Remover eventos antigos para evitar duplicação
            botaoExcluir.replaceWith(botaoExcluir.cloneNode(true));
            const novoBotao = elementoLista.querySelector('.excluir-lista');
            
            novoBotao.addEventListener('click', function() {
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
        }

        // Modal Nova Lista
        const novaListaBtn = document.getElementById('novaListaBtn');
        const novaListaModal = document.getElementById('criarListaModal');
        const cancelarModal = document.getElementById('cancelarModal');
        const novaListaForm = document.getElementById('novaListaForm');

        // Abrir modal
        function toggleModal() {
            novaListaModal.classList.toggle('hidden');
        }

        novaListaBtn.addEventListener('click', toggleModal);

        // Fechar modal
        cancelarModal.addEventListener('click', toggleModal);

        // Criar lista
        novaListaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nome = document.getElementById('nomeLista').value.trim();

            console.log('Criando lista:', nome);  // Log de diagnóstico

            fetch('lista_crud_ajax.php?action=criar_lista', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    nome: nome
                })
            })
            .then(response => {
                console.log('Resposta do servidor:', response);  // Log de diagnóstico
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data);  // Log de diagnóstico
                
                if (data.success) {
                    // Fechar modal
                    toggleModal();

                    // Limpar campo
                    document.getElementById('nomeLista').value = '';

                    // Mostrar notificação
                    showToast('Lista criada com sucesso!');

                    // Recarregar listas
                    console.log('Chamando recarregarListas após criação');  // Log de diagnóstico
                    recarregarListas();
                } else {
                    // Mostrar erro
                    console.error('Erro na criação:', data.message);  // Log de erro
                    showToast(data.message || 'Erro ao criar lista', 'error');
                }
            })
            .catch(error => {
                console.error('Erro completo:', error);  // Log de erro completo
                showToast('Erro ao criar lista', 'error');
            });
        });

        // Adicionar eventos de exclusão para listas existentes
        document.querySelectorAll('.excluir-lista').forEach(botao => {
            const elementoLista = botao.closest('.bg-white');
            adicionarEventoExclusaoLista(elementoLista);
        });

        // Inicializar ícones Feather
        feather.replace();
        
        // Chamar recarregamento de listas ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Carregamento inicial da página');  // Log de diagnóstico
            recarregarListas();
        });
    </script>
</body>
</html>

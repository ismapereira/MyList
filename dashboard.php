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
    <title>Dashboard - MyList</title>
    <link href="assets/css/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-6 space-y-6 hidden md:block">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center text-xl font-bold">
                    <?php echo strtoupper(substr($usuario->nome, 0, 1)); ?>
                </div>
                <div>
                    <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($usuario->nome); ?></h2>
                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($usuario->email); ?></p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="#" class="flex items-center space-x-3 text-gray-700 p-2 hover:bg-blue-50 rounded-lg transition">
                    <i data-feather="home" class="w-5 h-5"></i>
                    <span>Início</span>
                </a>
                <a href="#" class="flex items-center space-x-3 text-gray-700 p-2 hover:bg-blue-50 rounded-lg transition">
                    <i data-feather="list" class="w-5 h-5"></i>
                    <span>Minhas Listas</span>
                </a>
                <a href="#" class="flex items-center space-x-3 text-gray-700 p-2 hover:bg-blue-50 rounded-lg transition">
                    <i data-feather="settings" class="w-5 h-5"></i>
                    <span>Configurações</span>
                </a>
                <a href="logout.php" class="flex items-center space-x-3 text-red-600 p-2 hover:bg-red-50 rounded-lg transition">
                    <i data-feather="log-out" class="w-5 h-5"></i>
                    <span>Sair</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 bg-gray-50">
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
                            <p class="text-2xl font-bold text-blue-600"><?php echo count($listas); ?></p>
                        </div>
                        <i data-feather="clipboard" class="w-8 h-8 text-blue-500"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-500 text-sm">Itens Pendentes</h3>
                            <p class="text-2xl font-bold text-yellow-600">
                                <?php 
                                $totalPendentes = 0;
                                foreach($listas as $lista) {
                                    $totalPendentes += $lista['itens_pendentes'];
                                }
                                echo $totalPendentes;
                                ?>
                            </p>
                        </div>
                        <i data-feather="shopping-cart" class="w-8 h-8 text-yellow-500"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-500 text-sm">Listas Concluídas</h3>
                            <p class="text-2xl font-bold text-green-600">
                                <?php 
                                $totalConcluidas = 0;
                                foreach($listas as $lista) {
                                    if($lista['status'] == 'concluida') $totalConcluidas++;
                                }
                                echo $totalConcluidas;
                                ?>
                            </p>
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

                <div class="grid gap-4">
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
                            <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center hover:shadow-lg transition">
                                <div>
                                    <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($lista['nome']); ?></h3>
                                    <p class="text-sm text-gray-500">
                                        <?php echo $lista['itens_pendentes']; ?> itens pendentes
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="lista.php?id=<?php echo $lista['id']; ?>" class="text-blue-600 hover:text-blue-800">
                                        <i data-feather="edit" class="w-5 h-5"></i>
                                    </a>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i data-feather="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Nova Lista -->
    <div id="novaListaModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-xl w-96 max-w-full">
            <h2 class="text-2xl font-bold mb-4">Criar Nova Lista</h2>
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
                    <button type="button" id="fecharModal" class="btn-secondary px-4 py-2 rounded-lg">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary px-4 py-2 rounded-lg">
                        Criar Lista
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Inicializar ícones Feather
        feather.replace();

        // Modal Nova Lista
        const novaListaBtn = document.getElementById('novaListaBtn');
        const primeiraListaBtn = document.getElementById('primeiraListaBtn');
        const novaListaModal = document.getElementById('novaListaModal');
        const fecharModal = document.getElementById('fecharModal');
        const novaListaForm = document.getElementById('novaListaForm');

        function toggleModal() {
            novaListaModal.classList.toggle('hidden');
            novaListaModal.classList.toggle('flex');
        }

        novaListaBtn?.addEventListener('click', toggleModal);
        primeiraListaBtn?.addEventListener('click', toggleModal);
        fecharModal.addEventListener('click', toggleModal);

        novaListaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const nomeLista = document.getElementById('nomeLista').value.trim();
            
            if (nomeLista) {
                // Aqui você pode adicionar a lógica AJAX para criar a lista
                alert('Funcionalidade de criação de lista em desenvolvimento');
                toggleModal();
            }
        });
    </script>
</body>
</html>

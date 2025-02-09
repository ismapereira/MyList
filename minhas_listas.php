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

            <!-- Filtros e Ordenação -->
            <section class="mb-6">
                <div class="bg-white p-4 rounded-xl shadow-md">
                    <div class="flex space-x-4">
                        <select class="form-select flex-1">
                            <option>Filtrar por Status</option>
                            <option>Pendentes</option>
                            <option>Concluídas</option>
                        </select>
                        <select class="form-select flex-1">
                            <option>Ordenar por</option>
                            <option>Data de Criação</option>
                            <option>Alfabético</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Lista de Listas -->
            <section id="listasContainer" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($listas)): ?>
                    <div class="col-span-full text-center bg-white p-6 rounded-xl shadow-md">
                        <i data-feather="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                        <h2 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma lista encontrada</h2>
                        <p class="text-gray-500">Crie sua primeira lista para começar!</p>
                        <button id="primeiraListaBtn" class="btn-primary mt-4">
                            Criar Primeira Lista
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach($listas as $lista): ?>
                        <div class="bg-white p-6 rounded-xl shadow-md">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($lista['nome']); ?></h3>
                                <div class="flex space-x-2">
                                    <button class="text-blue-500 hover:text-blue-600">
                                        <i data-feather="edit-2" class="w-5 h-5"></i>
                                    </button>
                                    <button class="text-red-500 hover:text-red-600 excluir-lista" data-lista-id="<?php echo $lista['id']; ?>">
                                        <i data-feather="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($lista['descricao'] ?? 'Sem descrição'); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">
                                    Criada em <?php echo date('d/m/Y', strtotime($lista['data_criacao'])); ?>
                                </span>
                                <span class="text-sm font-semibold <?php echo $lista['itens_pendentes'] > 0 ? 'text-yellow-600' : 'text-green-600'; ?>">
                                    <?php echo $lista['itens_pendentes'] > 0 ? $lista['itens_pendentes'] . ' pendentes' : 'Concluída'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <!-- Modal Criar Lista -->
    <?php include 'components/modal_criar_lista.php'; ?>

    <script src="assets/js/dashboard.js"></script>
    <script>
        // Inicializar ícones Feather
        feather.replace();
    </script>
</body>
</html>

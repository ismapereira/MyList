<?php
session_start();
require_once 'config/database.php';
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
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - MyList</title>
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
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Configurações</h1>
                <p class="text-gray-600">Personalize sua experiência no MyList</p>
            </header>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Perfil -->
                <section class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Perfil</h2>
                    <form id="formPerfil">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Nome Completo</label>
                            <input type="text" class="form-input w-full" 
                                   value="<?php echo htmlspecialchars($usuario->nome); ?>" 
                                   placeholder="Seu nome completo">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">E-mail</label>
                            <input type="email" class="form-input w-full" 
                                   value="<?php echo htmlspecialchars($usuario->email); ?>" 
                                   placeholder="Seu e-mail">
                        </div>
                        <button type="submit" class="btn-primary w-full">
                            Atualizar Perfil
                        </button>
                    </form>
                </section>

                <!-- Preferências -->
                <section class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Preferências</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span>Tema Escuro</span>
                            <div class="relative">
                                <input type="checkbox" id="darkMode" class="hidden">
                                <label for="darkMode" class="toggle-switch"></label>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Notificações por E-mail</span>
                            <div class="relative">
                                <input type="checkbox" id="emailNotif" class="hidden">
                                <label for="emailNotif" class="toggle-switch"></label>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Mostrar Listas Concluídas</span>
                            <div class="relative">
                                <input type="checkbox" id="showCompleted" class="hidden">
                                <label for="showCompleted" class="toggle-switch"></label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Segurança -->
                <section class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Segurança</h2>
                    <form id="formSenha">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Senha Atual</label>
                            <input type="password" class="form-input w-full" placeholder="Digite sua senha atual">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Nova Senha</label>
                            <input type="password" class="form-input w-full" placeholder="Digite sua nova senha">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Confirmar Nova Senha</label>
                            <input type="password" class="form-input w-full" placeholder="Confirme sua nova senha">
                        </div>
                        <button type="submit" class="btn-primary w-full">
                            Alterar Senha
                        </button>
                    </form>
                </section>

                <!-- Área de Risco -->
                <section class="bg-white p-6 rounded-xl shadow-md border-2 border-red-200">
                    <h2 class="text-xl font-semibold mb-4 text-red-600">Área de Risco</h2>
                    <div class="space-y-4">
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-red-700 mb-2">Excluir Conta</h3>
                            <p class="text-red-600 text-sm mb-4">Ao excluir sua conta, todos os dados serão permanentemente removidos.</p>
                            <button class="btn-danger w-full">
                                Excluir Minha Conta
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        // Inicializar ícones Feather
        feather.replace();
    </script>
</body>
</html>

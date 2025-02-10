<?php
session_start();
require_once 'config/database.php';
require_once 'models/Usuario.php';

// Verificar autenticação
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Recuperar informações do usuário
$usuario = new Usuario();
$usuario->id = $_SESSION['user_id'];
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
<body class="bg-gray-50 dark:bg-gray-900 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 overflow-x-hidden">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Configurações</h1>
                <p class="text-gray-600 dark:text-gray-400">Personalize sua experiência no MyList</p>
            </header>

            <!-- Toast Container -->
            <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Perfil -->
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="user" class="w-6 h-6 text-blue-500"></i>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Perfil</h2>
                    </div>
                    <form id="formPerfil" class="space-y-4">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Completo</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">
                                    <i data-feather="user" class="w-4 h-4"></i>
                                </span>
                                <input type="text" id="nome" name="nome" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800" 
                                       value="<?php echo htmlspecialchars($usuario->nome); ?>" 
                                       placeholder="Seu nome completo">
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">
                                    <i data-feather="mail" class="w-4 h-4"></i>
                                </span>
                                <input type="email" id="email" name="email" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800" 
                                       value="<?php echo htmlspecialchars($usuario->email); ?>" 
                                       placeholder="Seu e-mail">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full flex items-center justify-center space-x-2">
                            <i data-feather="save" class="w-4 h-4"></i>
                            <span>Atualizar Perfil</span>
                        </button>
                    </form>
                </section>

                <!-- Preferências -->
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="settings" class="w-6 h-6 text-blue-500"></i>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Preferências</h2>
                    </div>
                    <form id="formPreferencias" class="space-y-6">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            <div>
                                <h3 class="font-medium text-gray-800 dark:text-gray-100">Tema Escuro</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ativar modo escuro na interface</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" id="tema_escuro" name="tema_escuro" class="hidden">
                                <label for="tema_escuro" class="toggle-switch"></label>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            <div>
                                <h3 class="font-medium text-gray-800 dark:text-gray-100">Notificações por E-mail</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Receber atualizações por e-mail</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" id="notificacoes_email" name="notificacoes_email" class="hidden">
                                <label for="notificacoes_email" class="toggle-switch"></label>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full flex items-center justify-center space-x-2">
                            <i data-feather="save" class="w-4 h-4"></i>
                            <span>Salvar Preferências</span>
                        </button>
                    </form>
                </section>

                <!-- Segurança -->
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="lock" class="w-6 h-6 text-blue-500"></i>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Segurança</h2>
                    </div>
                    <form id="formSenha" class="space-y-4">
                        <div>
                            <label for="senha_atual" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha Atual</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">
                                    <i data-feather="key" class="w-4 h-4"></i>
                                </span>
                                <input type="password" id="senha_atual" name="senha_atual" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800" 
                                       placeholder="Digite sua senha atual">
                            </div>
                        </div>
                        <div>
                            <label for="nova_senha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nova Senha</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">
                                    <i data-feather="lock" class="w-4 h-4"></i>
                                </span>
                                <input type="password" id="nova_senha" name="nova_senha" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800" 
                                       placeholder="Nova senha">
                            </div>
                        </div>
                        <div>
                            <label for="confirmar_senha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Nova Senha</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                </span>
                                <input type="password" id="confirmar_senha" name="confirmar_senha" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800" 
                                       placeholder="Confirme a nova senha">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full flex items-center justify-center space-x-2">
                            <i data-feather="save" class="w-4 h-4"></i>
                            <span>Alterar Senha</span>
                        </button>
                    </form>
                </section>

                <!-- Dados da Conta -->
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="info" class="w-6 h-6 text-blue-500"></i>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Dados da Conta</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Data de Criação</label>
                            <p class="text-gray-800 dark:text-gray-100" id="data_criacao">
                                <?php echo date('d/m/Y H:i', strtotime($usuario->data_criacao)); ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total de Listas</label>
                            <p class="text-gray-800 dark:text-gray-100" id="total_listas">
                                <?php echo $usuario->getTotalListas(); ?> listas criadas
                            </p>
                        </div>
                    </div>
                </section>
                <!-- Exclusão de Conta -->
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border-2 border-red-100 dark:border-red-900">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="trash-2" class="w-6 h-6 text-red-500"></i>
                        <h2 class="text-xl font-semibold text-red-600 dark:text-red-400">Excluir Conta</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                            <p class="text-red-600 dark:text-red-400 font-medium">Atenção: Esta ação é irreversível!</p>
                            <p class="text-red-500 dark:text-red-400 text-sm mt-1">
                                Ao excluir sua conta, todos os seus dados e listas serão permanentemente removidos.
                            </p>
                        </div>
                        <form id="formExcluirConta" class="space-y-4">
                            <div>
                                <label for="senha_exclusao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Confirme sua senha para excluir a conta
                                </label>
                                <input type="password" id="senha_exclusao" name="senha_exclusao" 
                                    class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-red-500 focus:ring focus:ring-red-200 dark:focus:ring-red-800" 
                                    placeholder="Digite sua senha">
                            </div>
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                <span>Confirmar Exclusão de Conta</span>
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="assets/js/dark-mode.js"></script>
    <script>
        // Inicializar ícones Feather
        feather.replace();

        // Função para mostrar toast
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type === 'success' ? 'toast-success' : 'toast-error'} mb-4`;
            toast.textContent = message;
            toastContainer.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Atualizar perfil
        document.getElementById('formPerfil').addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const response = await fetch('usuario_crud_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'action': 'atualizar_perfil',
                        'nome': document.getElementById('nome').value,
                        'email': document.getElementById('email').value
                    })
                });

                const data = await response.json();
                if (response.ok) {
                    showToast('Perfil atualizado com sucesso!');
                } else {
                    showToast(data.error || 'Erro ao atualizar perfil', 'error');
                }
            } catch (error) {
                showToast('Erro ao atualizar perfil', 'error');
            }
        });

        // Atualizar senha
        document.getElementById('formSenha').addEventListener('submit', async (e) => {
            e.preventDefault();
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;

            if (novaSenha !== confirmarSenha) {
                showToast('As senhas não coincidem', 'error');
                return;
            }

            try {
                const response = await fetch('usuario_crud_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'action': 'alterar_senha',
                        'senha_atual': document.getElementById('senha_atual').value,
                        'nova_senha': novaSenha,
                        'confirmar_senha': confirmarSenha
                    })
                });

                const data = await response.json();
                if (response.ok) {
                    showToast('Senha alterada com sucesso!');
                    document.getElementById('formSenha').reset();
                } else {
                    showToast(data.error || 'Erro ao alterar senha', 'error');
                }
            } catch (error) {
                showToast('Erro ao alterar senha', 'error');
            }
        });

        // Excluir conta
        document.getElementById('formExcluirConta').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) {
                return;
            }

            try {
                const response = await fetch('usuario_crud_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'action': 'excluir_conta',
                        'senha': document.getElementById('senha_exclusao').value
                    })
                });

                const data = await response.json();
                if (response.ok) {
                    showToast('Conta excluída com sucesso!');
                    setTimeout(() => {
                        window.location.href = 'logout.php';
                    }, 2000);
                } else {
                    showToast(data.error || 'Erro ao excluir conta', 'error');
                }
            } catch (error) {
                showToast('Erro ao excluir conta', 'error');
            }
        });
    </script>
</body>
</html>

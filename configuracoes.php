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
        <main class="flex-1 p-6 md:p-10 overflow-x-hidden">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Configurações</h1>
                <p class="text-gray-600">Personalize sua experiência no MyList</p>
            </header>

            <!-- Toast Container -->
            <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Perfil -->
                <section class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="user" class="w-6 h-6 text-blue-500"></i>
                        <h2 class="text-xl font-semibold">Perfil</h2>
                    </div>
                    <form id="formPerfil" class="space-y-4">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                    <i data-feather="user" class="w-4 h-4"></i>
                                </span>
                                <input type="text" id="nome" name="nome" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" 
                                       value="<?php echo htmlspecialchars($usuario->nome); ?>" 
                                       placeholder="Seu nome completo">
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                    <i data-feather="mail" class="w-4 h-4"></i>
                                </span>
                                <input type="email" id="email" name="email" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" 
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
                <section class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="settings" class="w-6 h-6 text-blue-500"></i>
                        <h2 class="text-xl font-semibold">Preferências</h2>
                    </div>
                    <form id="formPreferencias" class="space-y-6">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <h3 class="font-medium">Tema Escuro</h3>
                                <p class="text-sm text-gray-500">Ativar modo escuro na interface</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" id="tema_escuro" name="tema_escuro" class="hidden">
                                <label for="tema_escuro" class="toggle-switch"></label>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <h3 class="font-medium">Notificações por E-mail</h3>
                                <p class="text-sm text-gray-500">Receber atualizações por e-mail</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" id="notificacoes_email" name="notificacoes_email" class="hidden">
                                <label for="notificacoes_email" class="toggle-switch"></label>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <h3 class="font-medium">Mostrar Listas Concluídas</h3>
                                <p class="text-sm text-gray-500">Exibir listas já finalizadas</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" id="mostrar_concluidas" name="mostrar_concluidas" class="hidden">
                                <label for="mostrar_concluidas" class="toggle-switch"></label>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full flex items-center justify-center space-x-2">
                            <i data-feather="save" class="w-4 h-4"></i>
                            <span>Salvar Preferências</span>
                        </button>
                    </form>
                </section>

                <!-- Segurança -->
                <section class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="lock" class="w-6 h-6 text-blue-500"></i>
                        <h2 class="text-xl font-semibold">Segurança</h2>
                    </div>
                    <form id="formSenha" class="space-y-4">
                        <div>
                            <label for="senha_atual" class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                    <i data-feather="key" class="w-4 h-4"></i>
                                </span>
                                <input type="password" id="senha_atual" name="senha_atual" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" 
                                       placeholder="Digite sua senha atual">
                            </div>
                        </div>
                        <div>
                            <label for="nova_senha" class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                    <i data-feather="lock" class="w-4 h-4"></i>
                                </span>
                                <input type="password" id="nova_senha" name="nova_senha" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" 
                                       placeholder="Digite a nova senha">
                            </div>
                        </div>
                        <div>
                            <label for="confirmar_senha" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                </span>
                                <input type="password" id="confirmar_senha" name="confirmar_senha" 
                                       class="form-input pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200" 
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
                <section class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex items-center space-x-3 mb-6">
                        <i data-feather="info" class="w-6 h-6 text-blue-500"></i>
                        <h2 class="text-xl font-semibold">Dados da Conta</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Data de Criação</label>
                            <p class="text-gray-800" id="data_criacao">
                                <?php echo date('d/m/Y H:i', strtotime($usuario->data_criacao)); ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total de Listas</label>
                            <p class="text-gray-800" id="total_listas">
                                <?php echo $usuario->getTotalListas(); ?> listas criadas
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        // Inicializar ícones Feather
        feather.replace();

        // Função para mostrar toast
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type} animate-fade-in-down`;
            toast.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="w-5 h-5"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.getElementById('toast-container').appendChild(toast);
            feather.replace();
            
            setTimeout(() => {
                toast.classList.add('animate-fade-out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Função para fazer requisição AJAX
        async function makeRequest(url, method, data = null) {
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: data ? JSON.stringify(data) : null
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Erro ao processar requisição');
                }

                return result;
            } catch (error) {
                throw error;
            }
        }

        // Carregar dados do usuário
        async function carregarDadosUsuario() {
            try {
                const result = await makeRequest('usuario_crud_ajax.php?action=dados_usuario', 'GET');
                if (result.success && result.data) {
                    const { preferencias } = result.data;
                    document.getElementById('tema_escuro').checked = preferencias?.tema_escuro || false;
                    document.getElementById('notificacoes_email').checked = preferencias?.notificacoes_email || false;
                    document.getElementById('mostrar_concluidas').checked = preferencias?.mostrar_concluidas || false;
                }
            } catch (error) {
                showToast(error.message, 'error');
            }
        }

        // Atualizar perfil
        document.getElementById('formPerfil').addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const data = {
                    nome: document.getElementById('nome').value,
                    email: document.getElementById('email').value
                };

                const result = await makeRequest('usuario_crud_ajax.php?action=atualizar_perfil', 'POST', data);
                showToast(result.message);
            } catch (error) {
                showToast(error.message, 'error');
            }
        });

        // Atualizar preferências
        document.getElementById('formPreferencias').addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const data = {
                    tema_escuro: document.getElementById('tema_escuro').checked,
                    notificacoes_email: document.getElementById('notificacoes_email').checked,
                    mostrar_concluidas: document.getElementById('mostrar_concluidas').checked
                };

                const result = await makeRequest('usuario_crud_ajax.php?action=atualizar_preferencias', 'POST', data);
                showToast(result.message);
            } catch (error) {
                showToast(error.message, 'error');
            }
        });

        // Atualizar senha
        document.getElementById('formSenha').addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const novaSenha = document.getElementById('nova_senha').value;
                const confirmarSenha = document.getElementById('confirmar_senha').value;

                if (novaSenha !== confirmarSenha) {
                    throw new Error('As senhas não coincidem');
                }

                const data = {
                    senha_atual: document.getElementById('senha_atual').value,
                    nova_senha: novaSenha,
                    confirmar_senha: confirmarSenha
                };

                const result = await makeRequest('usuario_crud_ajax.php?action=atualizar_senha', 'POST', data);
                showToast(result.message);
                document.getElementById('formSenha').reset();
            } catch (error) {
                showToast(error.message, 'error');
            }
        });

        // Carregar dados iniciais
        carregarDadosUsuario();
    </script>
</body>
</html>

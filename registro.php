<?php
session_start();
if(isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}
require_once 'models/Usuario.php';

$erro = '';
$sucesso = '';

// Processamento do formulário de registro
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuario();
    $usuario->nome = $_POST['nome'];
    $usuario->email = $_POST['email'];
    $usuario->senha = $_POST['senha'];

    // Validações básicas
    if(empty($usuario->nome) || empty($usuario->email) || empty($usuario->senha)) {
        $erro = "campos_invalidos";
    } elseif(strlen($usuario->senha) < 6) {
        $erro = "senha_curta";
    } elseif($usuario->criar()) {
        $sucesso = "Cadastro realizado com sucesso! Faça login para continuar.";
    } else {
        $erro = "Erro ao cadastrar usuário. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - MyList</title>
    <link href="assets/css/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="bg-gray-50 antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-md w-full bg-white shadow-lg rounded-xl p-8 space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-blue-800 mb-4">MyList</h1>
                <p class="text-gray-600">Crie sua conta gratuitamente</p>
            </div>
            
            <form id="registroForm" action="" method="POST" class="space-y-6">
                <?php if($erro): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <?php 
                        switch($erro) {
                            case 'email_existente':
                                echo 'E-mail já cadastrado.';
                                break;
                            case 'campos_invalidos':
                                echo 'Preencha todos os campos corretamente.';
                                break;
                            case 'senha_curta':
                                echo 'Senha deve ter no mínimo 6 caracteres.';
                                break;
                            default:
                                echo 'Ocorreu um erro no cadastro.';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if($sucesso): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <?php echo $sucesso; ?>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="nome" class="block text-gray-700 mb-2">Nome Completo</label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        required 
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border focus:border-blue-500 focus:bg-white focus:outline-none transition duration-300"
                        placeholder="Seu nome completo"
                    >
                </div>

                <div>
                    <label for="email" class="block text-gray-700 mb-2">E-mail</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border focus:border-blue-500 focus:bg-white focus:outline-none transition duration-300"
                        placeholder="Seu e-mail"
                    >
                </div>

                <div>
                    <label for="senha" class="block text-gray-700 mb-2">Senha</label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        required 
                        minlength="6"
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border focus:border-blue-500 focus:bg-white focus:outline-none transition duration-300"
                        placeholder="Crie uma senha"
                    >
                    <p class="text-xs text-gray-500 mt-1">Mínimo de 6 caracteres</p>
                </div>

                <div>
                    <label for="confirmar_senha" class="block text-gray-700 mb-2">Confirmar Senha</label>
                    <input 
                        type="password" 
                        id="confirmar_senha" 
                        name="confirmar_senha" 
                        required 
                        minlength="6"
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border focus:border-blue-500 focus:bg-white focus:outline-none transition duration-300"
                        placeholder="Repita a senha"
                    >
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full btn-primary text-white py-3 rounded-lg hover:opacity-90 transition duration-300"
                    >
                        Criar Conta
                    </button>
                </div>
            </form>

            <div class="text-center">
                <p class="text-gray-600">
                    Já tem uma conta? 
                    <a href="login.php" class="text-blue-600 hover:text-blue-800">
                        Faça login
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registroForm').addEventListener('submit', function(e) {
            const nome = document.getElementById('nome');
            const email = document.getElementById('email');
            const senha = document.getElementById('senha');
            const confirmarSenha = document.getElementById('confirmar_senha');

            if (!nome.value.trim() || !email.value.trim() || !senha.value.trim() || !confirmarSenha.value.trim()) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos.');
                return;
            }

            if (senha.value !== confirmarSenha.value) {
                e.preventDefault();
                alert('As senhas não coincidem.');
                return;
            }

            if (senha.value.length < 6) {
                e.preventDefault();
                alert('A senha deve ter no mínimo 6 caracteres.');
                return;
            }
        });
    </script>
</body>
</html>

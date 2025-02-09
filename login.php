<?php
session_start();
require_once 'models/Usuario.php';

// Verificar se o usuário já está logado
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$erro = '';

// Processamento do formulário de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuario();
    $usuario->email = $_POST['email'];
    $usuario->senha = $_POST['senha'];

    if ($usuario->autenticar()) {
        // Login bem-sucedido
        $_SESSION['user_id'] = $usuario->id;
        $_SESSION['user_nome'] = $usuario->nome;
        
        error_log("Login bem-sucedido - User ID: " . $usuario->id);
        
        header("Location: dashboard.php");
        exit();
    } else {
        $erro = "Email ou senha inválidos";
        error_log("Falha no login - Email: " . $usuario->email);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyList</title>
    <link href="assets/css/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="bg-gray-50 antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="max-w-md w-full bg-white shadow-lg rounded-xl p-8 space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-blue-800 mb-4">MyList</h1>
                <p class="text-gray-600">Faça login para continuar</p>
            </div>
            
            <form id="loginForm" action="" method="POST" class="space-y-6">
                <?php if($erro): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

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
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border focus:border-blue-500 focus:bg-white focus:outline-none transition duration-300"
                        placeholder="Sua senha"
                    >
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="lembrar" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="lembrar" class="ml-2 block text-gray-900">
                            Lembrar-me
                        </label>
                    </div>
                    <div>
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                            Esqueceu a senha?
                        </a>
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full btn-primary text-white py-3 rounded-lg hover:opacity-90 transition duration-300"
                    >
                        Entrar
                    </button>
                </div>
            </form>

            <div class="text-center">
                <p class="text-gray-600">
                    Não tem uma conta? 
                    <a href="registro.php" class="text-blue-600 hover:text-blue-800">
                        Cadastre-se
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const senha = document.getElementById('senha');

            if (!email.value.trim() || !senha.value.trim()) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos.');
            }
        });
    </script>
</body>
</html>

<?php
session_start();
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
        $erro = "Todos os campos são obrigatórios";
    } elseif(strlen($usuario->senha) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres";
    } elseif($usuario->criar()) {
        $sucesso = "Cadastro realizado com sucesso! Faça login para continuar.";
    } else {
        $erro = "Erro ao cadastrar usuário. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro - Lista de Mercado</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/registro.css">
</head>
<body>
    <div class="container">
        <div class="registro-container">
            <h2>Criar Conta</h2>
            <?php if($erro): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            <?php if($sucesso): ?>
                <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                <div class="text-center mt-3">
                    <a href="login.php">Já tem uma conta? Faça login</a>
                </div>
            </form>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

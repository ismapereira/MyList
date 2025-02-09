<?php
session_start();

// Verificar se o usuário está logado
if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'models/Lista.php';

$lista_model = new Lista();
$listas = $lista_model->buscarListasUsuario($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Listas de Mercado</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Lista de Mercado</a>
            <div class="navbar-nav ml-auto">
                <span class="navbar-text mr-3">
                    Olá, <?php echo $_SESSION['usuario_nome']; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Criar Nova Lista
                    </div>
                    <div class="card-body">
                        <form id="nova-lista-form">
                            <div class="form-group">
                                <label for="nome-lista">Nome da Lista</label>
                                <input type="text" class="form-control" id="nome-lista" required>
                            </div>
                            <div class="form-group">
                                <label for="descricao-lista">Descrição (opcional)</label>
                                <textarea class="form-control" id="descricao-lista"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Criar Lista</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <h3>Minhas Listas</h3>
                <div class="lista-listas">
                    <?php foreach($listas as $lista): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($lista['nome']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($lista['descricao']); ?></p>
                                <a href="lista.php?id=<?php echo $lista['id']; ?>" class="btn btn-sm btn-outline-primary">Ver Lista</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>

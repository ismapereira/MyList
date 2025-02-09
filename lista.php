<?php
session_start();

// Verificar se o usuário está logado
if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'models/Lista.php';

// Verificar se um ID de lista foi passado
if(!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$lista_id = intval($_GET['id']);
$lista_model = new Lista();
$lista_model->id = $lista_id;

// Buscar itens da lista
$itens = $lista_model->buscarItens();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Compras</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/lista.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">MyList</a>
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
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Lista de Compras</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarItemModal">
                            Adicionar Item
                        </button>
                    </div>
                    <div class="card-body">
                        <ul class="list-group" id="lista-itens">
                            <?php foreach($itens as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               data-item-id="<?php echo $item['id']; ?>"
                                               <?php echo $item['comprado'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label <?php echo $item['comprado'] ? 'text-muted' : ''; ?>">
                                            <?php echo htmlspecialchars($item['nome']); ?> 
                                            (<?php echo $item['quantidade'] . ' ' . $item['unidade']; ?>)
                                        </label>
                                    </div>
                                    <button class="btn btn-sm btn-danger remover-item" data-item-id="<?php echo $item['id']; ?>">
                                        Remover
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar Item -->
    <div class="modal fade" id="adicionarItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-adicionar-item">
                        <input type="hidden" name="lista_id" value="<?php echo $lista_id; ?>">
                        <div class="mb-3">
                            <label for="nome-item" class="form-label">Nome do Item</label>
                            <input type="text" class="form-control" id="nome-item" name="nome" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantidade-item" class="form-label">Quantidade</label>
                                <input type="number" class="form-control" id="quantidade-item" name="quantidade" value="1" min="0.1" step="0.1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unidade-item" class="form-label">Unidade</label>
                                <select class="form-select" id="unidade-item" name="unidade">
                                    <option value="un">Unidade</option>
                                    <option value="kg">Kg</option>
                                    <option value="g">g</option>
                                    <option value="ml">ml</option>
                                    <option value="L">L</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/lista.js"></script>
</body>
</html>

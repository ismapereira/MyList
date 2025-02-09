<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado
if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado']);
    exit();
}

require_once '../config/database.php';

// Validar dados recebidos
if(!isset($_POST['item_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
    exit();
}

$database = new Database();
$conexao = $database->conectar();

if(!$conexao) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados']);
    exit();
}

try {
    $query = "DELETE FROM itens_lista WHERE id = :item_id";
    $stmt = $conexao->prepare($query);
    $stmt->bindParam(':item_id', $_POST['item_id'], PDO::PARAM_INT);
    
    $resultado = $stmt->execute();

    if($resultado) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao remover item']);
    }
} catch(PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}
?>

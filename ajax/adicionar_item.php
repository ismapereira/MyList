<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado
if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado']);
    exit();
}

require_once '../models/Lista.php';

// Validar dados recebidos
if(!isset($_POST['lista_id']) || !isset($_POST['nome']) || 
   !isset($_POST['quantidade']) || !isset($_POST['unidade'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
    exit();
}

$lista_model = new Lista();
$lista_model->id = intval($_POST['lista_id']);

// Adicionar item
$resultado = $lista_model->adicionarItem(
    $_POST['nome'], 
    floatval($_POST['quantidade']), 
    $_POST['unidade']
);

if($resultado) {
    $item_id = $lista_model->getUltimoItemId(); // Novo método para obter o último ID
    echo json_encode(['sucesso' => true, 'item_id' => $item_id]);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao adicionar item']);
}
?>

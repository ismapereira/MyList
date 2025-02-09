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
if(!isset($_POST['item_id']) || !isset($_POST['comprado'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
    exit();
}

$lista_model = new Lista();

// Marcar item como comprado/não comprado
$resultado = $lista_model->marcarItemComprado(
    intval($_POST['item_id']), 
    intval($_POST['comprado']) === 1
);

if($resultado) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao marcar item']);
}
?>

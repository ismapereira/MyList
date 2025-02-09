<?php
session_start();
header('Content-Type: application/json');

require_once 'config/database.php';
require_once 'models/Lista.php';

// Verificar autenticação
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

// Verificar método da requisição
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

try {
    $lista = new Lista();
    $lista->usuario_id = $_SESSION['usuario_id'];

    switch ($method) {
        case 'POST':
            // Adicionar item
            if ($action === 'adicionar_item') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['lista_id'], $data['nome'], $data['quantidade'], $data['unidade'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Dados incompletos']);
                    exit();
                }

                $lista->id = $data['lista_id'];
                $item_id = $lista->adicionarItemLista(
                    $data['nome'], 
                    $data['quantidade'], 
                    $data['unidade']
                );

                if ($item_id) {
                    echo json_encode([
                        'success' => true, 
                        'item_id' => $item_id,
                        'message' => 'Item adicionado com sucesso'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Falha ao adicionar item']);
                }
                exit();
            }
            break;

        case 'PUT':
            // Atualizar status do item
            if ($action === 'atualizar_status') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['lista_id'], $data['item_id'], $data['status'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Dados incompletos']);
                    exit();
                }

                $lista->id = $data['lista_id'];
                $resultado = $lista->atualizarStatusItem(
                    $data['item_id'], 
                    $data['status']
                );

                if ($resultado) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Status atualizado com sucesso'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Falha ao atualizar status']);
                }
                exit();
            }
            break;

        case 'DELETE':
            // Remover item
            if ($action === 'remover_item') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['lista_id'], $data['item_id'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Dados incompletos']);
                    exit();
                }

                $lista->id = $data['lista_id'];
                $resultado = $lista->removerItem($data['item_id']);

                if ($resultado) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Item removido com sucesso'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Falha ao remover item']);
                }
                exit();
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            exit();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro interno do servidor', 
        'details' => $e->getMessage()
    ]);
}

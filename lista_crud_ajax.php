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
            // Criar nova lista
            if ($action === 'criar_lista') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['nome'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Nome da lista é obrigatório']);
                    exit();
                }

                $lista_id = $lista->criarLista(
                    $data['nome'], 
                    $data['descricao'] ?? null
                );

                if ($lista_id) {
                    echo json_encode([
                        'success' => true, 
                        'lista_id' => $lista_id,
                        'message' => 'Lista criada com sucesso'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Falha ao criar lista']);
                }
                exit();
            }
            break;

        case 'PUT':
            // Atualizar lista
            if ($action === 'atualizar_lista') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['lista_id'], $data['nome'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Dados incompletos']);
                    exit();
                }

                $resultado = $lista->atualizarLista(
                    $data['lista_id'], 
                    $data['nome'], 
                    $data['descricao'] ?? null
                );

                if ($resultado) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Lista atualizada com sucesso'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Falha ao atualizar lista']);
                }
                exit();
            }
            break;

        case 'GET':
            // Listar listas do usuário
            if ($action === 'listar_listas') {
                try {
                    // Verificar se é uma requisição AJAX
                    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                        throw new Exception('Requisição inválida');
                    }

                    $listas = $lista->buscarListasUsuario($_SESSION['usuario_id']);

                    // Adicionar informações extras
                    $listasComDetalhes = array_map(function($listaItem) use ($lista) {
                        // Contar itens pendentes
                        $queryPendentes = "SELECT COUNT(*) as pendentes FROM itens_lista 
                                         WHERE lista_id = :lista_id AND status = 'pendente'";
                        $conexao = $lista->getConexao(); // Método para obter conexão
                        $stmtPendentes = $conexao->prepare($queryPendentes);
                        $stmtPendentes->bindParam(':lista_id', $listaItem['id']);
                        $stmtPendentes->execute();
                        $pendentes = $stmtPendentes->fetch(PDO::FETCH_ASSOC)['pendentes'];

                        $listaItem['itens_pendentes'] = (int)$pendentes;
                        return $listaItem;
                    }, $listas);

                    echo json_encode([
                        'success' => true,
                        'listas' => $listasComDetalhes
                    ]);
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode([
                        'error' => 'Erro ao buscar listas', 
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            break;

        case 'DELETE':
            // Excluir lista
            if ($action === 'excluir_lista') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['lista_id'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID da lista é obrigatório']);
                    exit();
                }

                $resultado = $lista->excluirLista($data['lista_id']);

                if ($resultado) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Lista excluída com sucesso'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Falha ao excluir lista']);
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

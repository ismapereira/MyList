<?php
// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Log detalhado da sessão
error_log("[MyList Debug] Sessão iniciada");
error_log("[MyList Debug] Conteúdo da sessão: " . print_r($_SESSION, true));
error_log("[MyList Debug] ID da sessão: " . session_id());
error_log("[MyList Debug] Variáveis da sessão: " . print_r(get_defined_vars(), true));

header('Content-Type: application/json');

require_once 'config/database.php';
require_once 'models/Lista.php';

// Função de log de depuração
function debugLog($message) {
    error_log("[MyList Debug] " . $message);
}

// Verificar autenticação
if (!isset($_SESSION['usuario_id'])) {
    debugLog("Usuário não autenticado");
    debugLog("Conteúdo da sessão: " . print_r($_SESSION, true));
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

// Verificar método da requisição
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

debugLog("Método: $method, Ação: $action");

try {
    $lista = new Lista();
    $lista->usuario_id = $_SESSION['usuario_id'];
    debugLog("ID do usuário: " . $_SESSION['usuario_id']);

    switch ($method) {
        case 'GET':
            // Listar listas do usuário
            if ($action === 'listar_listas') {
                $listas = $lista->listarListasUsuario();
                debugLog("Listas encontradas: " . count($listas));
                echo json_encode([
                    'success' => true,
                    'listas' => $listas
                ]);
                exit();
            }
            // Obter lista específica
            elseif ($action === 'obter_lista') {
                $lista_id = $_GET['lista_id'] ?? null;
                debugLog("Obtendo lista com ID: $lista_id");
                
                if (!$lista_id) {
                    debugLog("ID da lista não fornecido");
                    http_response_code(400);
                    echo json_encode(['error' => 'ID da lista não fornecido']);
                    exit();
                }

                try {
                    $lista->id = $lista_id;
                    $lista->usuario_id = $_SESSION['usuario_id'];
                    $dados = $lista->obterPorId();
                    $itens = $lista->buscarItensLista();
                    
                    if ($dados) {
                        debugLog("Lista encontrada: " . json_encode($dados));
                        echo json_encode([
                            'success' => true,
                            'lista' => $dados,
                            'itens' => $itens
                        ]);
                    } else {
                        debugLog("Nenhuma lista encontrada com ID $lista_id");
                        http_response_code(404);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Lista não encontrada',
                            'details' => "Nenhuma lista encontrada com ID $lista_id para o usuário atual"
                        ]);
                    }
                } catch (Exception $e) {
                    debugLog("Erro ao buscar lista: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao buscar lista',
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            // Buscar itens de uma lista
            elseif ($action === 'buscar_itens') {
                $lista_id = $_GET['lista_id'] ?? null;
                debugLog("Buscando itens da lista com ID: $lista_id");
                
                if (!$lista_id) {
                    debugLog("ID da lista não fornecido");
                    http_response_code(400);
                    echo json_encode(['error' => 'ID da lista não fornecido']);
                    exit();
                }

                try {
                    $lista->id = $lista_id;
                    $lista->usuario_id = $_SESSION['usuario_id'];
                    $itens = $lista->buscarItensLista();
                    
                    debugLog("Itens encontrados: " . count($itens));
                    echo json_encode([
                        'success' => true,
                        'itens' => $itens
                    ]);
                } catch (Exception $e) {
                    debugLog("Erro ao buscar itens da lista: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao buscar itens da lista',
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            break;

        case 'POST':
            // Criar nova lista
            if ($action === 'criar_lista') {
                $dados = json_decode(file_get_contents('php://input'), true);
                debugLog("Dados recebidos para criar lista: " . print_r($dados, true));

                if (!isset($dados['nome'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Nome da lista é obrigatório']);
                    exit();
                }

                $resultado = $lista->criarLista($dados['nome'], $dados['descricao'] ?? null);
                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Lista criada com sucesso']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao criar lista']);
                }
                exit();
            }
            // Adicionar item à lista
            if ($action === 'adicionar_item') {
                $dados = json_decode(file_get_contents('php://input'), true);
                debugLog("Dados recebidos para adicionar item: " . print_r($dados, true));

                if (!isset($dados['lista_id'], $dados['nome'])) {
                    debugLog("Dados inválidos para adicionar item");
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Dados inválidos para adicionar item'
                    ]);
                    exit();
                }

                try {
                    $lista->id = $dados['lista_id'];
                    $lista->usuario_id = $_SESSION['usuario_id'];
                    
                    // Definir a tabela de itens usando o método setter
                    $lista->setTabelaItens('itens_lista');
                    
                    // Adicionar item à lista
                    $item_id = $lista->adicionarItemLista(
                        $dados['nome'], 
                        $dados['quantidade'] ?? null, 
                        $dados['unidade'] ?? null
                    );

                    echo json_encode([
                        'success' => true,
                        'message' => 'Item adicionado com sucesso',
                        'item_id' => $item_id
                    ]);
                } catch (Exception $e) {
                    debugLog("Erro ao adicionar item: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao adicionar item',
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            break;

        case 'PUT':
            // Editar lista
            if ($action === 'editar_lista') {
                $dados = json_decode(file_get_contents('php://input'), true);
                debugLog("Dados recebidos para edição: " . print_r($dados, true));

                if (!isset($dados['lista_id'], $dados['nome'])) {
                    debugLog("Dados inválidos para edição");
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Dados inválidos para edição'
                    ]);
                    exit();
                }

                try {
                    $lista->id = $dados['lista_id'];
                    $lista->nome = $dados['nome'];
                    $lista->descricao = $dados['descricao'] ?? null;

                    $resultado = $lista->editar();

                    if ($resultado) {
                        debugLog("Lista editada com sucesso: {$lista->id}");
                        echo json_encode([
                            'success' => true,
                            'message' => 'Lista atualizada com sucesso'
                        ]);
                    } else {
                        debugLog("Falha ao editar lista");
                        http_response_code(500);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Falha ao atualizar lista'
                        ]);
                    }
                } catch (Exception $e) {
                    debugLog("Erro ao editar lista: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao atualizar lista',
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            // Marcar como concluída
            elseif ($action === 'marcar_concluida') {
                if (!isset($data['lista_id'])) {
                    debugLog("ID da lista não fornecido para marcar como concluída");
                    http_response_code(400);
                    echo json_encode(['error' => 'ID da lista não fornecido']);
                    exit();
                }

                try {
                    $lista->id = $data['lista_id'];
                    $resultado = $lista->marcarComoConcluida();
                    if ($resultado) {
                        debugLog("Lista marcada como concluída");
                        echo json_encode([
                            'success' => true,
                            'message' => 'Lista marcada como concluída'
                        ]);
                    } else {
                        debugLog("Falha ao marcar lista como concluída");
                        http_response_code(500);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Falha ao marcar lista como concluída',
                            'details' => 'Erro ao marcar lista como concluída'
                        ]);
                    }
                } catch (Exception $e) {
                    debugLog("Erro ao marcar lista como concluída: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao marcar lista como concluída',
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            // Marcar item como comprado/não comprado
            elseif ($action === 'marcar_item') {
                $dados = json_decode(file_get_contents('php://input'), true);
                debugLog("Dados recebidos para marcar item: " . print_r($dados, true));

                if (!isset($dados['lista_id'], $dados['item_id'], $dados['comprado'])) {
                    debugLog("Dados inválidos para marcar item");
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Dados inválidos para marcar item'
                    ]);
                    exit();
                }

                try {
                    $lista->id = $dados['lista_id'];
                    $lista->usuario_id = $_SESSION['usuario_id'];
                    
                    // Definir a tabela de itens usando o método setter
                    $lista->setTabelaItens('itens_lista');
                    
                    // Marcar item como comprado/não comprado
                    $query = "UPDATE " . $lista->getTabelaItens() . " 
                              SET comprado = :comprado 
                              WHERE id = :item_id AND lista_id = :lista_id";

                    $stmt = $lista->getConexao()->prepare($query);
                    $stmt->bindParam(":comprado", $dados['comprado'], PDO::PARAM_INT);
                    $stmt->bindParam(":item_id", $dados['item_id'], PDO::PARAM_INT);
                    $stmt->bindParam(":lista_id", $dados['lista_id'], PDO::PARAM_INT);

                    // Adicionar log de depuração
                    debugLog("Marcando item - Lista ID: {$dados['lista_id']}, Item ID: {$dados['item_id']}, Comprado: {$dados['comprado']}");
                    
                    // Executar query
                    $resultado = $stmt->execute();

                    // Verificar se a atualização foi bem-sucedida
                    if (!$resultado) {
                        debugLog("Falha ao marcar item");
                        debugLog("Erro: " . print_r($stmt->errorInfo(), true));
                        http_response_code(500);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Falha ao marcar item'
                        ]);
                        exit();
                    }

                    // Verificar se alguma linha foi afetada
                    $linhasAfetadas = $stmt->rowCount();
                    debugLog("Linhas afetadas na marcação do item: $linhasAfetadas");

                    echo json_encode([
                        'success' => true,
                        'message' => 'Item marcado com sucesso'
                    ]);
                } catch (Exception $e) {
                    debugLog("Erro ao marcar item: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao marcar item',
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            // Editar item da lista
            elseif ($action === 'editar_item') {
                $dados = json_decode(file_get_contents('php://input'), true);
                debugLog("Dados recebidos para editar item: " . print_r($dados, true));

                if (!isset($dados['lista_id'], $dados['item_id'], $dados['nome'])) {
                    debugLog("Dados inválidos para editar item");
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Dados inválidos para editar item'
                    ]);
                    exit();
                }

                try {
                    $lista->id = $dados['lista_id'];
                    $lista->usuario_id = $_SESSION['usuario_id'];
                    
                    // Definir a tabela de itens usando o método setter
                    $lista->setTabelaItens('itens_lista');
                    
                    // Editar item da lista
                    $linhasAfetadas = $lista->editarItemLista(
                        $dados['item_id'], 
                        $dados['nome'], 
                        $dados['quantidade'] ?? null, 
                        $dados['unidade'] ?? null
                    );

                    echo json_encode([
                        'success' => true,
                        'message' => 'Item editado com sucesso',
                        'linhas_afetadas' => $linhasAfetadas
                    ]);
                } catch (Exception $e) {
                    debugLog("Erro ao editar item: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao editar item',
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
                debugLog("Dados recebidos: " . json_encode($data));
                
                if (!isset($data['lista_id'])) {
                    debugLog("ID da lista não fornecido para exclusão");
                    http_response_code(400);
                    echo json_encode(['error' => 'ID da lista não fornecido']);
                    exit();
                }

                try {
                    $resultado = $lista->excluirLista($data['lista_id']);
                    if ($resultado) {
                        debugLog("Lista excluída com sucesso");
                        echo json_encode([
                            'success' => true,
                            'message' => 'Lista excluída com sucesso'
                        ]);
                    } else {
                        debugLog("Falha ao excluir lista");
                        http_response_code(500);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Falha ao excluir lista',
                            'details' => 'Erro ao excluir lista'
                        ]);
                    }
                } catch (Exception $e) {
                    debugLog("Erro ao excluir lista: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao excluir lista',
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            // Remover item da lista
            elseif ($action === 'remover_item') {
                $dados = json_decode(file_get_contents('php://input'), true);
                debugLog("Dados recebidos para remover item: " . print_r($dados, true));

                if (!isset($dados['lista_id'], $dados['item_id'])) {
                    debugLog("Dados inválidos para remover item");
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Dados inválidos para remover item'
                    ]);
                    exit();
                }

                try {
                    $lista->id = $dados['lista_id'];
                    $lista->usuario_id = $_SESSION['usuario_id'];
                    
                    // Definir a tabela de itens usando o método setter
                    $lista->setTabelaItens('itens_lista');
                    
                    // Remover item da lista
                    $linhasAfetadas = $lista->removerItemLista($dados['item_id']);

                    echo json_encode([
                        'success' => true,
                        'message' => 'Item removido com sucesso',
                        'linhas_afetadas' => $linhasAfetadas
                    ]);
                } catch (Exception $e) {
                    debugLog("Erro ao remover item: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao remover item',
                        'details' => $e->getMessage()
                    ]);
                }
                exit();
            }
            break;

        default:
            debugLog("Método não permitido: $method");
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            exit();
    }
} catch (Exception $e) {
    debugLog("Erro interno do servidor: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro interno do servidor',
        'details' => $e->getMessage()
    ]);
}

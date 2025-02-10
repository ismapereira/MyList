<?php
session_start();

// Configuração de erros e logs
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/logs/php_error.log');

// Função para log personalizado
function logError($message, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log .= " - Data: " . print_r($data, true);
    }
    error_log($log);
}

header('Content-Type: application/json');

try {
    logError("Iniciando processamento de remover_item.php");
    logError("POST data", $_POST);
    logError("SESSION data", $_SESSION);

    // Verificar se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Usuário não autenticado');
    }

    // Validar dados recebidos
    if (!isset($_POST['item_id'])) {
        throw new Exception('ID do item não fornecido');
    }

    require_once dirname(__DIR__) . '/config/database.php';
    require_once dirname(__DIR__) . '/models/Lista.php';

    $lista = new Lista();
    $item_id = intval($_POST['item_id']);

    // Verificar se o item pertence a uma lista do usuário
    if (!$lista->verificarProprietarioItem($item_id, $_SESSION['user_id'])) {
        throw new Exception('Você não tem permissão para remover este item');
    }

    // Remover o item
    if ($lista->removerItem($item_id)) {
        logError("Item removido com sucesso", ['item_id' => $item_id]);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Item removido com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao remover o item');
    }

} catch (Exception $e) {
    logError("Erro ao remover item: " . $e->getMessage());
    logError("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}

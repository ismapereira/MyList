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
    logError("Iniciando processamento de marcar_item.php");
    logError("POST data", $_POST);
    logError("SESSION data", $_SESSION);

    // Verificar se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Usuário não autenticado');
    }

    // Validar dados recebidos
    if (!isset($_POST['item_id']) || !isset($_POST['comprado'])) {
        throw new Exception('Dados incompletos');
    }

    require_once dirname(__DIR__) . '/models/Lista.php';

    $lista_model = new Lista();
    $item_id = intval($_POST['item_id']);
    $comprado = intval($_POST['comprado']);

    // Marcar item como comprado/não comprado
    $resultado = $lista_model->marcarItemComprado($item_id, $comprado === 1);

    if($resultado) {
        logError("Item marcado com sucesso", [
            'item_id' => $item_id,
            'comprado' => $comprado
        ]);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Status do item atualizado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao atualizar status do item');
    }

} catch (Exception $e) {
    logError("Erro ao marcar item: " . $e->getMessage());
    logError("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}

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

// Garantir que a resposta seja sempre JSON
header('Content-Type: application/json');

try {
    logError("Iniciando processamento de adicionar_item.php");
    logError("POST data", $_POST);
    logError("SESSION data", $_SESSION);

    // Verificar se o usuário está logado
    if(!isset($_SESSION['user_id'])) {
        logError("Falha na autenticação: user_id não encontrado na sessão");
        throw new Exception('Usuário não autenticado');
    }

    // Incluir arquivos necessários
    $config_file = dirname(__DIR__) . '/config/database.php';
    $model_file = dirname(__DIR__) . '/models/Lista.php';

    if (!file_exists($config_file)) {
        logError("Arquivo de configuração não encontrado: " . $config_file);
        throw new Exception('Erro de configuração do sistema');
    }

    if (!file_exists($model_file)) {
        logError("Arquivo de modelo não encontrado: " . $model_file);
        throw new Exception('Erro de configuração do sistema');
    }

    require_once $config_file;
    require_once $model_file;

    // Validar dados recebidos
    $campos_obrigatorios = ['lista_id', 'nome', 'quantidade', 'unidade'];
    $dados_faltando = [];
    foreach ($campos_obrigatorios as $campo) {
        if (!isset($_POST[$campo]) || trim($_POST[$campo]) === '') {
            $dados_faltando[] = $campo;
        }
    }

    if (!empty($dados_faltando)) {
        logError("Campos obrigatórios faltando", $dados_faltando);
        throw new Exception('Campos obrigatórios faltando: ' . implode(', ', $dados_faltando));
    }

    // Instanciar Lista e configurar
    $lista_model = new Lista();
    $lista_model->id = intval($_POST['lista_id']);
    $lista_model->usuario_id = $_SESSION['user_id'];

    // Verificar se a lista pertence ao usuário
    if (!$lista_model->verificarProprietario()) {
        logError("Tentativa de adicionar item em lista não pertencente ao usuário", [
            'lista_id' => $_POST['lista_id'],
            'usuario_id' => $_SESSION['user_id']
        ]);
        throw new Exception('Você não tem permissão para modificar esta lista');
    }

    // Adicionar item
    $resultado = $lista_model->adicionarItem(
        trim($_POST['nome']), 
        floatval($_POST['quantidade']), 
        trim($_POST['unidade'])
    );

    if($resultado) {
        $item_id = $lista_model->getUltimoItemId();
        logError("Item adicionado com sucesso", [
            'item_id' => $item_id,
            'lista_id' => $_POST['lista_id']
        ]);
        echo json_encode([
            'sucesso' => true, 
            'item_id' => $item_id,
            'mensagem' => 'Item adicionado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao adicionar item no banco de dados');
    }

} catch (Exception $e) {
    logError("Erro ao adicionar item: " . $e->getMessage());
    logError("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'sucesso' => false, 
        'mensagem' => $e->getMessage(),
        'erro_detalhes' => 'Verifique o log para mais informações'
    ]);
}

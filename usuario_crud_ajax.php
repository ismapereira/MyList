<?php
session_start();
header('Content-Type: application/json');

require_once 'config/database.php';
require_once 'models/Usuario.php';

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

$usuario = new Usuario();
$usuario->id = $_SESSION['user_id'];

// Carrega os dados atuais do usuário
if (!$usuario->obterPorId()) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuário não encontrado']);
    exit();
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if ($action === 'dados_usuario') {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'nome' => $usuario->nome,
                        'email' => $usuario->email,
                        'data_criacao' => $usuario->data_criacao,
                        'preferencias' => $usuario->preferencias,
                        'total_listas' => $usuario->getTotalListas()
                    ]
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Ação inválida']);
            }
            break;

        case 'POST':
            $dados = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['error' => 'JSON inválido']);
                exit();
            }

            if ($action === 'atualizar_perfil') {
                if (empty($dados['nome']) || empty($dados['email'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Nome e email são obrigatórios']);
                    exit();
                }

                if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email inválido']);
                    exit();
                }

                $usuario->nome = $dados['nome'];
                $usuario->email = $dados['email'];
                
                if ($usuario->atualizarPerfil()) {
                    $_SESSION['usuario_nome'] = $usuario->nome; // Atualiza o nome na sessão
                    echo json_encode([
                        'success' => true,
                        'message' => 'Perfil atualizado com sucesso',
                        'data' => [
                            'nome' => $usuario->nome,
                            'email' => $usuario->email
                        ],
                        'reload' => true
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao atualizar perfil']);
                }
            }
            elseif ($action === 'atualizar_senha') {
                if (empty($dados['senha_atual']) || empty($dados['nova_senha']) || empty($dados['confirmar_senha'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Todos os campos de senha são obrigatórios']);
                    exit();
                }

                if ($dados['nova_senha'] !== $dados['confirmar_senha']) {
                    http_response_code(400);
                    echo json_encode(['error' => 'As senhas não coincidem']);
                    exit();
                }

                if (strlen($dados['nova_senha']) < 6) {
                    http_response_code(400);
                    echo json_encode(['error' => 'A nova senha deve ter pelo menos 6 caracteres']);
                    exit();
                }

                if ($usuario->verificarSenha($dados['senha_atual'])) {
                    if ($usuario->atualizarSenha($dados['nova_senha'])) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Senha atualizada com sucesso',
                            'reload' => true
                        ]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['error' => 'Erro ao atualizar senha']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Senha atual incorreta']);
                }
            }
            elseif ($action === 'atualizar_preferencias') {
                if (!isset($dados['tema_escuro']) || !isset($dados['notificacoes_email']) || !isset($dados['mostrar_concluidas'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Todas as preferências são obrigatórias']);
                    exit();
                }

                $preferencias = [
                    'tema_escuro' => (bool) $dados['tema_escuro'],
                    'notificacoes_email' => (bool) $dados['notificacoes_email'],
                    'mostrar_concluidas' => (bool) $dados['mostrar_concluidas']
                ];

                if ($usuario->atualizarPreferencias($preferencias)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Preferências atualizadas com sucesso',
                        'data' => $preferencias,
                        'reload' => true
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao atualizar preferências']);
                }
            }
            elseif ($action === 'excluir_conta') {
                if (empty($dados['senha']) || !$usuario->verificarSenha($dados['senha'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Senha incorreta']);
                    exit();
                }

                if ($usuario->excluirConta()) {
                    session_destroy();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Conta excluída com sucesso',
                        'redirect' => 'login.php'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao excluir conta']);
                }
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro interno do servidor',
        'message' => $e->getMessage()
    ]);
}
?>

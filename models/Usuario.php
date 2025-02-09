<?php
require_once 'config/database.php';

class Usuario {
    private $conexao;
    private $tabela = 'usuarios';

    // Propriedades do usuário
    public $id;
    public $nome;
    public $email;
    public $senha;
    public $data_criacao;
    public $preferencias;

    // Construtor
    public function __construct() {
        $database = new Database();
        $this->conexao = $database->conectar();
    }

    // Método para criar um novo usuário
    public function criar() {
        // Consulta para inserir usuário
        $query = "INSERT INTO " . $this->tabela . " 
                  SET nome = :nome, email = :email, senha = :senha";

        // Prepara a declaração
        $stmt = $this->conexao->prepare($query);

        // Limpa os dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        // Vincula os valores
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);

        // Executa a query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para autenticar usuário
    public function autenticar() {
        $query = "SELECT * FROM " . $this->tabela . " WHERE email = :email LIMIT 0,1";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se encontrou o usuário e se a senha está correta
        if($linha && password_verify($this->senha, $linha['senha'])) {
            // Define as propriedades do usuário
            $this->id = $linha['id'];
            $this->nome = $linha['nome'];
            return true;
        }

        return false;
    }

    // Método para buscar usuário por ID
    public function obterPorId() {
        $query = "SELECT id, nome, email, data_criacao, preferencias FROM " . $this->tabela . " WHERE id = :id LIMIT 0,1";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if($linha) {
            $this->nome = $linha['nome'];
            $this->email = $linha['email'];
            $this->data_criacao = $linha['data_criacao'];
            $this->preferencias = json_decode($linha['preferencias'], true) ?? [];
            return true;
        }

        return false;
    }

    // Método para verificar se o email já existe
    public function emailExiste() {
        $query = "SELECT COUNT(*) FROM " . $this->tabela . " WHERE email = :email AND id != :id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Método para atualizar perfil
    public function atualizarPerfil() {
        // Verifica se o email já existe para outro usuário
        if ($this->emailExiste()) {
            throw new Exception("Este e-mail já está em uso");
        }

        $query = "UPDATE " . $this->tabela . " 
                  SET nome = :nome, email = :email 
                  WHERE id = :id";

        $stmt = $this->conexao->prepare($query);

        // Limpa os dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Vincula os valores
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Método para verificar senha atual
    public function verificarSenha($senha) {
        $query = "SELECT senha FROM " . $this->tabela . " WHERE id = :id";
        
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $hash = $stmt->fetchColumn();
        
        return password_verify($senha, $hash);
    }

    // Método para atualizar senha
    public function atualizarSenha($nova_senha) {
        $query = "UPDATE " . $this->tabela . " 
                  SET senha = :senha 
                  WHERE id = :id";

        $stmt = $this->conexao->prepare($query);

        // Hash da nova senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Vincula os valores
        $stmt->bindParam(":senha", $senha_hash);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Método para atualizar preferências
    public function atualizarPreferencias($preferencias) {
        $query = "UPDATE " . $this->tabela . " 
                  SET preferencias = :preferencias 
                  WHERE id = :id";

        $stmt = $this->conexao->prepare($query);

        // Converte array para JSON
        $preferencias_json = json_encode($preferencias);

        // Vincula os valores
        $stmt->bindParam(":preferencias", $preferencias_json);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            $this->preferencias = $preferencias;
            return true;
        }

        return false;
    }

    // Método para obter total de listas
    public function getTotalListas() {
        $query = "SELECT COUNT(*) FROM listas WHERE usuario_id = :usuario_id";
        
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":usuario_id", $this->id);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    // Método para excluir conta
    public function excluirConta() {
        try {
            // Inicia uma transação
            $this->conexao->beginTransaction();

            // Exclui todas as listas do usuário (as listas já têm ON DELETE CASCADE para itens)
            $query = "DELETE FROM listas WHERE usuario_id = :id";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(":id", $this->id);
            $stmt->execute();

            // Exclui o usuário
            $query = "DELETE FROM " . $this->tabela . " WHERE id = :id";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(":id", $this->id);
            $stmt->execute();

            // Confirma a transação
            $this->conexao->commit();
            return true;
        } catch (Exception $e) {
            // Em caso de erro, desfaz as alterações
            $this->conexao->rollBack();
            return false;
        }
    }
}
?>

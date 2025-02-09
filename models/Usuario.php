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
}
?>

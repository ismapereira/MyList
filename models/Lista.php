<?php
require_once 'config/database.php';

class Lista {
    private $conexao;
    private $tabela_listas = 'listas';
    private $tabela_itens = 'itens_lista';

    // Propriedades da lista
    public $id;
    public $nome;
    public $descricao;
    public $usuario_id;
    public $itens = [];

    // Construtor
    public function __construct() {
        $database = new Database();
        $this->conexao = $database->conectar();
    }

    // Criar nova lista
    public function criar() {
        $query = "INSERT INTO " . $this->tabela_listas . " 
                  SET nome = :nome, descricao = :descricao, usuario_id = :usuario_id";

        $stmt = $this->conexao->prepare($query);

        // Limpa e vincula os dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        if($stmt->execute()) {
            $this->id = $this->conexao->lastInsertId();
            return true;
        }

        return false;
    }

    // Adicionar item à lista
    public function adicionarItem($nome, $quantidade, $unidade) {
        $query = "INSERT INTO " . $this->tabela_itens . " 
                  SET lista_id = :lista_id, nome = :nome, 
                  quantidade = :quantidade, unidade = :unidade";

        $stmt = $this->conexao->prepare($query);

        // Limpa e vincula os dados
        $nome = htmlspecialchars(strip_tags($nome));
        $unidade = htmlspecialchars(strip_tags($unidade));

        $stmt->bindParam(":lista_id", $this->id);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":quantidade", $quantidade);
        $stmt->bindParam(":unidade", $unidade);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para obter o último ID do item inserido
    public function getUltimoItemId() {
        return $this->conexao->lastInsertId();
    }

    // Buscar listas de um usuário
    public function buscarListasUsuario($usuario_id) {
        $query = "SELECT * FROM " . $this->tabela_listas . " 
                  WHERE usuario_id = :usuario_id ORDER BY data_criacao DESC";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar itens de uma lista
    public function buscarItens() {
        $query = "SELECT * FROM " . $this->tabela_itens . " 
                  WHERE lista_id = :lista_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":lista_id", $this->id);
        $stmt->execute();

        $this->itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->itens;
    }

    // Marcar item como comprado
    public function marcarItemComprado($item_id, $comprado) {
        $query = "UPDATE " . $this->tabela_itens . " 
                  SET comprado = :comprado 
                  WHERE id = :item_id AND lista_id = :lista_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":comprado", $comprado, PDO::PARAM_BOOL);
        $stmt->bindParam(":item_id", $item_id);
        $stmt->bindParam(":lista_id", $this->id);

        return $stmt->execute();
    }
}
?>

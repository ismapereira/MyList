<?php
require_once 'config/database.php';

class Lista {
    private $conexao;
    private $tabela_listas = 'listas';
    private $tabela_itens_privada = 'itens_lista';

    // Propriedades da lista
    public $id;
    public $nome;
    public $descricao;
    public $usuario_id;
    public $itens = [];
    public $status;

    // Construtor
    public function __construct() {
        $database = new Database();
        $this->conexao = $database->conectar();
    }

    // Método para obter a conexão
    public function getConexao() {
        return $this->conexao;
    }

    // Getter para tabela de itens
    public function getTabelaItens() {
        return $this->tabela_itens_privada;
    }

    // Setter para tabela de itens
    public function setTabelaItens($tabela) {
        $this->tabela_itens_privada = $tabela;
    }

    // Método mágico para lidar com propriedades indefinidas
    public function __get($nome) {
        if ($nome === 'tabela_itens') {
            return $this->getTabelaItens();
        }
        return null;
    }

    // Método mágico para definir propriedades
    public function __set($nome, $valor) {
        if ($nome === 'tabela_itens') {
            $this->setTabelaItens($valor);
        }
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

    // Criar nova lista
    public function criarLista($nome, $descricao = null) {
        // Validar entrada
        if (empty($nome)) {
            throw new Exception("Nome da lista é obrigatório");
        }

        // Limpar e sanitizar dados
        $nome = htmlspecialchars(strip_tags($nome));
        $descricao = $descricao ? htmlspecialchars(strip_tags($descricao)) : null;

        // Preparar query
        $query = "INSERT INTO " . $this->tabela_listas . " 
                  SET nome = :nome, 
                      descricao = :descricao, 
                      usuario_id = :usuario_id";

        $stmt = $this->conexao->prepare($query);

        // Bind de parâmetros
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        // Executar e retornar resultado
        return $stmt->execute();
    }

    // Atualizar lista existente
    public function atualizarLista($lista_id, $nome, $descricao = null) {
        // Validar entrada
        if (empty($nome)) {
            throw new Exception("Nome da lista é obrigatório");
        }

        // Limpar e sanitizar dados
        $nome = htmlspecialchars(strip_tags($nome));
        $descricao = $descricao ? htmlspecialchars(strip_tags($descricao)) : null;

        // Preparar query
        $query = "UPDATE " . $this->tabela_listas . " 
                  SET nome = :nome, 
                      descricao = :descricao 
                  WHERE id = :lista_id AND usuario_id = :usuario_id";

        $stmt = $this->conexao->prepare($query);

        // Bind de parâmetros
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->bindParam(":lista_id", $lista_id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        // Executar
        return $stmt->execute();
    }

    // Excluir lista
    public function excluirLista($lista_id) {
        // Preparar query
        $query = "DELETE FROM " . $this->tabela_listas . " 
                  WHERE id = :lista_id AND usuario_id = :usuario_id";

        $stmt = $this->conexao->prepare($query);

        // Bind de parâmetros
        $stmt->bindParam(":lista_id", $lista_id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        // Executar
        return $stmt->execute();
    }

    // Adicionar item à lista
    public function adicionarItem($nome, $quantidade, $unidade) {
        $query = "INSERT INTO " . $this->getTabelaItens() . " 
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
        $query = "SELECT * FROM " . $this->getTabelaItens() . " 
                  WHERE lista_id = :lista_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":lista_id", $this->id);
        $stmt->execute();

        $this->itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->itens;
    }

    // Marcar item como comprado
    public function marcarItemComprado($item_id, $comprado) {
        $query = "UPDATE " . $this->getTabelaItens() . " 
                  SET comprado = :comprado 
                  WHERE id = :item_id AND lista_id = :lista_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":comprado", $comprado, PDO::PARAM_BOOL);
        $stmt->bindParam(":item_id", $item_id);
        $stmt->bindParam(":lista_id", $this->id);

        return $stmt->execute();
    }

    // Listar listas do usuário com detalhes de itens
    public function listarListasUsuario() {
        $query = "SELECT 
                    l.id, 
                    l.nome, 
                    l.descricao, 
                    l.data_criacao,
                    (SELECT COUNT(*) FROM " . $this->getTabelaItens() . " 
                     WHERE lista_id = l.id AND status = 'pendente') as itens_pendentes,
                    (SELECT COUNT(*) FROM " . $this->getTabelaItens() . " 
                     WHERE lista_id = l.id AND status = 'concluido') as itens_concluidos,
                    CASE 
                        WHEN (SELECT COUNT(*) FROM " . $this->getTabelaItens() . " 
                              WHERE lista_id = l.id) = 0
                        THEN 'vazia'
                        WHEN (SELECT COUNT(*) FROM " . $this->getTabelaItens() . " 
                              WHERE lista_id = l.id) = 
                             (SELECT COUNT(*) FROM " . $this->getTabelaItens() . " 
                              WHERE lista_id = l.id AND status = 'concluido')
                        THEN 'concluida'
                        ELSE 'em_andamento'
                    END as status
                  FROM " . $this->tabela_listas . " l
                  WHERE l.usuario_id = :usuario_id 
                  ORDER BY l.data_criacao DESC";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obter detalhes da lista
    public function obterDetalhesLista() {
        $query = "SELECT l.id, l.nome, l.descricao, l.data_criacao, 
                    (SELECT COUNT(*) FROM " . $this->getTabelaItens() . " 
                     WHERE lista_id = l.id) as total_itens
                  FROM " . $this->tabela_listas . " l
                  WHERE l.id = :id AND l.usuario_id = :usuario_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar itens da lista
    public function buscarItensLista() {
        // Validar se o ID da lista está definido
        if (!$this->id) {
            error_log("[MyList Debug] ID da lista não definido ao buscar itens");
            throw new Exception("ID da lista não definido");
        }

        $query = "SELECT id, nome, quantidade, unidade, comprado 
                  FROM " . $this->getTabelaItens() . " 
                  WHERE lista_id = :lista_id 
                  ORDER BY comprado, nome";

        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(":lista_id", $this->id, PDO::PARAM_INT);
            $stmt->execute();

            // Buscar todos os itens
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Log de depuração
            error_log("[MyList Debug] Itens encontrados para lista {$this->id}: " . count($itens));
            
            // Atualizar propriedade de itens da classe
            $this->itens = $itens;

            return $itens;
        } catch (PDOException $e) {
            // Log do erro para depuração
            error_log("[MyList Debug] Erro ao buscar itens da lista: " . $e->getMessage());
            error_log("[MyList Debug] Query: $query");
            error_log("[MyList Debug] ID da lista: {$this->id}");
            error_log("[MyList Debug] Erro detalhado: " . print_r($e, true));
            
            throw new Exception("Erro ao buscar itens da lista: " . $e->getMessage());
        }
    }

    // Adicionar item à lista
    public function adicionarItemLista($nome, $quantidade = null, $unidade = null) {
        if (empty($this->id)) {
            throw new Exception("ID da lista não definido");
        }

        try {
            $query = "INSERT INTO " . $this->getTabelaItens() . " 
                      (lista_id, nome, quantidade, unidade, comprado) 
                      VALUES (:lista_id, :nome, :quantidade, :unidade, 0)";

            $stmt = $this->getConexao()->prepare($query);
            $stmt->bindParam(":lista_id", $this->id, PDO::PARAM_INT);
            $stmt->bindParam(":nome", $nome, PDO::PARAM_STR);
            $stmt->bindParam(":quantidade", $quantidade, $quantidade ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(":unidade", $unidade, $unidade ? PDO::PARAM_STR : PDO::PARAM_NULL);

            // Log de depuração
            error_log("[MyList Debug] Adicionando item à lista - Lista ID: {$this->id}, Nome: $nome, Quantidade: $quantidade, Unidade: $unidade");

            $resultado = $stmt->execute();

            if (!$resultado) {
                error_log("[MyList Debug] Falha ao adicionar item à lista");
                error_log("[MyList Debug] Erro: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Falha ao adicionar item à lista");
            }

            // Retornar o ID do item inserido
            return $this->getConexao()->lastInsertId();

        } catch (Exception $e) {
            error_log("[MyList Debug] Erro ao adicionar item à lista: " . $e->getMessage());
            throw $e;
        }
    }

    // Método para editar um item da lista
    public function editarItemLista($item_id, $nome, $quantidade = null, $unidade = null) {
        if (empty($this->id)) {
            throw new Exception("ID da lista não definido");
        }

        try {
            $query = "UPDATE " . $this->getTabelaItens() . " 
                      SET nome = :nome, 
                          quantidade = :quantidade, 
                          unidade = :unidade 
                      WHERE id = :item_id AND lista_id = :lista_id";

            $stmt = $this->getConexao()->prepare($query);
            $stmt->bindParam(":nome", $nome, PDO::PARAM_STR);
            $stmt->bindParam(":quantidade", $quantidade, $quantidade ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(":unidade", $unidade, $unidade ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(":item_id", $item_id, PDO::PARAM_INT);
            $stmt->bindParam(":lista_id", $this->id, PDO::PARAM_INT);

            // Log de depuração
            error_log("[MyList Debug] Editando item da lista - Lista ID: {$this->id}, Item ID: $item_id, Nome: $nome, Quantidade: $quantidade, Unidade: $unidade");

            $resultado = $stmt->execute();

            if (!$resultado) {
                error_log("[MyList Debug] Falha ao editar item da lista");
                error_log("[MyList Debug] Erro: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Falha ao editar item da lista");
            }

            // Retornar o número de linhas afetadas
            return $stmt->rowCount();

        } catch (Exception $e) {
            error_log("[MyList Debug] Erro ao editar item da lista: " . $e->getMessage());
            throw $e;
        }
    }

    // Método para remover um item da lista
    public function removerItemLista($item_id) {
        if (empty($this->id)) {
            throw new Exception("ID da lista não definido");
        }

        try {
            $query = "DELETE FROM " . $this->getTabelaItens() . " 
                      WHERE id = :item_id AND lista_id = :lista_id";

            $stmt = $this->getConexao()->prepare($query);
            $stmt->bindParam(":item_id", $item_id, PDO::PARAM_INT);
            $stmt->bindParam(":lista_id", $this->id, PDO::PARAM_INT);

            // Log de depuração
            error_log("[MyList Debug] Removendo item da lista - Lista ID: {$this->id}, Item ID: $item_id");

            $resultado = $stmt->execute();

            if (!$resultado) {
                error_log("[MyList Debug] Falha ao remover item da lista");
                error_log("[MyList Debug] Erro: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Falha ao remover item da lista");
            }

            // Retornar o número de linhas afetadas
            return $stmt->rowCount();

        } catch (Exception $e) {
            error_log("[MyList Debug] Erro ao remover item da lista: " . $e->getMessage());
            throw $e;
        }
    }

    // Atualizar lista
    public function atualizar() {
        $query = "UPDATE " . $this->tabela_listas . "
                  SET nome = :nome, 
                      descricao = :descricao,
                      status = :status
                  WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->conexao->prepare($query);

        // Limpa e vincula os dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        return $stmt->execute();
    }

    // Marcar lista como concluída
    public function marcarComoConcluida() {
        $query = "UPDATE " . $this->tabela_listas . "
                  SET status = 'concluida'
                  WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        if ($stmt->execute()) {
            // Atualizar todos os itens da lista como concluídos
            $query = "UPDATE " . $this->getTabelaItens() . "
                      SET status = 'concluido'
                      WHERE lista_id = :lista_id";
            
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(":lista_id", $this->id);
            
            return $stmt->execute();
        }

        return false;
    }

    // Obter lista por ID
    public function obterPorId() {
        // Validar se o ID e o usuário_id estão definidos
        if (!$this->id || !$this->usuario_id) {
            error_log("[MyList Debug] ID da lista ou usuário não definido");
            error_log("[MyList Debug] ID da lista: " . ($this->id ?: 'NÃO DEFINIDO'));
            error_log("[MyList Debug] ID do usuário: " . ($this->usuario_id ?: 'NÃO DEFINIDO'));
            throw new Exception("ID da lista ou usuário não definido");
        }

        $query = "SELECT id, nome, descricao, data_criacao
                  FROM " . $this->tabela_listas . "
                  WHERE id = :id AND usuario_id = :usuario_id";

        try {
            $stmt = $this->conexao->prepare($query);
            
            // Adicionar log de depuração
            error_log("[MyList Debug] Tentando obter lista com ID: {$this->id}, Usuário ID: {$this->usuario_id}");
            error_log("[MyList Debug] Query: $query");
            
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->bindParam(":usuario_id", $this->usuario_id, PDO::PARAM_INT);
            
            // Verificar estado da conexão
            error_log("[MyList Debug] Estado da conexão: " . $this->conexao->getAttribute(PDO::ATTR_CONNECTION_STATUS));
            
            $stmt->execute();
            
            // Adicionar log de depuração para o número de linhas
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("[MyList Debug] Resultado da busca: " . ($resultado ? json_encode($resultado) : "Nenhum resultado"));
            
            return $resultado;
        } catch (PDOException $e) {
            // Log do erro para depuração
            error_log("[MyList Debug] Erro ao obter lista: " . $e->getMessage());
            error_log("[MyList Debug] Query: $query");
            error_log("[MyList Debug] Parâmetros - ID: {$this->id}, Usuário ID: {$this->usuario_id}");
            error_log("[MyList Debug] Erro detalhado: " . print_r($e, true));
            
            throw new Exception("Erro ao buscar lista: " . $e->getMessage());
        }
    }

    // Editar lista existente
    public function editar() {
        // Validar dados obrigatórios
        if (!$this->id || !$this->nome) {
            error_log("[MyList Debug] Dados inválidos para edição de lista");
            error_log("[MyList Debug] ID da lista: " . ($this->id ?: 'NÃO DEFINIDO'));
            error_log("[MyList Debug] Nome da lista: " . ($this->nome ?: 'NÃO DEFINIDO'));
            throw new Exception("Dados inválidos para edição de lista");
        }

        // Preparar query de atualização
        $query = "UPDATE " . $this->tabela_listas . " 
                  SET nome = :nome, 
                      descricao = :descricao
                  WHERE id = :id AND usuario_id = :usuario_id";

        try {
            // Preparar statement
            $stmt = $this->conexao->prepare($query);

            // Limpar e validar dados
            $this->nome = htmlspecialchars(strip_tags($this->nome));
            $this->descricao = $this->descricao ? htmlspecialchars(strip_tags($this->descricao)) : null;

            // Vincular parâmetros
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":descricao", $this->descricao);
            $stmt->bindParam(":usuario_id", $this->usuario_id, PDO::PARAM_INT);

            // Adicionar log de depuração
            error_log("[MyList Debug] Editando lista - ID: {$this->id}, Nome: {$this->nome}, Descrição: " . ($this->descricao ?: 'VAZIO'));
            
            // Executar query
            $resultado = $stmt->execute();

            // Verificar se a atualização foi bem-sucedida
            if (!$resultado) {
                error_log("[MyList Debug] Falha ao editar lista");
                error_log("[MyList Debug] Erro: " . print_r($stmt->errorInfo(), true));
                return false;
            }

            // Verificar se alguma linha foi afetada
            $linhasAfetadas = $stmt->rowCount();
            error_log("[MyList Debug] Linhas afetadas na edição: $linhasAfetadas");

            return $linhasAfetadas > 0;
        } catch (PDOException $e) {
            // Log do erro para depuração
            error_log("[MyList Debug] Erro ao editar lista: " . $e->getMessage());
            error_log("[MyList Debug] Query: $query");
            error_log("[MyList Debug] Parâmetros - ID: {$this->id}, Nome: {$this->nome}, Descrição: " . ($this->descricao ?: 'VAZIO'));
            error_log("[MyList Debug] Erro detalhado: " . print_r($e, true));
            
            throw new Exception("Erro ao editar lista: " . $e->getMessage());
        }
    }
}
?>

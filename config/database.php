<?php
// Configurações de conexão com o banco de dados MySQL
class Database {
    // Configurações do banco de dados
    private $host = 'localhost';
    private $usuario = 'root';
    private $senha = '';
    private $banco = 'lista_mercado';
    
    // Conexão com o banco de dados
    public function conectar() {
        try {
            // Cria a conexão PDO com o MySQL
            $conexao = new PDO(
                "mysql:host={$this->host};dbname={$this->banco};charset=utf8", 
                $this->usuario, 
                $this->senha
            );
            
            // Configura para lançar exceções em caso de erro
            $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $conexao;
        } catch(PDOException $erro) {
            // Registra o erro em um log (em produção)
            error_log("Erro de conexão: " . $erro->getMessage());
            
            // Retorna null em caso de falha
            return null;
        }
    }
}
?>

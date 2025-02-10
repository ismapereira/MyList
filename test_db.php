<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->conectar();
    echo "Conexão com o banco de dados estabelecida com sucesso!\n";
    
    // Testar a tabela de listas
    $stmt = $conn->query("SHOW TABLES LIKE 'listas'");
    if ($stmt->rowCount() > 0) {
        echo "Tabela 'listas' encontrada\n";
        
        // Mostrar estrutura da tabela
        $stmt = $conn->query("DESCRIBE listas");
        echo "Estrutura da tabela 'listas':\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    } else {
        echo "Tabela 'listas' não encontrada\n";
    }
    
    // Testar a tabela de itens
    $stmt = $conn->query("SHOW TABLES LIKE 'itens_lista'");
    if ($stmt->rowCount() > 0) {
        echo "\nTabela 'itens_lista' encontrada\n";
        
        // Mostrar estrutura da tabela
        $stmt = $conn->query("DESCRIBE itens_lista");
        echo "Estrutura da tabela 'itens_lista':\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    } else {
        echo "\nTabela 'itens_lista' não encontrada\n";
    }
    
} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage() . "\n";
    echo "Código do erro: " . $e->getCode() . "\n";
}

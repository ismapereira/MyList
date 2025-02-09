-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS lista_mercado;
USE lista_mercado;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de listas de mercado
CREATE TABLE listas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    usuario_id INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de itens da lista
CREATE TABLE itens_lista (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lista_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    quantidade DECIMAL(10,2) DEFAULT 1,
    unidade VARCHAR(20) DEFAULT 'un',
    status ENUM('pendente', 'concluido') DEFAULT 'pendente',
    FOREIGN KEY (lista_id) REFERENCES listas(id) ON DELETE CASCADE
);

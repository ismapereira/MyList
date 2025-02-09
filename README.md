# MyList 🛒 - Sistema Inteligente de Gerenciamento de Listas de Compras

[![Version](https://img.shields.io/badge/version-1.0-blue.svg)](https://github.com/ismapereira/MyList)
[![Status](https://img.shields.io/badge/status-stable-green.svg)](https://github.com/ismapereira/MyList)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![AI Powered](https://img.shields.io/badge/AI%20Powered-Yes-purple.svg)](https://github.com/ismapereira/MyList)

## 📝 Descrição do Projeto

MyList é uma aplicação web moderna e inteligente para gerenciamento de listas de compras, desenvolvida com o objetivo de simplificar e otimizar o processo de criação, organização e acompanhamento de compras.

## 🌟 Principais Características

- 🔐 Sistema robusto de autenticação de usuários
- 📋 Criação e gerenciamento de múltiplas listas de compras
- ✅ Marcação intuitiva de itens como comprados
- 📊 Dashboard com estatísticas detalhadas
- 📱 Design responsivo e acessível
- 🖨️ Exportação de listas em PDF com design elegante

## 🏗️ Arquitetura do Sistema

### Estrutura de Diretórios

```
mylist/
├── assets/                 # Recursos estáticos
│   ├── css/                # Folhas de estilo
│   │   └── tailwind.min.css
│   └── js/                 # Scripts JavaScript
│       └── main.js
│
├── components/             # Componentes reutilizáveis
│   ├── header.php
│   ├── footer.php
│   └── sidebar.php
│
├── config/                 # Configurações do sistema
│   ├── database.php        # Configurações de conexão com banco de dados
│   └── constants.php       # Constantes globais
│
├── models/                 # Modelos de dados
│   ├── Usuario.php         # Modelo de usuário
│   ├── Lista.php           # Modelo de lista
│   └── Item.php            # Modelo de item
│
├── controllers/            # Controladores de lógica
│   ├── UsuarioController.php
│   ├── ListaController.php
│   └── ItemController.php
│
└── views/                  # Páginas e templates
    ├── dashboard.php
    ├── login.php
    ├── registro.php
    └── export_pdf.php
```

### Componentes Principais

#### 1. Modelo de Usuário (`models/Usuario.php`)
- Gerencia autenticação e informações do usuário
- Métodos principais:
  - `autenticar()`: Validação de credenciais
  - `registrar()`: Criação de nova conta
  - `obterPorId()`: Recuperação de dados do usuário

#### 2. Modelo de Lista (`models/Lista.php`)
- Gerencia operações relacionadas às listas de compras
- Funcionalidades:
  - Criação de novas listas
  - Listagem de listas por usuário
  - Estatísticas de listas
  - Gerenciamento de itens

#### 3. Modelo de Item (`models/Item.php`)
- Controla operações de itens nas listas
- Métodos importantes:
  - `adicionarItem()`
  - `marcarComoComprado()`
  - `removerItem()`

### Fluxo de Autenticação

1. Usuário acessa página de login
2. Credenciais são validadas no banco de dados
3. Sessão é iniciada com `user_id`
4. Usuário redirecionado para dashboard

### Exportação de PDF

- Utiliza biblioteca TCPDF
- Gera PDF com design minimalista
- Inclui informações da lista e seus itens
- Personalização de cores e layout

## 🔧 Tecnologias e Dependências

- **Linguagem:** PHP 8.0+
- **Frontend:** 
  - HTML5
  - Tailwind CSS
  - JavaScript
- **Backend:** 
  - PDO para acesso ao banco de dados
  - Arquitetura MVC
- **Banco de Dados:** MySQL
- **Bibliotecas:**
  - TCPDF (geração de PDFs)
  - Composer para gerenciamento de dependências

## 📦 Instalação e Configuração

### Pré-requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Composer
- Servidor web (Apache/Nginx)

### Passos de Instalação

1. Clone o repositório
```bash
git clone https://github.com/ismapereira/MyList.git
```

2. Instale dependências
```bash
composer install
```

3. Configure o banco de dados
   - Copie `config/database.example.php` para `config/database.php`
   - Edite com suas credenciais de banco de dados

4. Importe o schema
```bash
mysql -u seu_usuario -p mylist_db < config/schema.sql
```

## 🔒 Segurança

- Senhas hash com PHP password_hash()
- Prepared statements contra SQL Injection
- Validação de entrada de dados
- Proteção contra CSRF
- Sessões seguras

## 🤖 Desenvolvimento com IA

- Desenvolvimento assistido por Inteligência Artificial
- Uso de prompts especializados
- Otimização de código
- Documentação gerada com IA
- Integração entre desenvolvimento humano e artificial

## 🚀 Próximas Funcionalidades

- Compartilhamento de listas
- Integração com APIs de mercados
- Modo offline
- Aplicativo móvel
- Estimativa de gastos

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Faça um push
5. Abra um Pull Request

## 📄 Licença

Distribuído sob a licença MIT. Veja `LICENSE` para mais informações.

## 👥 Autor

Desenvolvido por Ismael Pereira com auxílio de Inteligência Artificial

---
 &copy;2025 MyList. Todos os direitos reservados.

# MyList 🛒 - Gerenciador de Listas de Compras

[![Version](https://img.shields.io/badge/version-1.0-blue.svg)](https://github.com/ismapereira/MyList)
[![Status](https://img.shields.io/badge/status-stable-green.svg)](https://github.com/ismapereira/MyList)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## 📝 Descrição do Projeto

MyList é uma aplicação web moderna para gerenciamento de listas de compras, permitindo que usuários criem, organizem e acompanhem suas compras de forma simples e intuitiva.

## ✨ Recursos Principais

- 🔐 Autenticação de usuários
- 📋 Criação e gerenciamento de listas de compras
- ✅ Marcação de itens como comprados
- 📊 Dashboard com estatísticas e visão geral
- 📱 Design responsivo e moderno

## 🚀 Tecnologias Utilizadas

- **Backend:** PHP 7.4+
- **Frontend:** HTML5, Tailwind CSS, JavaScript
- **Banco de Dados:** MySQL
- **Dependências:** PDO, Feather Icons

## 🛠️ Configuração do Ambiente

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Composer (opcional, para gerenciamento de dependências)

### Instalação

1. Clone o repositório
2. Configure as credenciais de banco de dados em `config/database.php`
3. Importe o schema do banco de dados em `config/schema.sql`
4. Inicie seu servidor web

## 📦 Estrutura do Projeto

```
mylist/
├── assets/
│   ├── css/
│   └── js/
├── config/
│   ├── database.php
│   └── schema.sql
├── models/
│   ├── Usuario.php
│   └── Lista.php
├── components/
│   └── sidebar.php
├── ajax/
│   ├── adicionar_item.php
│   ├── marcar_item.php
│   └── remover_item.php
├── dashboard.php
├── login.php
└── register.php
```

## 🔄 Fluxo de Trabalho

1. **Login/Registro**: Usuários podem criar uma conta ou fazer login
2. **Dashboard**: Visualização centralizada de todas as listas e estatísticas
3. **Gerenciamento de Listas**: Criar, editar e excluir listas
4. **Gerenciamento de Itens**: Adicionar, marcar como comprado e remover itens

## 📈 Funcionalidades do Dashboard

- Visão geral de todas as listas
- Estatísticas de listas e itens
- Acesso rápido às funções mais usadas
- Interface intuitiva e responsiva

## 🤝 Contribuindo

Consulte `CONTRIBUTING.md` para saber como contribuir com o projeto.

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## Versão 1.0

### Recursos Principais
- Sistema completo de autenticação
- Gerenciamento de listas e itens
- Exportação em PDF com design profissional
- Interface responsiva e moderna

### Próximas Atualizações
- Compartilhamento de listas entre usuários
- Categorização de itens
- Histórico de compras
- Estimativa de preços
- Modo offline
- Aplicativo móvel

## Autor

Desenvolvido por Ismael Pereira

---
© 2025 MyList. Todos os direitos reservados.

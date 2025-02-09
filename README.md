# MyList 🛒 - Gerenciador de Listas de Compras

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

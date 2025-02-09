# MyList 🛒 - Gerenciador de Listas de Compras

## 📝 Descrição do Projeto

MyList é uma aplicação web moderna para gerenciamento de listas de compras, permitindo que usuários criem, organizem e acompanhem suas compras de forma simples e intuitiva.

## ✨ Recursos Principais

- 🔐 Autenticação de usuários
- 📋 Criação e gerenciamento de listas de compras
- ✅ Marcação de itens como comprados
- 📊 Estatísticas de listas e itens
- 📱 Design responsivo e moderno

## 🚀 Tecnologias Utilizadas

- **Backend:** PHP 7.4+
- **Frontend:** HTML5, Tailwind CSS
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
├── dashboard.php
├── lista.php
├── login.php
└── registro.php
```

## 🔧 Próximas Melhorias

- [ ] Implementação de AJAX para operações sem recarregamento
- [ ] Exportação de listas
- [ ] Compartilhamento de listas
- [ ] Integração com APIs de mercados

## 🤝 Contribuições

Consulte `CONTRIBUTING.md` para detalhes sobre como contribuir para o projeto.

## 📄 Licença

Este projeto está sob licença MIT. Veja `LICENSE` para mais detalhes.

## 📞 Contato

Desenvolvido com ❤️ por [Seu Nome/Organização]

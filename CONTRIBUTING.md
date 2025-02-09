# 🤝 Guia de Contribuição para MyList

## 🌟 Bem-vindo(a)!

Agradecemos seu interesse em contribuir para o MyList! Este documento fornece diretrizes para contribuição.

## 📋 Processo de Contribuição

### 1. Abra uma Issue

- Verifique se já não existe uma issue similar
- Descreva claramente o problema ou melhoria
- Use labels apropriadas

### 2. Fork e Desenvolvimento

1. Faça um fork do repositório
2. Crie uma branch para sua feature
   ```bash
   git checkout -b feature/nome-da-feature
   ```
3. Faça commits concisos e significativos
   ```bash
   git commit -m "Adiciona: descrição clara da mudança"
   ```

### 3. Padrões de Código

- Siga as PSRs do PHP
- Use indentação de 4 espaços
- Mantenha linhas com no máximo 120 caracteres
- Adicione comentários explicativos quando necessário

### 4. Testes

- Adicione testes para novas funcionalidades
- Garanta que todos os testes passem antes de submeter

### 5. Pull Request

- Descreva detalhadamente as mudanças
- Referencie issues relacionadas
- Aguarde revisão da equipe

## 🛠️ Ambiente de Desenvolvimento

### Requisitos

- PHP 7.4+
- MySQL 5.7+
- Composer
- Git

### Configuração Local

1. Clone o repositório
2. Instale dependências
   ```bash
   composer install
   ```
3. Configure o banco de dados
4. Execute testes
   ```bash
   ./vendor/bin/phpunit
   ```

## 🐛 Reportando Bugs

- Use o template de issue de bug
- Forneça passos para reproduzir
- Inclua versões de PHP, MySQL e navegador

## 📜 Código de Conduta

- Seja respeitoso
- Colabore construtivamente
- Mantenha um ambiente inclusivo

## 🏆 Reconhecimento

Contribuidores serão listados nos créditos do projeto!

## Funcionalidades do Sistema

### Gerenciamento de Usuário
- [x] Registro de usuário
- [x] Autenticação de usuário
- [x] Recuperação de senha
- [x] Página de configurações
- [x] Atualização de perfil
- [x] Alteração de preferências
- [x] **Exclusão de conta**

### Detalhes da Exclusão de Conta

#### Fluxo de Exclusão
1. Usuário navega até a seção "Excluir Conta" na página de configurações
2. Sistema solicita confirmação de senha
3. Ao confirmar, todas as informações do usuário são permanentemente removidas
4. Usuário é deslogado e redirecionado para página de login

#### Segurança
- Requer confirmação de senha atual
- Exclusão em transação de banco de dados para garantir integridade
- Remoção em cascata de todos os dados do usuário

#### Tratamento de Erros
- Validação de senha no backend
- Feedback visual de sucesso ou erro
- Prevenção de exclusão acidental

### Próximas Melhorias
- [ ] Período de carência para recuperação de conta
- [ ] Opção de exportação de dados antes da exclusão
- [ ] Log de auditoria para exclusões de conta

---

**Última atualização:** Fevereiro 2025

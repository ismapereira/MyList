# Changelog

## [0.2.0] - 2025-02-09

### Adicionado
- Página de gerenciamento de lista de compras (`lista.php`)
- Métodos no modelo `Lista` para manipulação de itens
  - `obterDetalhesLista()`
  - `buscarItens()`
  - `adicionarItemLista()`
  - `atualizarStatusItem()`
  - `removerItem()`
- Coluna `status` na tabela `itens_lista`
- Design responsivo com Tailwind CSS para página de lista

### Modificado
- Atualização da documentação (`README.md` e `CONTRIBUTING.md`)
- Ajustes no schema do banco de dados

### Removido
- Coluna `comprado` da tabela `itens_lista`

## [0.1.0] - 2025-02-01

### Adicionado
- Autenticação de usuários
- Dashboard inicial
- Modelo de usuário e lista
- Páginas de login e registro
- Configuração inicial do projeto

**Nota:** Versão inicial do projeto MyList

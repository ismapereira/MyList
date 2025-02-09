# Changelog

## [0.3.29] - 2025-02-09

### Removido
- Página "Minhas Listas" removida por redundância com o Dashboard
- Link para "Minhas Listas" removido da barra lateral

### Corrigido
- Função de criar lista no Dashboard
- Método `criarLista()` na classe Lista
- Tratamento de resposta AJAX ao criar lista

### Melhorado
- Estrutura do projeto mais enxuta e organizada
- Documentação atualizada refletindo as mudanças
- Interface mais intuitiva focada no Dashboard

## [0.3.28] - 2025-02-09

### Corrigido
- Contadores de listas na página Minhas Listas
- Cálculo de listas totais, pendentes e concluídas
- Renderização dinâmica de estatísticas de listas

### Melhorado
- Performance na recuperação de dados de listas
- Precisão das informações estatísticas

## [0.3.27] - 2025-02-09

### Corrigido
- Método de listagem de listas no JavaScript
- Compatibilidade com endpoint de listagem AJAX
- Renderização de listas na página Minhas Listas

### Melhorado
- Tratamento de erros na listagem de listas
- Feedback visual para listas vazias

## [0.3.26] - 2025-02-09

### Adicionado
- Método `editarLista()` na classe Lista para edição de listas via AJAX
- Suporte a edição de status de lista diretamente no banco de dados

### Corrigido
- Compatibilidade entre modelo e controlador AJAX
- Tratamento de dados de edição de lista

## [0.3.25] - 2025-02-09

### Adicionado
- Funcionalidade de edição de listas existentes
- Opção para marcar listas como concluídas
- Sistema de status para listas (Nova, Em Andamento, Concluída)
- Novo arquivo JavaScript para gerenciamento de listas
- Modal de edição de lista com campos para nome, descrição e status

### Melhorado
- Interface de gerenciamento de listas com novos botões de ação
- Feedback visual para diferentes status de lista
- Organização do código JavaScript em arquivo separado

## [0.3.24] - 2025-02-09

### Adicionado
- Configuração completa do Tailwind CSS
- Suporte a diretivas `@apply` no CSS personalizado
- Arquivos de configuração para build de CSS

### Corrigido
- Erro de processamento de CSS personalizado
- Configuração de estilos globais

## [0.3.23] - 2025-02-09

### Removido
- Arquivo `home.php` completamente descontinuado
- Referências obsoletas a métodos de listagem de listas

### Corrigido
- Removidas chamadas a métodos inexistentes
- Ajustada estrutura de navegação para usar dashboard como página inicial

## [0.3.22] - 2025-02-09

### Corrigido
- Inclusão do modal de criação de lista
- Funcionalidades de criação e exclusão de listas na página Minhas Listas
- Carregamento dinâmico de listas
- Tratamento de erros na página de listas

### Adicionado
- Scripts para recarregamento de listas
- Funções de toast e manipulação de listas
- Eventos de criação e exclusão de listas

### Melhorado
- Experiência do usuário na página de listas
- Consistência de funcionalidades entre dashboard e minhas listas

## [0.3.21] - 2025-02-09

### Corrigido
- Navegação inicial usando dashboard como página principal
- Remoção de página home.php redundante
- Links de navegação atualizados

### Refatorado
- Estrutura de navegação simplificada
- Sidebar atualizada para usar dashboard como início

## [0.3.20] - 2025-02-09

### Adicionado
- Página inicial (home.php)
- Página de Minhas Listas (minhas_listas.php)
- Página de Configurações (configuracoes.php)
- Componente de sidebar reutilizável
- Navegação entre páginas do sistema

### Melhorado
- Estrutura de navegação
- Consistência visual entre páginas
- Experiência do usuário na navegação

### Refatorado
- Sidebar extraída para componente separado
- Links de navegação atualizados

## [0.3.19] - 2025-02-09

### Adicionado
- Função `showToast()` para notificações de sistema
- Notificações visuais para ações do usuário
- Animações de entrada e saída para toasts

### Corrigido
- Erro de função `showToast` não definida
- Tratamento de notificações de sucesso e erro

### Melhorado
- Experiência do usuário com feedback visual
- Consistência das mensagens de sistema
- Componente de notificação reutilizável

## [0.3.18] - 2025-02-09

### Corrigido
- Recarregamento de listas após criação e exclusão
- Tratamento de erros na criação e exclusão de listas
- Eventos de recarregamento dinâmico

### Adicionado
- Logs de diagnóstico detalhados
- Tratamento de erros específicos
- Verificação de eventos de recarregamento

### Melhorado
- Experiência do usuário na manipulação de listas
- Feedback de ações do usuário
- Consistência das operações AJAX
- Melhorias no recarregamento

## [0.3.17] - 2025-02-09

### Corrigido
- Acesso à conexão do banco de dados
- Método para obter conexão na classe Lista
- Tratamento de erros em consultas AJAX

### Adicionado
- Método `getConexao()` na classe Lista
- Validação de acesso a recursos de banco de dados

## [0.3.16] - 2025-02-09

### Corrigido
- Atualização automática dos contadores
- Cálculo de listas concluídas
- Sincronização entre PHP e JavaScript

### Melhorado
- Precisão dos contadores
- Interface dos cards de estatísticas
- Ícones mais intuitivos

## [0.3.15] - 2025-02-09

### Corrigido
- Recarregamento automático de listas
- Eventos duplicados em botões
- Cache de requisições AJAX

### Melhorado
- Layout das listas em grid
- Contadores de listas e itens pendentes
- Limpeza de campos após criar lista
- Tratamento de erros e feedback visual
- Melhorias no recarregamento

## [0.3.14] - 2025-02-09

### Adicionado
- Validação de requisição AJAX
- Contagem de itens pendentes na listagem de listas
- Tratamento de erros detalhado na listagem

### Melhorado
- Performance na busca de listas
- Segurança das requisições AJAX
- Experiência do usuário na listagem de listas

### Corrigido
- Método de listagem de listas
- Detalhes de renderização de listas

## [0.3.13] - 2025-02-09

### Corrigido
- Restauração de eventos de modal e criação de lista
- Recuperação de funcionalidades removidas acidentalmente
- Consistência dos eventos de interface

### Melhorado
- Estabilidade do código JavaScript
- Recuperação de estado anterior da interface

## [0.3.12] - 2025-02-09

### Adicionado
- Rota de listagem de listas via AJAX
- Recarregamento dinâmico de listas após exclusão
- Tratamento para lista vazia com botão de criação

### Melhorado
- Consistência entre frontend e backend
- Logs de diagnóstico para depuração
- Experiência do usuário na manipulação de listas

## [0.3.11] - 2025-02-09

### Corrigido
- Método de requisição para exclusão de lista (mudado para DELETE)
- Formato de envio de dados para exclusão
- Tratamento de erros na exclusão de lista

### Melhorado
- Consistência entre frontend e backend
- Logs de diagnóstico para depuração

## [0.3.10] - 2025-02-09

### Corrigido
- Mecanismo de exclusão de lista
- Método de envio de requisição para exclusão
- Tratamento de erros na exclusão de lista

### Adicionado
- Logs de diagnóstico para depuração
- Melhoria no tratamento de erros de exclusão

## [0.3.9] - 2025-02-09

### Adicionado
- Recarregamento automático de listas após criação e exclusão
- Função de sincronização dinâmica da lista de listas
- Tratamento de lista vazia com botão de criação

### Melhorado
- Experiência do usuário na manipulação de listas
- Performance na atualização da interface

## [0.3.8] - 2025-02-09

### Adicionado
- Data de criação ao lado do nome da lista
- Funcionalidade de exclusão de lista
- Confirmação de exclusão via modal de confirmação

### Melhorado
- Experiência do usuário na visualização de listas
- Interatividade com as listas criadas

## [0.3.7] - 2025-02-09

### Removido
- Ícones "X" do modal de criação de lista
- Botão de fechamento no canto superior direito
- Ícones dos botões de ação

### Simplificado
- Design do modal de criação de lista

## [0.3.6] - 2025-02-09

### Corrigido
- Funcionalidade do botão "Cancelar" no modal de criação de lista
- Adicionado evento de fechamento correto para o modal

## [0.3.5] - 2025-02-09

### Adicionado
- Ícones nos botões do modal de criação de lista
- Ícones de confirmação e cancelamento
- Melhoria visual dos botões

### Corrigido
- Funcionalidade de cancelamento no modal
- Estilização dos botões com ícones

## [0.3.4] - 2025-02-09

### Corrigido
- Ajustes mínimos nos botões de criação de lista
- Restauração do design original do dashboard
- Correção de pequenos detalhes de interatividade

## [0.3.3] - 2025-02-09

### Corrigido
- Funcionalidade dos botões de criação de lista
- Menu sidebar responsivo
- Adicionados ícones nos botões e menu
- Corrigida navegação e interatividade do dashboard

### Adicionado
- Botão de menu no cabeçalho
- Overlay para sidebar mobile
- Transição suave para sidebar

## [0.3.2] - 2025-02-09

### Corrigido
- Restauração do design original do dashboard
- Revertido layout dos cartões de estatísticas
- Retorno ao estilo inicial de exibição

## [0.3.1] - 2025-02-09

### Corrigido
- Restauração dos ícones no dashboard
- Layout dos cartões de estatísticas
- Ícones de Feather Icons reintegrados

## [0.3.0] - 2025-02-09

### Adicionado
- Funcionalidade completa de criação de listas
  - Modal de criação de lista no dashboard
  - Métodos no modelo `Lista` para gerenciar listas
    - `criarLista()`
    - `atualizarLista()`
    - `excluirLista()`
- Script AJAX `lista_crud_ajax.php` para operações de lista
- Validação e sanitização de dados de entrada
- Feedback visual para criação de lista

### Modificado
- Layout do dashboard para suportar criação dinâmica de listas
- Melhorias na experiência do usuário

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
- Implementação de AJAX para operações sem recarregamento

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

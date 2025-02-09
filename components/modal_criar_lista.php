<!-- Modal Criar Lista -->
<div id="criarListaModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Criar Nova Lista</h2>
            <button id="fecharModal" class="text-gray-500 hover:text-gray-700">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="novaListaForm">
            <div class="mb-4">
                <label for="nomeLista" class="block text-gray-700 mb-2">Nome da Lista</label>
                <input type="text" id="nomeLista" name="nome" class="form-input w-full" 
                       placeholder="Digite o nome da lista" required>
            </div>
            <div class="mb-4">
                <label for="descricaoLista" class="block text-gray-700 mb-2">Descrição (Opcional)</label>
                <textarea id="descricaoLista" name="descricao" class="form-input w-full" 
                          placeholder="Descrição da lista"></textarea>
            </div>
            <button type="submit" class="btn-primary w-full">
                Criar Lista
            </button>
        </form>
    </div>
</div>

<script>
    // Função para alternar modal
    function toggleModal() {
        const modal = document.getElementById('criarListaModal');
        modal.classList.toggle('hidden');
    }

    // Abrir modal ao clicar em "Nova Lista"
    document.getElementById('novaListaBtn').addEventListener('click', function() {
        // Limpar campos
        document.getElementById('nomeLista').value = '';
        document.getElementById('descricaoLista').value = '';
        
        // Mostrar modal
        toggleModal();
    });

    // Fechar modal ao clicar no botão de fechar
    document.getElementById('fecharModal').addEventListener('click', toggleModal);

    // Criar lista via AJAX
    document.getElementById('novaListaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nome = document.getElementById('nomeLista').value.trim();
        const descricao = document.getElementById('descricaoLista').value.trim();

        console.log('Criando lista:', nome);  // Log de diagnóstico

        fetch('lista_crud_ajax.php?action=criar_lista', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                nome: nome,
                descricao: descricao
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Resposta do servidor:', data);  // Log de diagnóstico
            
            if (data.success) {
                // Mostrar notificação
                showToast('Lista criada com sucesso!');

                // Fechar modal
                toggleModal();

                // Recarregar listas
                recarregarListas();
            } else {
                // Mostrar erro
                console.error('Erro na criação:', data.message);  // Log de erro
                showToast(data.message || 'Erro ao criar lista', 'error');
            }
        })
        .catch(error => {
            console.error('Erro completo:', error);  // Log de erro completo
            showToast('Erro ao criar lista', 'error');
        });
    });
</script>

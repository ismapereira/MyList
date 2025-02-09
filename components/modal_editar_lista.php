<!-- Modal Editar Lista -->
<div id="editarListaModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg w-96">
        <div class="modal-header flex justify-between items-center p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Editar Lista</h2>
            <button id="fecharModalEditar" class="text-gray-500 hover:text-gray-700">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="editarListaForm" class="modal-body p-6">
            <input type="hidden" id="editarListaId" name="id">
            <div class="mb-4">
                <label for="editarNomeLista" class="block text-gray-700 mb-2">Nome da Lista</label>
                <input type="text" id="editarNomeLista" name="nome" 
                       class="form-input w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="Digite o nome da lista" required>
            </div>
            <div class="mb-6">
                <label for="editarDescricaoLista" class="block text-gray-700 mb-2">Descrição (Opcional)</label>
                <textarea id="editarDescricaoLista" name="descricao" 
                          class="form-input w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                          placeholder="Descrição da lista"></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Status</label>
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="em_andamento" class="form-radio text-blue-600" checked>
                        <span class="ml-2">Em Andamento</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="concluida" class="form-radio text-green-600">
                        <span class="ml-2">Concluída</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full flex items-center justify-center space-x-2">
                <i data-feather="save" class="w-5 h-5"></i>
                <span>Salvar Alterações</span>
            </button>
        </form>
    </div>
</div>

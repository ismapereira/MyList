<?php
// Modal de Confirmação de Exclusão
?>
<div id="modalExcluirConta" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-auto">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-red-500"></i>
                    <h3 class="text-xl font-semibold">Confirmar Exclusão</h3>
                </div>
                <p class="text-gray-600 mb-4">
                    Para confirmar a exclusão da sua conta, digite sua senha atual:
                </p>
                <form id="formExcluirConta" class="space-y-4">
                    <div>
                        <label for="senha_confirmacao" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                        <input type="password" id="senha_confirmacao" name="senha_confirmacao" 
                               class="form-input w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200" 
                               placeholder="Digite sua senha">
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" id="btnCancelarExclusao" 
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Confirmar Exclusão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Modal de exclusão de conta
const modalExcluirConta = document.getElementById('modalExcluirConta');
const btnExcluirConta = document.getElementById('btnExcluirConta');
const btnCancelarExclusao = document.getElementById('btnCancelarExclusao');

btnExcluirConta.addEventListener('click', () => {
    modalExcluirConta.classList.remove('hidden');
});

btnCancelarExclusao.addEventListener('click', () => {
    modalExcluirConta.classList.add('hidden');
    document.getElementById('formExcluirConta').reset();
});

// Excluir conta
document.getElementById('formExcluirConta').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const data = {
            senha: document.getElementById('senha_confirmacao').value
        };

        const result = await makeRequest('usuario_crud_ajax.php?action=excluir_conta', 'POST', data);
        showToast(result.message);
        modalExcluirConta.classList.add('hidden');
    } catch (error) {
        showToast(error.message, 'error');
    }
});

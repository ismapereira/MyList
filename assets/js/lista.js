document.addEventListener('DOMContentLoaded', function() {
    const formularioAdicionarItem = document.getElementById('formulario-adicionar-item');
    const listaItens = document.getElementById('lista-itens');

    // Adicionar novo item
    formularioAdicionarItem.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Validar se todos os campos necessários estão presentes
        const camposObrigatorios = ['lista_id', 'nome', 'quantidade', 'unidade'];
        for (const campo of camposObrigatorios) {
            if (!formData.get(campo)) {
                console.error(`Campo obrigatório ausente: ${campo}`);
                alert(`Por favor, preencha o campo ${campo}`);
                return;
            }
        }

        console.log('Enviando dados:', Object.fromEntries(formData));

        fetch('ajax/adicionar_item.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Resposta recebida:', data);
            if(data.sucesso) {
                // Adicionar item dinamicamente à lista
                const novoItem = document.createElement('li');
                novoItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                novoItem.innerHTML = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" data-item-id="${data.item_id}">
                        <label class="form-check-label">
                            ${formData.get('nome')} (${formData.get('quantidade')} ${formData.get('unidade')})
                        </label>
                    </div>
                    <button class="btn btn-sm btn-danger remover-item" data-item-id="${data.item_id}">
                        Remover
                    </button>
                `;
                
                listaItens.appendChild(novoItem);
                
                // Fechar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('adicionarItemModal'));
                modal.hide();
                
                // Limpar formulário
                formularioAdicionarItem.reset();
            } else {
                alert('Erro ao adicionar item: ' + data.mensagem);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao adicionar item');
        });
    });

    // Marcar item como comprado/não comprado
    listaItens.addEventListener('change', function(e) {
        if(e.target.classList.contains('form-check-input')) {
            const itemId = e.target.dataset.itemId;
            const comprado = e.target.checked;
            const label = e.target.nextElementSibling;

            fetch('ajax/marcar_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_id=${itemId}&comprado=${comprado ? 1 : 0}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    label.classList.toggle('text-muted', comprado);
                } else {
                    alert('Erro ao marcar item');
                    e.target.checked = !comprado;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao marcar item');
                e.target.checked = !comprado;
            });
        }
    });

    // Remover item
    listaItens.addEventListener('click', function(e) {
        if(e.target.classList.contains('remover-item')) {
            const itemId = e.target.dataset.itemId;
            const itemLi = e.target.closest('li');

            fetch('ajax/remover_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    itemLi.remove();
                } else {
                    alert('Erro ao remover item: ' + data.mensagem);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao remover item');
            });
        }
    });
});

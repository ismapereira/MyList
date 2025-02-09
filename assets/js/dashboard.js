document.addEventListener('DOMContentLoaded', function() {
    const novaListaForm = document.getElementById('nova-lista-form');

    novaListaForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const nomeLista = document.getElementById('nome-lista').value;
        const descricaoLista = document.getElementById('descricao-lista').value;

        // Aqui você poderia adicionar uma chamada AJAX para criar a lista
        // Por enquanto, apenas um alerta de demonstração
        alert(`Nova lista criada:\nNome: ${nomeLista}\nDescrição: ${descricaoLista}`);

        // Limpar o formulário
        novaListaForm.reset();
    });
});

/**
 * Script de gerenciamento de eventos
 * Validações e interações na página
 */

document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
    const form = document.getElementById('eventoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validaFormulario()) {
                e.preventDefault();
            }
        });
    }
    
    // Adiciona máscara de data se necessário
    const dataInputs = document.querySelectorAll('input[type="date"]');
    dataInputs.forEach(input => {
        input.addEventListener('change', validaDatas);
    });
});

/**
 * Valida o formulário antes do envio
 */
function validaFormulario() {
    const titulo = document.getElementById('titulo').value.trim();
    const descricao = document.getElementById('descricao').value.trim();
    const dataInicio = document.getElementById('data_inicio').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    
    // Valida campos obrigatórios
    if (!titulo || !descricao || !dataInicio || !horaInicio) {
        alert('Por favor, preencha todos os campos obrigatórios marcados com *');
        return false;
    }
    
    // Valida datas
    if (!validaDatas()) {
        return false;
    }
    
    // Valida arquivo de imagem se existir
    const imagemInput = document.getElementById('imagem');
    if (imagemInput.files.length > 0) {
        const file = imagemInput.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!validTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido. Apenas JPG, PNG e GIF são aceitos.');
            return false;
        }
        
        if (file.size > maxSize) {
            alert('O arquivo é muito grande. O tamanho máximo permitido é 2MB.');
            return false;
        }
    }
    
    return true;
}

/**
 * Valida as datas do formulário
 */
function validaDatas() {
    const dataInicio = document.getElementById('data_inicio').value;
    const dataFim = document.getElementById('data_fim').value;
    
    if (!dataInicio) return true;
    
    // Valida formato da data de início
    if (!isValidDate(dataInicio)) {
        alert('Data de início inválida.');
        return false;
    }
    
    // Se existir data fim, valida
    if (dataFim) {
        if (!isValidDate(dataFim)) {
            alert('Data de término inválida.');
            return false;
        }
        
        // Verifica se data fim é posterior à data início
        const inicio = new Date(dataInicio);
        const fim = new Date(dataFim);
        
        if (fim < inicio) {
            alert('A data de término não pode ser anterior à data de início.');
            return false;
        }
    }
    
    return true;
}

/**
 * Verifica se uma string é uma data válida
 */
function isValidDate(dateString) {
    const regEx = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateString.match(regEx)) return false;
    const d = new Date(dateString);
    return d instanceof Date && !isNaN(d);
}

/**
 * Mostra preview da imagem selecionada
 */
document.getElementById('imagem')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(event) {
        const preview = document.querySelector('.current-image') || 
                        document.createElement('div');
        preview.className = 'current-image';
        
        const img = document.createElement('img');
        img.src = event.target.result;
        img.className = 'evento-imagem';
        img.alt = 'Preview da imagem';
        
        preview.innerHTML = '';
        preview.appendChild(img);
        preview.appendChild(document.createElement('p')).textContent = 'Nova imagem selecionada';
        
        if (!document.querySelector('.current-image')) {
            e.target.parentNode.insertBefore(preview, e.target.nextSibling);
        } else {
            document.querySelector('.current-image img').src = event.target.result;
            document.querySelector('.current-image p').textContent = 'Nova imagem selecionada';
        }
    };
    reader.readAsDataURL(file);
});
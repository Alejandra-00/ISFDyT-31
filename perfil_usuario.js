// Muestra el campo editable y oculta el valor de solo lectura.
function editarCampo(campoId) {
    const fila = document.getElementById('editar_' + campoId).closest('form');
    document.getElementById('ver_' + campoId).style.display = 'none';
    const campo = document.getElementById('editar_' + campoId);
    campo.style.display = 'block';
    document.getElementById('acciones_' + campoId).style.display = 'flex';
    fila.querySelector('.editar-btn').style.display = 'none';
    campo.focus();
}

function guardarCampo(campoId) {
    const campo = document.getElementById('editar_' + campoId);
    const valor = campoId === 'contrasena'
        ? '********'
        : campo.options ? campo.options[campo.selectedIndex].text : campo.value;

    document.getElementById('ver_' + campoId).textContent = valor;
    cancelarEdicion(campoId);
}

function cancelarEdicion(campoId) {
    const fila = document.getElementById('editar_' + campoId).closest('form');
    document.getElementById('ver_' + campoId).style.display = 'block';
    document.getElementById('editar_' + campoId).style.display = 'none';
    document.getElementById('acciones_' + campoId).style.display = 'none';
    fila.querySelector('.editar-btn').style.display = 'inline-block';
}

        // Función para agregar una nueva categoría
        function agregarCategoria() {
            const container = document.getElementById('categorias-container');
            const nuevoId = container.children.length;
            
            const nuevoField = document.createElement('div');
            nuevoField.className = 'categoria-field';
            nuevoField.innerHTML = `
                <input type="text" 
                       name="categorias[]" 
                       class="input-categoria"
                       placeholder="Nueva categoría">
                <button type="button" class="btn-eliminar-categoria" onclick="eliminarCategoria(this)">🗑️ Eliminar</button>
            `;
            
            container.appendChild(nuevoField);
        }

        // Función para eliminar una categoría
        function eliminarCategoria(btn) {
            btn.parentElement.remove();
        }
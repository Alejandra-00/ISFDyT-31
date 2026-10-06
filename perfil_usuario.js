document.addEventListener('DOMContentLoaded', () => {
    // Si no pasamos id, la API usará la sesión iniciada en el servidor
    // Obtener el ID del input oculto presente en nav.php
    const idusuario = document.getElementById('usuario')?.value;
    obtenerDatosUsuario(idusuario);
});

function obtenerDatosUsuario(idusuario) {
    fetch('API.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'usuarios', consulta: 'usuario', id_usuario: idusuario })
    })
    .then(respuesta => respuesta.json())
    .then(respuestaApi => {
        console.log("JSON tal cual viene de API.php:", respuestaApi);

        // Si la API devuelve un Array [ { ... } ], tomamos el primer elemento [0]
        const datos = Array.isArray(respuestaApi) ? respuestaApi[0] : respuestaApi;

        if (datos && !datos.error) {
            // Rellenar Nombre Completo
            asignarValor('vernombrecompleto', 'editar_nombre_completo', datos.nombre_completo);

            // Rellenar Número de Documento (DNI)
            asignarValor('ver_numerodocumento', 'editar_numero_documento', datos.DNI);

            // Rellenar Email
            asignarValor('veremail', 'editar_email', datos.email);

            // Rellenar Carrera
            const elTextoCarrera = document.getElementById('veridcarrera');
            const selectCarrera = document.getElementById('editar_id_carrera');
            if (elTextoCarrera) elTextoCarrera.textContent = datos.nombre_carrera ?? datos.id_carrera ?? '-';
            if (selectCarrera && datos.id_carrera) selectCarrera.value = datos.id_carrera;
            
            

            // Rellenar Teléfono
            asignarValor('vertelefono', 'editar_telefono', datos.telefono);

            // Rellenar Tipo de Socio
            asignarValor('vertiposocio', 'editar_tiposocio', datos.nombre_socio);
        } else {
            console.warn("No se encontraron datos de usuario en la respuesta.");
        }
    })
    .catch(error => console.error("Error al procesar el JSON recibido:", error));
}

// Transformar IDs a Texto durante la lectura inicial
function transformarIdATexto(selectId, valorId, mapaFallback) {
    const select = document.getElementById(selectId);
    if (select) {
        select.value = valorId;
        const opcion = Array.from(select.options).find(opt => opt.value == valorId);
        if (opcion) return opcion.text;
    }
    return mapaFallback[valorId] || valorId || '-';
}

// Función auxiliar para rellenar los elementos <p> e <input>
function asignarValor(idVer, idEditar, valor) {
    const elVer = document.getElementById(idVer);
    const elEditar = document.getElementById(idEditar);
    const val = valor ?? '';

    if (elVer) elVer.textContent = val;
    if (elEditar) elEditar.value = val;
}


// Mapeo de texto a ID en caso de que los SELECTs tarden en cargar
const mapaSocio = { "1": "Socio Activo", "2": "Socio Adherente", "3": "No Socio" };
const mapaCarrera = { "1": "Análisis de Sistemas", "2": "Hig. y Seg. Laboral", "3": "Gestión Ambiental" };

// 1. Mostrar input/select y manejar casos especiales (DNI, Contraseña)
function editarCampo(campo) {
    // Bloquear edición de DNI
    if (campo === 'dni' || campo === 'numero_documento') {
        alert('El DNI no se puede modificar.');
        return;
    }

    const form = document.querySelector(`input[name="campo"][value="${campo}"]`)?.closest('form');
    if (!form) return;

    const input = form.querySelector('.campo-edicion');
    const texto = form.querySelector('.informacion p');

    // Si es contraseña, mostrar el texto en claro durante la edición
    if (input.type === 'password' || campo === 'contrasena') {
        input.type = 'text';
        input.value = ''; // Se inicia limpio para ingresar la nueva clave
        input.placeholder = 'Ingresa la nueva contraseña';
    }

    // Ocultar vista y mostrar edición
    texto.style.display = 'none';
    form.querySelector('.editar-btn').style.display = 'none';
    form.classList.add('edicion');

    input.style.display = 'block';
    form.querySelector('.acciones-edicion').style.display = 'flex';
}

// 2. Cancelar la edición y restaurar la vista original
function cancelarEdicion(campo) {
    const form = document.querySelector(`input[name="campo"][value="${campo}"]`)?.closest('form');
    if (!form) return;

    const input = form.querySelector('.campo-edicion');
    const texto = form.querySelector('.informacion p');

    // Restaurar el campo contraseña a masked type="password"
    if (campo === 'contrasena') {
        input.type = 'password';
        input.value = '';
        texto.textContent = '********';
    } else if (input.tagName === 'SELECT') {
        // Restaurar selección previa en el select según el texto mostrado
        const opcionPrevia = Array.from(input.options).find(opt => opt.text.trim() === texto.textContent.trim());
        if (opcionPrevia) input.value = opcionPrevia.value;
    } else {
        input.value = texto.textContent.trim();
    }

    // Restaurar visibilidad
    texto.style.display = 'block';
    input.style.display = 'none';
    form.classList.remove('edicion');
    form.querySelector('.editar-btn').style.display = 'inline-block';
    form.querySelector('.acciones-edicion').style.display = 'none';
}

// 3. Guardar el cambio y actualizar la vista con nombres legibles
function guardarCampo(campo) {
    // Recopilar datos actuales del DOM
    const idusuario = document.getElementById('usuario')?.value || '1'; // <-- AQUÍ
    if (campo === 'dni' || campo === 'numero_documento') return;

    const form = document.querySelector(`input[name="campo"][value="${campo}"]`)?.closest('form');
    if (!form) return;

    const input = form.querySelector('.campo-edicion');
    const valorNuevo = input.value.trim();

    if (!valorNuevo && campo !== 'contrasena') {
        alert('El campo no puede estar vacío.');
        return;
    }

    // Recopilar datos actuales del DOM
    const idUsuario = document.getElementById('usuario')?.value || '1';
    const dniActual = document.getElementById('editar_numero_documento')?.value || document.getElementById('ver_numerodocumento')?.textContent.trim();
    const nombreActual = document.getElementById('editar_nombre_completo')?.value || document.getElementById('vernombrecompleto')?.textContent.trim();
    const emailActual = document.getElementById('editar_email')?.value || document.getElementById('veremail')?.textContent.trim();
    const telefonoActual = document.getElementById('editar_telefono')?.value || document.getElementById('vertelefono')?.textContent.trim();
    const socioActual = document.getElementById('editar_tipo_socio')?.value || '1';
    const carreraActual = document.getElementById('editar_id_carrera')?.value || '1';

    // Construir payload completo para case "Update"
    const payload = {
        recurso: 'usuarios',
        consulta: 'Update',
        id: idUsuario,
        dni: dniActual,
        nombre_completo: nombreActual,
        email: emailActual,
        telefono: telefonoActual,
        contrasena: '',
        socio: socioActual,
        carrera: carreraActual
    };

    // Sobrescribir solo el campo editado
    if (campo === 'nombre_completo') payload.nombre_completo = valorNuevo;
    if (campo === 'email') payload.email = valorNuevo;
    if (campo === 'telefono') payload.telefono = valorNuevo;
    if (campo === 'contrasena') payload.contrasena = valorNuevo;
    if (campo === 'tipo_socio' || campo === 'socio') payload.socio = valorNuevo;
    if (campo === 'id_carrera' || campo === 'carrera') payload.carrera = valorNuevo;

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(respuesta => respuesta.json())
    .then(respuestaData => {
        if (respuestaData && !respuestaData.error) {
            const textoElem = form.querySelector('.informacion p');

            // 1. Manejo para SELECTS (Carrera / Tipo de Socio): Mostrar el texto en lugar del ID
            if (input.tagName === 'SELECT') {
                const opcionSeleccionada = input.options[input.selectedIndex];
                textoElem.textContent = opcionSeleccionada ? opcionSeleccionada.text : valorNuevo;
            } 
            // 2. Manejo para Contraseña: Mover a asteriscos
            else if (campo === 'contrasena') {
                textoElem.textContent = '********';
                input.type = 'password';
                input.value = '';
            } 
            // 3. Resto de inputs de texto
            else {
                textoElem.textContent = valorNuevo;
            }

            cancelarEdicion(campo);
        } else {
            alert('Error al actualizar: ' + (respuestaData?.error || 'No se pudo guardar los cambios.'));
        }
    })
    .catch(error => {
        console.error('Error al guardar:', error);
        alert('Ocurrió un error al intentar guardar.');
    });
}
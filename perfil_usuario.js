// Variable global para almacenar las carreras obtenidas de la API
let carrerasCache = [];
let datosUsuarioCache = null;

document.addEventListener('DOMContentLoaded', () => {
    const idusuario = document.getElementById('usuario')?.value;
    obtenerDatosUsuario(idusuario);
    cargarCarrerasEnSelect();
});

function obtenerDatosUsuario(idusuario) {
    const mensaje = document.getElementById('mensaje');
    fetch('API.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'usuarios', consulta: 'usuario', id_usuario: idusuario })
    })
    .then(respuesta => respuesta.json())
    .then(respuestaApi => {
        const datos = Array.isArray(respuestaApi) ? respuestaApi[0] : respuestaApi;

        if (datos && !datos.error) {
            datosUsuarioCache = datos;

            const esVoluntario = (parseInt(datos.id_socio) === 2 || datos.nombre_carrera === 'Sin carrera' || datos.tipo_usuario === 'voluntario');
            const btnEditarCarrera = document.querySelector('form input[value="id_carrera"]')?.closest('form')?.querySelector('.editar-btn');

            if (btnEditarCarrera) {
                if (esVoluntario) {
                    btnEditarCarrera.disabled = true;
                    btnEditarCarrera.style.opacity = '0.5';
                    btnEditarCarrera.style.cursor = 'not-allowed';
                    btnEditarCarrera.title = 'Los voluntarios no pueden modificar la carrera';
                } else {
                    btnEditarCarrera.disabled = false;
                    btnEditarCarrera.style.opacity = '1';
                    btnEditarCarrera.style.cursor = 'pointer';
                }
            }

            // Bloquear botones de edición de DNI y Tipo de socio si existen en el HTML
            ['dni', 'numero_documento', 'tipo_socio', 'socio', 'id_socio'].forEach(campoBloqueado => {
                const btnBloqueado = document.querySelector(`form input[value="${campoBloqueado}"]`)?.closest('form')?.querySelector('.editar-btn');
                if (btnBloqueado) {
                    btnBloqueado.disabled = true;
                    btnBloqueado.style.display = 'none';
                }
            });

            asignarValor('vernombrecompleto', 'editar_nombre_completo', datos.nombre_completo);
            asignarValor('ver_numerodocumento', 'editar_numero_documento', datos.DNI);
            asignarValor('veremail', 'editar_email', datos.email);
            asignarValor('vertelefono', 'editar_telefono', datos.telefono);
            asignarValor('vertiposocio', 'editar_tiposocio', datos.nombre_socio);

            const elEditarSocio = document.getElementById('editar_tiposocio');
            if (elEditarSocio && datos.id_socio) {
                elEditarSocio.value = datos.id_socio;
            }

            const elTextoCarrera = document.getElementById('veridcarrera');
            if (elTextoCarrera) elTextoCarrera.textContent = datos.nombre_carrera ?? datos.id_carrera ?? '-';

            if (datos.id_carrera) {
                const nombreCarrera = datos.nombre_carrera ?? 'Seleccione una carrera';
                seleccionarCarrera(datos.id_carrera, nombreCarrera);
            } else {
                const hiddenCarrera = document.getElementById('editar_id_carrera');
                if (hiddenCarrera) hiddenCarrera.value = '';
            }
        } else {
            if (mensaje) mensaje.innerText = "No se encontraron datos de usuario en la respuesta.";
        }
    })
    .catch(error => {
        if (mensaje) mensaje.innerText = "Error, usuario no encontrado.";
        mensaje.style.display = "block";
        console.error(error);
    });
}

// Función auxiliar para rellenar elementos
function asignarValor(idVer, idEditar, valor) {
    const elVer = document.getElementById(idVer);
    const elEditar = document.getElementById(idEditar);
    const val = valor ?? '';

    if (elVer) elVer.textContent = val;
    if (elEditar) elEditar.value = val;
}

// 1. Mostrar campos en modo edición
function editarCampo(campo) {
    if (campo === 'dni' || campo === 'numero_documento') {
        alert('El DNI no se puede modificar.');
        return;
    }

    if (campo === 'tipo_socio' || campo === 'socio' || campo === 'id_socio') {
        alert('El tipo de socio no se puede modificar desde el perfil.');
        return;
    }

    const form = document.querySelector(`input[name="campo"][value="${campo}"]`)?.closest('form');
    if (!form) return;

    const input = form.querySelector('.campo-edicion');
    const texto = form.querySelector('.informacion p');

    if (input && (input.type === 'password' || campo === 'contrasena')) {
        input.type = 'text';
        input.value = '';
        input.placeholder = 'Ingresa la nueva contraseña';
    }

    texto.style.display = 'none';
    form.querySelector('.editar-btn').style.display = 'none';
    form.classList.add('edicion');

    if (campo === 'id_carrera') {
        const dropdown = document.getElementById('dropdown_carrera');
        if (dropdown) dropdown.style.display = 'block';
    } else if (input) {
        input.style.display = 'block';
    }

    const acciones = form.querySelector('.acciones-edicion');
    if (acciones) acciones.style.display = 'flex';
}

// 2. Cancelar la edición
function cancelarEdicion(campo) {
    const form = document.querySelector(`input[name="campo"][value="${campo}"]`)?.closest('form');
    if (!form) return;

    const input = form.querySelector('.campo-edicion');
    const texto = form.querySelector('.informacion p');

    if (campo === 'contrasena') {
        input.type = 'password';
        input.value = '';
        texto.textContent = '********';
    } else if (campo === 'id_carrera') {
        const dropdown = document.getElementById('dropdown_carrera');
        if (dropdown) dropdown.style.display = 'none';
        
        const textoActual = texto.textContent.trim();
        const carreraEncontrada = carrerasCache.find(c => c.nombre === textoActual);
        if (carreraEncontrada) {
            seleccionarCarrera(carreraEncontrada.id, carreraEncontrada.nombre);
        }
    } else if (input) {
        input.value = texto.textContent.trim();
        input.style.display = 'none';
    }

    texto.style.display = 'block';
    if (input) input.style.display = 'none';
    form.classList.remove('edicion');
    form.querySelector('.editar-btn').style.display = 'inline-block';
    form.querySelector('.acciones-edicion').style.display = 'none';
}

// 3. Guardar el cambio y actualizar la vista con nombres legibles
function guardarCampo(campo) {
    const idUsuario = document.getElementById('usuario')?.value || '1';
    
    if (campo === 'dni' || campo === 'numero_documento' || campo === 'tipo_socio' || campo === 'socio' || campo === 'id_socio') return;

    const form = document.querySelector(`input[name="campo"][value="${campo}"]`)?.closest('form');
    if (!form) return;

    const mensaje = document.getElementById('mensaje');
    if (mensaje) mensaje.style.display = 'none';

    // Obtener el valor según el tipo de campo (si es carrera, lee el input hidden; si es texto, lee el input visible)
    let valorNuevo = '';
    let input = null;

    if (campo === 'id_carrera' || campo === 'carrera') {
        input = document.getElementById('editar_id_carrera');
        valorNuevo = input ? input.value.trim() : '';
    } else {
        input = form.querySelector('.campo-edicion');
        valorNuevo = input ? input.value.trim() : '';
    }

    if (!valorNuevo) {
        alert('El campo no puede estar vacío.');
        return;
    }

    // ==========================================
    // VALIDACIONES ESPECÍFICAS POR CAMPO
    // ==========================================

    if (campo === 'email') {
        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexEmail.test(valorNuevo)) {
            if (mensaje) {
                mensaje.innerText = 'Debes colocar un e-mail válido para guardar los cambios.';
                mensaje.style.display = "block";
            }
            return;
        }
    }

    if (campo === 'telefono') {
        const regexTelefono = /^[0-9+\-\s()]{6,20}$/;
        const cantidadNumeros = (valorNuevo.match(/[0-9]/g) || []).length;
        if (!regexTelefono.test(valorNuevo) || cantidadNumeros < 6) {
            if (mensaje) {
                mensaje.innerText = 'El teléfono no puede contener letras y debe tener al menos 6 números.';
                mensaje.style.display = "block";
            }
            return;
        }
    }

    if (campo === 'contrasena') {
        if (valorNuevo.length < 6 || valorNuevo.length > 8) {
            if (mensaje) {
                mensaje.innerText = 'La contraseña debe tener entre 6 y 8 caracteres.';
                mensaje.style.display = "block";
            }
            return;
        }
    }

    // Recopilar datos actuales conservando los IDs reales de socio y carrera
    const dniActual = datosUsuarioCache?.DNI || document.getElementById('editar_numero_documento')?.value || document.getElementById('ver_numerodocumento')?.textContent.trim();
    const nombreActual = datosUsuarioCache?.nombre_completo || document.getElementById('editar_nombre_completo')?.value || document.getElementById('vernombrecompleto')?.textContent.trim();
    const emailActual = datosUsuarioCache?.email || document.getElementById('editar_email')?.value || document.getElementById('veremail')?.textContent.trim();
    const telefonoActual = datosUsuarioCache?.telefono || document.getElementById('editar_telefono')?.value || document.getElementById('vertelefono')?.textContent.trim();
    const socioActual = datosUsuarioCache?.id_socio || 1;
    const carreraActual = datosUsuarioCache?.id_carrera || null;

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

            if (datosUsuarioCache) {
                if (campo === 'nombre_completo') datosUsuarioCache.nombre_completo = valorNuevo;
                if (campo === 'email') datosUsuarioCache.email = valorNuevo;
                if (campo === 'telefono') datosUsuarioCache.telefono = valorNuevo;
                if (campo === 'id_carrera' || campo === 'carrera') datosUsuarioCache.id_carrera = valorNuevo;
            }

            // 1. Manejo para el Desplegable Personalizado de Carrera
            if (campo === 'id_carrera' || campo === 'carrera') {
                const nombreSeleccionado = document.getElementById('dropdown_selected_text')?.textContent;
                textoElem.textContent = nombreSeleccionado || valorNuevo;
                const dropdown = document.getElementById('dropdown_carrera');
                if (dropdown) dropdown.style.display = 'none';
            } 

            // 2. Manejo para Contraseña: Mover a asteriscos
            else if (campo === 'contrasena') {
                textoElem.textContent = '********';
                if (input) {
                    input.type = 'password';
                    input.value = '';
                }
            } 
            // 3. Resto de inputs de texto
            else {
                textoElem.textContent = valorNuevo;
            }

            cancelarEdicion(campo);
        } else {
            if (mensaje) {
                mensaje.innerText = respuestaData?.error || 'Error al actualizar.';
                mensaje.style.display = "block";
            }
            console.error(respuestaData?.error);
        }
    })
    .catch(error => {
        console.error(error);
        if (mensaje) {
            mensaje.innerText = 'Ocurrió un error al intentar guardar.';
            mensaje.style.display = "block";
        }
    });
}

// Cargar carreras en el desplegable personalizado
function cargarCarrerasEnSelect() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'carreras', consulta: 'Read' })
    })
    .then(res => res.json())
    .then(carreras => {
        carrerasCache = carreras;
        const contenedorOpciones = document.getElementById('dropdown_options_carrera');
        if (!contenedorOpciones) return;

        contenedorOpciones.innerHTML = '';

        if (Array.isArray(carreras) && carreras.length > 0) {
            carreras.forEach(carrera => {
                const div = document.createElement('div');
                div.className = 'dropdown-option';
                div.dataset.value = carrera.id;
                div.textContent = carrera.nombre;
                
                div.addEventListener('click', () => {
                    seleccionarCarrera(carrera.id, carrera.nombre);
                });

                contenedorOpciones.appendChild(div);
            });
        } else {
            contenedorOpciones.innerHTML = '<div class="dropdown-option">No hay carreras disponibles</div>';
        }
    })
    .catch(error => {
        console.error("Error al cargar carreras:", error);
        const mensaje = document.getElementById('mensaje');
        if (mensaje) {
            mensaje.innerText = 'Error al cargar las carreras.';
            mensaje.style.display = "block";
        }
    });
}

// Lógica de apertura/cierre del desplegable personalizado
document.addEventListener('click', (e) => {
    const dropdown = document.getElementById('dropdown_carrera');
    const trigger = document.getElementById('dropdown_trigger_carrera');

    if (trigger && trigger.contains(e.target)) {
        dropdown.classList.toggle('open');
    } else if (dropdown && !dropdown.contains(e.target)) {
        dropdown.classList.remove('open');
    }
});

// Selección de opción dentro del dropdown
function seleccionarCarrera(id, nombre) {
    const hiddenInput = document.getElementById('editar_id_carrera');
    const selectedText = document.getElementById('dropdown_selected_text');
    const dropdown = document.getElementById('dropdown_carrera');

    if (hiddenInput) hiddenInput.value = id;
    if (selectedText) selectedText.textContent = nombre;

    document.querySelectorAll('#dropdown_options_carrera .dropdown-option').forEach(opt => {
        opt.classList.toggle('selected', opt.dataset.value == id);
    });

    if (dropdown) dropdown.classList.remove('open');
}
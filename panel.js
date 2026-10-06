function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function mostrar(id) {
    const formulario = document.getElementById(id);
    const inicio = document.getElementById('inicio');

    if (id === 'inicio') {
        document.querySelectorAll('.formulario')
            .forEach(f => f.classList.remove('activo'));
        inicio.classList.add('activo');
        return;
    }

    if (formulario.classList.contains('activo')) {
        formulario.classList.remove('activo');
        inicio.classList.add('activo');
        return;
    }
    document.querySelectorAll('.formulario')
        .forEach(f => f.classList.remove('activo'));
    formulario.classList.add('activo');
}

document.addEventListener('DOMContentLoaded', () => {
    cargarInicio();
    cargarVoluntarios();
    cargarAlumnos();
    cargarMonto();
    cargarPagos();
});

// Inicio del panel
function cargarInicio() {
    Promise.all([
        // obtener usuarios
        fetch('API.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json'},
            body: JSON.stringify({ recurso: 'usuarios', consulta: 'Read'})
        }).then(response => response.json()),

        // obtener carreras
        fetch('API.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json'},
            body: JSON.stringify({ recurso: 'carreras', consulta: 'Read'})
        }).then(response => response.json()),

        // obtener montos
        fetch('API.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json'},
            body: JSON.stringify({ recurso: 'monto', consulta: 'Read'})
        }).then(response => response.json()) ])
        .then(([usuarios, carreras, montos]) => {
            // cuenta los socios alumnos y voluntarios
            let alumnos = 0; 
            let voluntarios = 0;

            const listaAlumnos = [];
            const listaVoluntarios = [];

        usuarios.forEach(usuario => {
            if (usuario.nombre_socio === 'Alumno') { 
                alumnos++; 
                listaAlumnos.push(usuario);
            }
            if (usuario.nombre_socio === 'Voluntario') { 
                voluntarios++; 
                listaVoluntarios.push(usuario);
            }
        });
        document.getElementById('cantAlumnos').textContent = alumnos;
        document.getElementById('cantVoluntarios').textContent = voluntarios;
        document.getElementById('cantCarreras').textContent = carreras.length;

        if (montos.length > 0) { // devuelve cuantos elementos hay
            const ultimoMonto = montos[montos.length - 1]; //obtiene el ultimo monto
            document.getElementById('montoActual').textContent = '$' + ultimoMonto.importe;
        }

        //obtiene los ultimos 5 alumnos y volunatarios registrados
        const ultimosAlumnos = listaAlumnos
            .sort((a, b) => b.id - a.id)
            .slice(0, 5);

        const ultimosVoluntarios = listaVoluntarios
            .sort((a, b) => b.id - a.id)
            .slice(0, 5);
            
        // Renderizar en HTML
        const contAlumnos = document.getElementById('socAlumno');
        if (contAlumnos) {
            contAlumnos.innerHTML = '';
            if (ultimosAlumnos.length === 0) {
                contAlumnos.innerHTML = '<div class="listado-item"><span class="item-email">No hay alumnos registrados</span></div>';
            } else {
                ultimosAlumnos.forEach(alumno => {
                    const item = document.createElement('div');
                    item.className = 'listado-item';
                    const dni = alumno.DNI ?? '';
                    item.innerHTML = `
                        <strong>${escapeHtml(alumno.nombre_completo)}</strong>
                        <span class="item-dato">DNI: ${escapeHtml(dni)}</span>
                    `;
                    contAlumnos.appendChild(item);
                });
            }
        }

        const contVoluntarios = document.getElementById('socVoluntario');
        if (contVoluntarios) {
            contVoluntarios.innerHTML = '';
            if (ultimosVoluntarios.length === 0) {
                contVoluntarios.innerHTML = '<div class="listado-item"><span class="item-email">No hay voluntarios registrados</span></div>';
            } else {
                ultimosVoluntarios.forEach(voluntario => {
                    const item = document.createElement('div');
                    item.className = 'listado-item';
                    const dni = voluntario.DNI ?? '';
                    item.innerHTML = `
                        <strong>${escapeHtml(voluntario.nombre_completo)}</strong>
                        <span class="item-dato">DNI: ${escapeHtml(dni)}</span>
                    `;
                    contVoluntarios.appendChild(item);
                });
            }
        }
    })
    .catch(error => {
        console.error('Error al cargar el inicio:', error); 
    });
}

// socio alumnos
function cargarAlumnos() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'usuarios',
            consulta: 'socios',
            id_socio: 1  
        })
    })
    .then(response => response.json())
    .then(data => { 
        const tbody = document.getElementById('tablasocios-Alumno');
        tbody.innerHTML = '';
        data.forEach(usuarios => {
            const dni = usuarios.DNI ?? '';
            const nombre = usuarios.nombre_completo ?? '';

            const fila = document.createElement('tr');

            // Celda DNI 
            const tdDni = document.createElement('td');
            const contDni = document.createElement('div');
            contDni.className = 'dni-con-boton';

            const spanDni = document.createElement('span');
            spanDni.textContent = escapeHtml(dni);

            const btnEditar = document.createElement('button');
            btnEditar.className = 'btn-editar-dni';
            btnEditar.innerHTML = '<img src="iconos/dni.png" alt="Editar">';
            btnEditar.onclick = () => editarDNI(dni);

            contDni.appendChild(btnEditar);
            contDni.appendChild(spanDni);
            tdDni.appendChild(contDni);

            // Celda Nombre
            const tdNombre = document.createElement('td');
            tdNombre.textContent = escapeHtml(nombre);

            // Celda Inactivo 
            const tdBoton = document.createElement('td');
            const btnInactivo = document.createElement('button');
            btnInactivo.className = 'btn-inactivo';

            // Según el estado, mostramos una imagen u otra
            const activo = usuarios.activo == 1; // 1 = activo, 0 = inactivo
            btnInactivo.innerHTML = activo
                ? '<img src="iconos/inactivo.png" alt="Inactivar">'
                : '<img src="iconos/activo.png" alt="Activar">';

            // Al clickear, alternamos el estado
            btnInactivo.onclick = () => cambiarEstadoActivo(usuarios.id, activo);
            tdBoton.appendChild(btnInactivo);

            fila.appendChild(tdDni);
            fila.appendChild(tdNombre);
            fila.appendChild(tdBoton);
            tbody.appendChild(fila);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        const tbody = document.getElementById('tablasocios-Alumno');
        tbody.innerHTML = '<tr><td colspan="3">Error al cargar los socios.</td></tr>';
    });
}

// socio voluntarios
function cargarVoluntarios() {
   fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'usuarios',
            consulta: 'socios',
            id_socio: 2
        })
    })
    .then(response => response.json())
    .then(data => { 
        const tbody = document.getElementById('tablasocios-Voluntario');
        tbody.innerHTML = '';
        data.forEach(usuarios => {
            const dni = usuarios.DNI ?? ''; //llama el dni, si no tiene mostramos vacío
            const nombre = usuarios.nombre_completo ?? ''; // llama el nombre, si no tiene nombre, mostramos vacío

            const fila = document.createElement('tr');

            // Celda DNI 
            const tdDni = document.createElement('td');
            const contDni = document.createElement('div');
            contDni.className = 'dni-con-boton';

            const spanDni = document.createElement('span'); // span para mostrar el DNI
            spanDni.textContent = escapeHtml(dni);

            const btnEditar = document.createElement('button');
            btnEditar.className = 'btn-editar-dni';
            btnEditar.innerHTML = '<img src="iconos/dni.png" alt="Editar">';
            btnEditar.onclick = () => editarDNI(dni);

            contDni.appendChild(btnEditar);
            contDni.appendChild(spanDni);
            tdDni.appendChild(contDni);

            // Celda Nombre
            const tdNombre = document.createElement('td');
            tdNombre.textContent = escapeHtml(nombre);

            // Celda Inactivo 
            const tdBoton = document.createElement('td');
            const btnInactivo = document.createElement('button');
            btnInactivo.className = 'btn-inactivo';

            // Según el estado, mostramos una imagen u otra
            const activo = usuarios.activo == 1;  
            btnInactivo.className =  'btn-inactivo'; 
            btnInactivo.innerHTML = activo
                ? '<img src="iconos/inactivo.png" alt="Inactivar">' 
                : '<img src="iconos/activo.png" alt="Activar">';

            // Al clickear, alternamos el estado
            btnInactivo.onclick = () => cambiarEstadoActivo(usuarios.id, activo);
            tdBoton.appendChild(btnInactivo); 

            fila.appendChild(tdDni);
            fila.appendChild(tdNombre);
            fila.appendChild(tdBoton);
            tbody.appendChild(fila);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        const tbody = document.getElementById('tablasocios-Voluntario');
        tbody.innerHTML = '<tr><td colspan="3">Error al cargar los socios.</td></tr>';
    });
}

function cambiarEstadoActivo(idUsuario, activo) {
    const consulta = activo ? 'Inactivate' : 'Activate'; 

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'usuarios',
            consulta: consulta,
            id: idUsuario
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error:', data.error);
            return;
        }
        // Refrescar según el estado de la barra de búsqueda
        refrescarAlumnos();
        refrescarVoluntarios();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Refresca alumnos: si hay búsqueda activa, la repite; si no, carga todos
function refrescarAlumnos() {
    const busqueda = document.getElementById("barraBusquedaAlumnos").value.trim(); 
    if (busqueda === "") {
        cargarAlumnos();
    } else {
        buscarUsuariosAlumnos();
    }
}

// Ídem para voluntarios
function refrescarVoluntarios() {
    const busqueda = document.getElementById("barraBusquedaVoluntarios").value.trim();
    if (busqueda === "") {
        cargarVoluntarios();
    } else {
        buscarUsuariosVoluntarios();
    }
}

let usuarioEditando = null;   //declaramos la variable global para almacenar el id del usuario que se está editando
let seccionAnterior = null;   //declaramos la variable global para almacenar la sección anterior antes de editar el DNI

function editarDNI(dni) {
     // Limpiar mensaje anterior
    document.getElementById('mensajeEditarDni').textContent = '';

    // Guardar de dónde venimos (alumno o voluntario)
    seccionAnterior = document.querySelector('.formulario.activo').id;

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'usuarios',
            consulta: 'ReadByDNI',
            dni: dni
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error:', data.error);
            return;
        }
        // Guardamos el id del usuario que vamos a editar
        usuarioEditando = data.id;

        // Prellenamos el input con el DNI actual
        document.getElementById('nuevoDni').value = data.DNI || '';

        // Mostramos el formulario de edición
        mostrar('editarDni');
    })
    .catch(error => {
        console.error('Error:', error);
    });
}



function guardarDni() {
    const nuevoDni = document.getElementById('nuevoDni').value.trim();

    if (!usuarioEditando) {
        alert('No hay ningún usuario seleccionado para editar.');
        return;
    }
    if (!nuevoDni) { 
        alert('Ingresá un DNI.');
        return;
    }

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            recurso: 'usuarios',
            consulta: 'UpdateDNI',
            id: usuarioEditando,
            dni: nuevoDni
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('mensajeEditarDni').textContent = 'Error: ' + data.error;
            return;
        }
        document.getElementById('mensajeEditarDni').textContent = 'DNI actualizado correctamente.';
        // Recargamos las tablas
        cargarAlumnos();
        cargarVoluntarios();
        // Volver a donde estábamos
        setTimeout(() => {
            mostrar(seccionAnterior || 'inicio');
            document.getElementById('mensajeEditarDni').textContent = ''; 
        }, 1500);
        usuarioEditando = null;
    })
}

async function buscarUsuariosAlumnos() { 
    const busqueda = document.getElementById("barraBusquedaAlumnos").value.trim();

    // Si está vacío, recargar todos los alumnos
    if (busqueda === "") {
        cargarAlumnos();
        return;
    }

    try {
        const respuesta = await fetch("API.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                recurso: "usuarios",
                consulta: "Buscar",
                busqueda: busqueda,
                id_socio: 1
            })
        });
        const datos = await respuesta.json();

        const tbody = document.getElementById('tablasocios-Alumno');
        tbody.innerHTML = '';

        if (respuesta.ok) {
            // Mismo pintado que cargarAlumnos()
            datos.forEach(usuarios => {
                const dni = usuarios.DNI ?? '';
                const nombre = usuarios.nombre_completo ?? '';

                const fila = document.createElement('tr');

                const tdDni = document.createElement('td');
                const contDni = document.createElement('div');
                contDni.className = 'dni-con-boton';

                const spanDni = document.createElement('span');
                spanDni.textContent = escapeHtml(dni);

                const btnEditar = document.createElement('button');
                btnEditar.className = 'btn-editar-dni';
                btnEditar.innerHTML = '<img src="iconos/dni.png" alt="Editar">';
                btnEditar.onclick = () => editarDNI(dni);

                contDni.appendChild(btnEditar);
                contDni.appendChild(spanDni);
                tdDni.appendChild(contDni);

                const tdNombre = document.createElement('td');
                tdNombre.textContent = escapeHtml(nombre);

                const tdBoton = document.createElement('td');
                const btnInactivo = document.createElement('button');
                btnInactivo.className = 'btn-inactivo';

                const activo = usuarios.activo == 1;
                btnInactivo.innerHTML = activo
                    ? '<img src="iconos/inactivo.png" alt="Inactivar">'
                    : '<img src="iconos/activo.png" alt="Activar">';

                btnInactivo.onclick = () => cambiarEstadoActivo(usuarios.id, activo);
                tdBoton.appendChild(btnInactivo);

                fila.appendChild(tdDni);
                fila.appendChild(tdNombre);
                fila.appendChild(tdBoton);
                tbody.appendChild(fila);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3">No se encontraron usuarios.</td></tr>';
        }
    } catch (error) {
        console.error("Error:", error);
        document.getElementById('tablasocios-Alumno').innerHTML = 
            '<tr><td colspan="3">Error al buscar.</td></tr>';
    }
}

async function buscarUsuariosVoluntarios() {
    const busqueda = document.getElementById("barraBusquedaVoluntarios").value.trim();

    // Si está vacío, recargar todos los voluntarios
    if (busqueda === "") {
        cargarVoluntarios();
        return;
    }

    try {
        const respuesta = await fetch("API.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                recurso: "usuarios",
                consulta: "Buscar",
                busqueda: busqueda,
                id_socio: 2
            })
        });
        const datos = await respuesta.json();

        const tbody = document.getElementById('tablasocios-Voluntario');
        tbody.innerHTML = '';

        if (respuesta.ok) {
            // Mismo pintado que cargarVoluntarios()
            datos.forEach(usuarios => {
                const dni = usuarios.DNI ?? '';
                const nombre = usuarios.nombre_completo ?? '';

                const fila = document.createElement('tr');

                const tdDni = document.createElement('td');
                const contDni = document.createElement('div');
                contDni.className = 'dni-con-boton';

                const spanDni = document.createElement('span');
                spanDni.textContent = escapeHtml(dni);

                const btnEditar = document.createElement('button');
                btnEditar.className = 'btn-editar-dni';
                btnEditar.innerHTML = '<img src="iconos/dni.png" alt="Editar">';
                btnEditar.onclick = () => editarDNI(dni);

                contDni.appendChild(btnEditar);
                contDni.appendChild(spanDni);
                tdDni.appendChild(contDni);

                const tdNombre = document.createElement('td');
                tdNombre.textContent = escapeHtml(nombre);

                const tdBoton = document.createElement('td');
                const btnInactivo = document.createElement('button');
                btnInactivo.className = 'btn-inactivo';

                const activo = usuarios.activo == 1;
                btnInactivo.innerHTML = activo
                    ? '<img src="iconos/inactivo.png" alt="Inactivar">'
                    : '<img src="iconos/activo.png" alt="Activar">';

                btnInactivo.onclick = () => cambiarEstadoActivo(usuarios.id, activo);
                tdBoton.appendChild(btnInactivo);

                fila.appendChild(tdDni);
                fila.appendChild(tdNombre);
                fila.appendChild(tdBoton);
                tbody.appendChild(fila);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3">No se encontraron usuarios.</td></tr>';
        }
    } catch (error) {
        console.error("Error:", error);
        document.getElementById('tablasocios-Voluntario').innerHTML = 
            '<tr><td colspan="3">Error al buscar.</td></tr>';
    }
}


// carreras

// monto actual
let editandoMonto = false; 
let datosOriginalesMonto = null; 

function cargarMonto() {
    const mensaje = document.getElementById('mensajeCooperadora');
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            recurso: 'monto', 
            consulta: 'Read' 
        })
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('tablaMonto');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
           if (mensaje) mensaje.innerText = 'No se encontraron registros de monto.';
           return;
        }

        data.forEach(monto => { 
           const fila = document.createElement('tr');
           if (monto.id) fila.dataset.id = monto.id;

           fila.innerHTML = `
              <td>${monto.importe ?? ''}</td>
              <td>${monto.importe_anterior ?? ''}</td>
              <td>${monto.fecha_guardado ?? ''}</td>
              <td>${monto.fecha_efecto ?? ''}</td>
           `;
           tbody.appendChild(fila);
        });
   })
   .catch(error => {
        if (mensaje) mensaje.innerText = 'Error al cargar el monto de la cooperadora: ' + error;
   });
}

// Convierte fechas en formato DD/MM/YYYY a YYYY-MM-DD,
function formatearFechaParaInput(fechaStr) {
    if (!fechaStr) return '';
    if (fechaStr.includes('/')) {
        const partes = fechaStr.split('/');
        if (partes.length === 3) {
            return `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
        }
    }
    return fechaStr; // Si ya venía en formato YYYY-MM-DD
}

function editarMonto() {
    const tbody = document.getElementById("tablaMonto");
    const tr = tbody.querySelector("tr"); // Obtenemos la fila de la tabla
    const btnEditar = document.getElementById("editar");
    const btnCancelar = document.getElementById("cancelar");
    const mensaje = document.getElementById('mensajeDescargar');

    if (!tr) return; // Si no hay fila cargada, salimos de la función

    if (!editandoMonto) {
        const celdas = tr.querySelectorAll("td");

        // Guardamos una copia exacta de los textos originales
        datosOriginalesMonto = {
            importe: celdas[0].textContent.trim(),
            importeAnt: celdas[1].textContent.trim(),
            fechaGuardado: celdas[2].textContent.trim(), // Esta fecha no se editará
            fechaEfecto: celdas[3].textContent.trim()
        };

        // Convertimos la fecha de efecto al formato apto para input date
        const fechaEfectoInput = formatearFechaParaInput(datosOriginalesMonto.fechaEfecto);

        // Reemplazamos el texto plano de las celdas por campos <input>
        celdas[0].innerHTML = `<input type="number" step="0.01" id="inputImporte" value="${datosOriginalesMonto.importe}">`;
        celdas[1].innerHTML = `<input type="number" step="0.01" id="inputImporteAnt" value="${datosOriginalesMonto.importeAnt}">`;
        // Nota: celdas[2] (fecha_guardado) se omite intencionalmente para que no sea editable
        celdas[3].innerHTML = `<input type="date" id="inputFechaEfecto" value="${fechaEfectoInput}">`;

        // Cambiamos la interfaz del botón
        btnEditar.textContent = "Guardar"; // El botón "Editar" cambia su texto a "Guardar"
        btnEditar.classList.add("btn-guardar");
        if (btnCancelar) btnCancelar.style.display = "inline-block"; // Hacemos visible el botón Cancelar

        editandoMonto = true; // Actualizamos la bandera de estado a "editando"

    } else {
        // Capturamos los nuevos valores ingresados en los inputs
        const nuevoImporte = document.getElementById("inputImporte").value;
        const nuevoImporteAnt = document.getElementById("inputImporteAnt").value;
        const nuevaFechaEfecto = document.getElementById("inputFechaEfecto").value;
        
        // Obtenemos el ID de la fila desde el atributo data-id (o usa 1 por defecto)
        const idMonto = tr.dataset.id || 1;

        // Armamos el objeto con los datos a enviar a la BD
        const payload = {
            recurso: 'monto',
            consulta: 'Update',
            id: parseInt(idMonto),
            importe: nuevoImporte,
            importe_anterior: nuevoImporteAnt,
            fecha_efecto: nuevaFechaEfecto
        };

        // Enviamos la actualización vía Fetch a la API
        fetch('API.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }, // Header obligatorio para que la API procese el JSON
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            if (res.error) {
                alert("Error al guardar: " + res.error);
                return;
            }

            // Si la BD se actualizó con éxito, quitamos los inputs y mostramos el nuevo texto plano
            const celdas = tr.querySelectorAll("td");
            celdas[0].textContent = nuevoImporte;
            celdas[1].textContent = nuevoImporteAnt;
            celdas[3].textContent = nuevaFechaEfecto;

            // Restablecemos los botones al estado inicial
            restaurarBotones();
            editandoMonto = false; // Desactivamos el modo edición
        })
        .catch(error => {
            mensaje.innerHTML = 'Error al actualizar el monto de la cooperadora', error;
        });
    }
}

function cancelarEdicionMonto() {
    // Si no estamos en modo edición o no hay datos respaldados, no hacemos nada
    if (!editandoMonto || !datosOriginalesMonto) return;

    const tbody = document.getElementById("tablaMonto");
    const tr = tbody.querySelector("tr");

    if (tr) {
        const celdas = tr.querySelectorAll("td");
        
        // Restauramos los valores originales en texto plano sin tocar la BD
        celdas[0].textContent = datosOriginalesMonto.importe;
        celdas[1].textContent = datosOriginalesMonto.importeAnt;
        celdas[3].textContent = datosOriginalesMonto.fechaEfecto;
    }

    // Ocultamos el botón Cancelar y volvemos el botón a "Editar"
    restaurarBotones();
    editandoMonto = false; // Desactivamos el modo edición
}

function restaurarBotones() {
    const btnEditar = document.getElementById("editar");
    const btnCancelar = document.getElementById("cancelar");

    if (btnEditar) {
        btnEditar.textContent = "Editar";
        btnEditar.classList.remove("btn-guardar");
    }
    if (btnCancelar) {
        btnCancelar.style.display = "none"; // Oculta el botón cancelar
    }
}

//Pagos
function cargarPagos() {
    fetch('API.php', { // peticion fetch a la api
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'registroPagos', consulta: 'Read' })
    })
    .then(response => response.json()) // toma la respuesta y transforma el json a un array
    .then(data => { //recibe los datos procesados
        const tbody = document.getElementById('tablapagos');
        tbody.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) { // si data esta vacio corta las columnas y muestra por pantalla el error 
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No hay pagos registrados.</td></tr>';
            return;
        }

        data.forEach(pago => { //recorre data
            const fila = document.createElement('tr'); //crea una nueva fila por cada elemento

             let comprobanteHTML = '';
            if (pago.foto && pago.foto.trim() !== '') {
               let mimeType = 'image/jpeg'; // tipo de archivo por defecto
                // detecta la cabecera si el string viene en Base64
                if (pago.foto.startsWith('/9j/')) {
                    mimeType = 'image/jpeg'; // JPG / JPEG
                } else if (pago.foto.startsWith('iVBORw0KGg')) {
                    mimeType = 'image/png'; // PNG
                } else if (pago.foto.startsWith('R0lGOD')) {
                    mimeType = 'image/gif'; // GIF
                } else if (pago.foto.startsWith('JVBERi0')) {
                    mimeType = 'application/pdf'; // PDF
                }
                // Construye el enlace Data URL completo
                const srcFoto = pago.foto.startsWith('data:') 
                    ? pago.foto 
                    : `data:${mimeType};base64,${pago.foto}`;

                comprobanteHTML = `<img 
                        src="${srcFoto}" 
                        alt="Comprobante"
                        onclick="mostrarComprobante('${srcFoto}')"
                        style="
                            width: 100px;
                            height: 80px;
                            object-fit: contain;
                            cursor: pointer;
                            border-radius: 5px;
                        "
                    >
                `;
            }

            const idEstadoActual = parseInt(pago.id_estado); // obtiene id del estado y lo convierte a entero para comprarlo
            const estadoSelect = `
                <select onchange="cambiarEstadoPago(${pago.id}, this.value)" class="estado">
                    <option value="1" ${idEstadoActual === 1 ? 'selected' : ''}>Paga</option>
                    <option value="2" ${idEstadoActual === 2 ? 'selected' : ''}>Impaga</option>
                    <option value="3" ${idEstadoActual === 3 ? 'selected' : ''}>Pendiente</option>
                </select>
            `; // onchange llama a la funcion y le pasa el id del pago y la opcion seleccionada
              // le agregar la propiedad selected al option que coincia con el id del estado en la bd

            fila.innerHTML = `
                <td>${escapeHtml(pago.mes || '-')}</td>
                <td>${escapeHtml(pago.fecha || '-')}</td>
                <td>${escapeHtml(pago.nombre_completo || '-')}</td>
                <td>$${escapeHtml(pago.importe || '0')}</td>
                <td>${comprobanteHTML}</td>
                <td>${estadoSelect}</td>
                <td>${escapeHtml(pago.tipo_socio || '-')}</td>
                <td>${escapeHtml(pago.carrera || '-')}</td>
            `;
            tbody.appendChild(fila); //agrega la fila al tbody y termina el foreach
        });
    })
    .catch(error => {
        console.error('Error al cargar pagos:', error);
        const tbody = document.getElementById('tablapagos');
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">Error al cargar los pagos.</td></tr>';
    });
}

function mostrarComprobante(srcFoto) {
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
    modal.style.display = 'flex';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';
    modal.style.zIndex = '9999';
    modal.style.cursor = 'pointer';

    modal.innerHTML = `
        <img src="${srcFoto}" style="
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            cursor: default;
            border-radius: 5px;
        ">
    `;
    modal.onclick = function () {
        modal.remove();
    };

    document.body.appendChild(modal);
}

function cambiarEstadoPago(idPago, idEstado) {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'registroPagos', consulta: 'Update',
            id_pago: parseInt(idPago), id_estado: parseInt(idEstado) //convierte los valores a enteros
        })
    })
    .then(res => res.json()) // convierte la respuesta
    .then(res => {
        if (res.mensaje) { // si la respuesta tiene la propiedad mensaje (api) imprime en la consola
            console.log("Estado actualizado correctamente en BD");
        } else if (res.error) {
            alert("Error: " + res.error); //si no, muestra un mensaje de error
        }
    })
    .catch(err => console.error("Error en la petición:", err));
}

// Gráficos

// Exportar datos
function exportarExcelPagos() {
    const fecha_inicio = document.getElementById('fecha_inicio').value;
    const fecha_final = document.getElementById('fecha_final').value;
    const mensaje = document.getElementById('mensajeDescargar');

    if (!fecha_inicio || !fecha_final) {
        if (mensaje) mensaje.innerText = 'Por favor selecciona ambas fechas para definir el período.';
        return;
    }

    if (mensaje) mensaje.innerText = '';

    const payload = {
        recurso: 'registroPagos',
        consulta: 'ExportarExcelPagos',
        fechaInicio: fecha_inicio,
        fechaFinal: fecha_final
    };

    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(datos => {
        if (!Array.isArray(datos) || datos.length === 0) {
            if (mensaje) mensaje.innerText = "No se encontraron registros de pagos en el período seleccionado.";
            return;
        }

        // Formateamos los datos
        const datosFormateados = datos.map(item => {
            let fechaObjeto = '';
            if (item.fecha && item.fecha.includes('-')) {
                const partes = item.fecha.split('-');
                if (partes.length === 3) {
                    fechaObjeto = new Date(partes[0], partes[1] - 1, partes[2]);
                }
            }

            return {
                "DNI": item.DNI ?? '',
                "Nombre Completo": item.nombre_completo ?? '',
                "Tipo de Socio": item.socio ?? '',
                "Carrera": item.carrera ?? '',
                "Monto ($)": parseFloat(item.monto) || 0,
                "Fecha de Pago": fechaObjeto
            };
        });

        const hojaTrabajo = XLSX.utils.json_to_sheet(datosFormateados);
        const libroTrabajo = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(libroTrabajo, hojaTrabajo, "Pagos");

        XLSX.writeFile(libroTrabajo, `Reporte_Pagos_${fecha_inicio}_a_${fecha_final}.xlsx`);
    })
    .catch(error => {
        if (mensaje) mensaje.innerText = "Error al exportar pagos: " + error;
    });
}

function exportarExcelUsuarios() {
    const mensaje = document.getElementById('mensajeDescargar');
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            recurso: 'usuarios', 
            consulta: 'ExportarExcelUsuarios' 
        })
    })
    .then(response => response.json())
    .then(datos => {
        if (!Array.isArray(datos) || datos.length === 0) {
            if (mensaje) mensaje.innerText = "No se encontraron usuarios registrados.";
            return;
        }

        // Formateamos los datos forzando el monto como NÚMERO
        const datosFormateados = datos.map(item => ({
            "DNI": item.DNI ?? '',
            "Nombre Completo": item.nombre_completo ?? '',
            "Tipo de Socio": item.socio ?? '',
            "Carrera": item.carrera ?? '',
        }));

        const hojaTrabajo = XLSX.utils.json_to_sheet(datosFormateados);
        const libroTrabajo = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(libroTrabajo, hojaTrabajo, "Usuarios");

        XLSX.writeFile(libroTrabajo, `Reporte_Usuarios.xlsx`);
    })
    .catch(error => {
        if (mensaje) mensaje.innerText = "Error al exportar usuarios: " + error;
    });
}
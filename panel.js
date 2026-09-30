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

    if (id === 'verPagos') {
        cargarPagos();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarInicio();
    cargarPagos();
    cambiarEstadoPago();
    cargarMonto();
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
            // cuenta los cocios alumnos y voluntarios
            let alumnos = 0; 
            let voluntarios = 0;

        usuarios.forEach(usuario => {
            if (usuario.nombre_socio === 'Alumno') { alumnos++; }
            if (usuario.nombre_socio === 'Voluntario') { voluntarios++; }
        });
        document.getElementById('cantAlumnos').textContent = alumnos;
        document.getElementById('cantVoluntarios').textContent = voluntarios;
        document.getElementById('cantCarreras').textContent = carreras.length -1;

        if (montos.length > 0) { // devuelve cuantos elementos hay
            const ultimoMonto = montos[montos.length - 1]; //obtiene el ultimo monto
            document.getElementById('montoActual').textContent = '$' + ultimoMonto.importe;
        }
    })
    .catch(error => {
        console.error('Error al cargar el inicio:', error); 
    });
}

// socio alumnos
function cargarAlumno() {
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
            btnInactivo.innerHTML = '<img src="iconos/inactivo.png" alt="Inactivo">';
            btnInactivo.onclick = () => marcarInactivo(dni);
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
function cargarVoluntario() {
    
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
      
        console.log('Respuesta:', data);
         const tbody = document.getElementById('tablasocios-Voluntario');
        tbody.innerHTML = '';
        data.forEach(usuarios => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${escapeHtml(usuarios.DNI ?? '')}</td>
                <td>${escapeHtml(usuarios.nombre_completo ?? '')}</td>
                <td>${escapeHtml(usuarios.activo ?? '')}</td>
            `;
            tbody.appendChild(fila);
        }); 
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="3">Error al cargar los socios.</td></tr>';
    }); 
}



// carreras

// monto actual
let editandoMonto = false; 
let datosOriginalesMonto = null; 

function cargarMonto() {
    // Realizamos la petición POST a la API para leer la información de la BD
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
        tbody.innerHTML = ''; // Limpiamos la tabla antes de renderizar

        // Validamos que la respuesta sea un arreglo con datos
        if (!Array.isArray(data) || data.length === 0) {
           console.error("Respuesta de la API no es una lista válida:", data);
           return;
        }

        // Recorremos los registros devueltos por la API
        data.forEach(monto => { 
           const fila = document.createElement('tr');
           
           // Guardamos el ID real del registro dentro de un atributo HTML (data-id)
           // Esto es clave para saber qué registro actualizar luego en la BD
           if (monto.id) {
               fila.dataset.id = monto.id;
           }

           // Insertamos los valores en cada celda de la fila
           fila.innerHTML = `
              <td>${monto.importe ?? ''}</td>
              <td>${monto.importe_anterior ?? ''}</td>
              <td>${monto.fecha_guardado ?? ''}</td>
              <td>${monto.fecha_efecto ?? ''}</td>
           `;

           tbody.appendChild(fila);
        });
   })
   .catch(error => console.error('Error al cargar la tabla de montos:', error));
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

    if (!tr) return; // Si no hay fila cargada, salimos de la función

    if (!editandoMonto) {
        const celdas = tr.querySelectorAll("td");

        // 1. Guardamos una copia exacta de los textos originales
        datosOriginalesMonto = {
            importe: celdas[0].textContent.trim(),
            importeAnt: celdas[1].textContent.trim(),
            fechaGuardado: celdas[2].textContent.trim(), // Esta fecha no se editará
            fechaEfecto: celdas[3].textContent.trim()
        };

        // Convertimos la fecha de efecto al formato apto para input date
        const fechaEfectoInput = formatearFechaParaInput(datosOriginalesMonto.fechaEfecto);

        // 2. Reemplazamos el texto plano de las celdas por campos <input>
        celdas[0].innerHTML = `<input type="number" step="0.01" id="inputImporte" value="${datosOriginalesMonto.importe}">`;
        celdas[1].innerHTML = `<input type="number" step="0.01" id="inputImporteAnt" value="${datosOriginalesMonto.importeAnt}">`;
        // Nota: celdas[2] (fecha_guardado) se omite intencionalmente para que no sea editable
        celdas[3].innerHTML = `<input type="date" id="inputFechaEfecto" value="${fechaEfectoInput}">`;

        // 3. Cambiamos la interfaz del botón
        btnEditar.textContent = "Guardar"; // El botón "Editar" cambia su texto a "Guardar"
        btnEditar.classList.add("btn-guardar");
        if (btnCancelar) btnCancelar.style.display = "inline-block"; // Hacemos visible el botón Cancelar

        editandoMonto = true; // Actualizamos la bandera de estado a "editando"

    } else {
        // 1. Capturamos los nuevos valores ingresados en los inputs
        const nuevoImporte = document.getElementById("inputImporte").value;
        const nuevoImporteAnt = document.getElementById("inputImporteAnt").value;
        const nuevaFechaEfecto = document.getElementById("inputFechaEfecto").value;
        
        // Obtenemos el ID de la fila desde el atributo data-id (o usa 1 por defecto)
        const idMonto = tr.dataset.id || 1;

        // 2. Armamos el objeto con los datos a enviar a la BD
        const payload = {
            recurso: 'monto',
            consulta: 'Update',
            id: parseInt(idMonto),
            importe: nuevoImporte,
            importe_anterior: nuevoImporteAnt,
            fecha_efecto: nuevaFechaEfecto
        };

        // 3. Enviamos la actualización vía Fetch a la API
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

            // 4. Si la BD se actualizó con éxito, quitamos los inputs y mostramos el nuevo texto plano
            const celdas = tr.querySelectorAll("td");
            celdas[0].textContent = nuevoImporte;
            celdas[1].textContent = nuevoImporteAnt;
            celdas[3].textContent = nuevaFechaEfecto;

            // 5. Restablecemos los botones al estado inicial
            restaurarBotones();
            editandoMonto = false; // Desactivamos el modo edición
        })
        .catch(err => {
            console.error("Error al actualizar monto:", err);
            alert("Error de comunicación con el servidor.");
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

        if (!Array.isArray(data) || data.length === 0) { // si data esta vacio corta las columnas y uestra por pantalla el error 
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No hay pagos registrados.</td></tr>';
            return;
        }

        data.forEach(pago => { //recorre data
            const fila = document.createElement('tr'); //crea una nueva fila por cada elemento

            // comprobante

            const idEstadoActual = parseInt(pago.id_estado); // obtiene id del estado y lo convierte a entero para comprarlo
            const estadoSelect = `
                <select onchange="cambiarEstadoPago(${pago.id}, this.value)" class="estado">
                    <option value="1" ${idEstadoActual === 1 ? 'selected' : ''}>Paga</option>
                    <option value="2" ${idEstadoActual === 2 ? 'selected' : ''}>Inpaga</option>
                    <option value="3" ${idEstadoActual === 3 ? 'selected' : ''}>Pendiente</option>
                </select>
            `; // onchange llama a la funcion y le pasa el id del pago y la opcion seleccionada
              // le agregar la propiedad selected al option que coincia con el id del estado en la bd

            fila.innerHTML = `
                <td>${escapeHtml(pago.mes || '-')}</td>
                <td>${escapeHtml(pago.fecha || '-')}</td>
                <td>${escapeHtml(pago.nombre_completo || '-')}</td>
                <td>$${escapeHtml(pago.importe || '0')}</td>
                <td>${scapeHtml(comprobante)}</td>
                <td>${echo ("holis")}</td>
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

function cambiarEstadoPago(idPago, idEstado) {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'registroPagos', consulta: 'Update',
            id_pago: parseInt(idPago), estado: parseInt(idEstado) //convierte los valores a enteros
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

// graficos
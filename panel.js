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
cargarInicio();

// socio alumnos

// socio voluntarios

// carreras

// monto actual

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
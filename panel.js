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

// ver pagos
function cargarPagos() {
    fetch('API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ recurso: 'registroPagos', consulta: 'Read'})
    }).then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('tablapagos');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay pagos registrados.</td></tr>';
            return;
        }

        data.forEach(pago => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${escapeHtml(pago.nombre_completo || '-')}</td>
                <td>${escapeHtml(pago.tipo_socio || '-')}</td>
                <td>${escapeHtml(pago.carrera || '-')}</td>
                <td>${escapeHtml(pago.mes || '-')}</td>
                <td>${escapeHtml(pago.fecha || '-')}</td>
                <td>$${escapeHtml(pago.importe || '0')}</td>
                <td>${escapeHtml(pago.estado || '-')}</td>
                <td>${pago.foto
                    ? `<a href="${escapeHtml(pago.foto)}}`
                    : 'Sin archivo'
                }</td>`;
            tbody.appendChild(fila);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        const tbody = document.getElementById('tablapagos');
        tbody.innerHTML = '<tr><td colspan="8">Error al cargar los pagos.</td></tr>';
    });
}

// graficos
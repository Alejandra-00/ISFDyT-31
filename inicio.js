// solicita a la api los montos que se muestran en la pagina de inicio
fetch("API.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        recurso: "monto",
        consulta: "Inicio"
    })
})

// hace el json y actualiza los montos visibles
.then(respuesta => respuesta.json())
.then(datos => {
    if (datos.importe !== undefined) {
        document.getElementById("montoActual").textContent = "$" + datos.importe;
    }
    if (datos.importe_anterior !== undefined) {
        document.getElementById("montoAnterior").textContent = "$" + datos.importe_anterior;
    }
})

// registra en la consola los errores durante la solicitud o procesamiento
.catch(error => {
    console.error(error);
    const mensaje1 = document.getElementById('mensajeMonto1');
    const mensaje2 = document.getElementById('mensajeMonto2');
    
    if (mensaje1) mensaje1.innerText = 'Error al cargar el monto.';
    if (mensaje2) mensaje2.innerText = 'Error al cargar el monto.';
    
    mensaje1.style.display = "block";
    mensaje2.style.display = "block";
});
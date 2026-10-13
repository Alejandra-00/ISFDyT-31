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
    console.error("Error al obtener los montos:", error);
});
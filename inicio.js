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
    document.getElementById("montoActual").textContent = "$" + datos.importe;
    document.getElementById("montoAnterior").textContent = "$" + datos.importe_anterior;
})

// registra en la consola los errores durante la solicitud o procesamiento
.catch(error => {
    console.error("Error al obtener los montos:", error);
});
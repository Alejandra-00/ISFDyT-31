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

.then(respuesta => respuesta.json())
.then(datos => {
    document.getElementById("montoActual").textContent = "$" + datos.importe;
    document.getElementById("montoAnterior").textContent = "$" + datos.importe_anterior;
})

.catch(error => {
    console.error("Error al obtener los montos:", error);
});
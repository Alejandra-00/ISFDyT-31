  function cargarPagos() {
            fetch('API.php', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ recurso: 'registroPagos', consulta: 'Readusuarios', id_usuario: document.getElementById('usuario').value })
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('tablapagos');
                tbody.innerHTML = '';

                data.forEach(pago => { 
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${pago.meses ?? ''}</td>
                        <td>${pago.monto ?? ''}</td>
                        <td>${pago.fecha ?? ''}</td>
                        <td>${pago.estadopago ?? ''}</td>
                    `;
                    tbody.appendChild(fila);
                });
            })
            .catch(error => console.error('Error:', error));
}   

        // Cargar los pagos automáticamente al abrir la página
        cargarPagos();

        /*
   function obtenerDatos() {
      // Petición para obtener el Monto
      fetch("API.php", {
         method: "POST",
         headers: {
            "Content-Type": "application/json"
         },
         body: JSON.stringify({
            recurso: "monto",
            consulta: "Read"
         })
      })
      .then(respuesta => respuesta.json())
      .then(datosMonto => {
         console.log("Datos de monto:", datosMonto);
         //Como 'datosMonto' es un arreglo, tomamos el valor 'importe' del primer elemento
         if (datosMonto && datosMonto.length > 0) {
            document.getElementById("monto").textContent = "$" + datosMonto[0].importe;
         }
      })
      .catch(error => console.error("Error al obtener monto:", error));
      
      //Petición para obtener los Meses
      fetch("API.php", {
         method: "POST",
         headers: {
         "Content-Type": "application/json"
         },
         body: JSON.stringify({
            recurso: "meses",
            consulta: "Read"
         })
      })
      .then(respuesta => respuesta.json())
      .then(datosMeses => {
         console.log("Datos de meses:", datosMeses);
         //Como 'datosMeses' es un arreglo de nombres, mostramos el mes deseado (por ejemplo, el primero)
         if (datosMeses && datosMeses.length > 0) {
            document.getElementById("mes").textContent =datosMeses[0].nombre;
         }
      })
      .catch(error => console.error("Error al obtener meses:", error));
   }

   //Cargar los datos automáticamente al cargar la página
   document.addEventListener("DOMContentLoaded", obtenerDatos);
*/

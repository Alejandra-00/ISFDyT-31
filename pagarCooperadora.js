function copiarElemento(idElemento) {
   const texto = document.getElementById(idElemento).innerText;
   navigator.clipboard.writeText(texto)
      .then(() => alert("Elemento copiado al portapapeles!"))
      .catch(err => console.error("Error: ", err));
} 

// Muestra únicamente los IDs que se pasen por parámetro
function mostrar(...ids) { 
   document.querySelectorAll('.contenedor').forEach(contenedor => {
      contenedor.classList.remove('activo');
   });

   ids.forEach(id => {
      const elemento = document.getElementById(id);
      if (elemento) {
         elemento.classList.add('activo');
      }
   });
}

function comprobanteSeleccionado() {
   const input = document.getElementById('subir');
   const texto = document.getElementById('textoSubir');
   const btnPago = document.getElementById('BtnEnviarPago');
   
   if (input.files && input.files[0]) {
      texto.innerText = "Imagen cargada: " + input.files[0].name;
      if (btnPago) btnPago.disabled = false;
   } else {
      texto.innerText = "Subir foto del comprobante";
      if (btnPago) btnPago.disabled = true;
   }
}

function aceptarComprobante() {
   const input = document.getElementById('subir');
   if (!input.files || input.files.length === 0) {
      alert("Debes adjuntar una foto del comprobante.");
      return;
   }
   // Al pulsar Aceptar, oculta 'enviarComprobante' y vuelve a 'pasarela'
   mostrar('pasarela');
}

document.addEventListener('DOMContentLoaded', () => {
   obtenerDatosPagos();
});

function obtenerDatosPagos() {
   const idPago = document.getElementById('idPagoActual').value;

   fetch('API.php', { 
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ recurso: 'registroPagos', consulta: 'Readpagos', id_pago: idPago })
   })
   .then(respuesta => respuesta.json())
   .then(datos => {
      if (datos && !datos.error) {
         document.getElementById("verMes").textContent = datos.meses ?? '';
         document.getElementById("verMonto").textContent = "$" + (datos.monto ?? '0');
         
         const textoEstado = datos.estadopago ?? 'Impago';
         
         // Actualizar textos de estado en la pasarela y en cuotaPendiente
         const elementosEstado = document.querySelectorAll('#verEstado, #verEstadoPendiente');
         elementosEstado.forEach(el => el.textContent = textoEstado);

         // Muestra los contenedores según el estado que viene de la BD
         evaluarVistasSegunEstado(textoEstado);
      }
   })
   .catch(error => console.error("Error al obtener los datos del pago:", error));
}

function evaluarVistasSegunEstado(estado) {
   if (estado === 'Pendiente') {
      // Si está pendiente, se muestran PASARELA y CUOTAPENDIENTE juntos
      mostrar('pasarela', 'cuotaPendiente');
      desactivarBotones('Pendiente');
   } else if (estado === 'Pago') {
      // Si el admin ya validó el pago, se oculta cuotaPendiente y queda solo PASARELA
      mostrar('pasarela');
      desactivarBotones('Pago');
   } else {
      // Si es Impago
      mostrar('pasarela');
      desactivarBotones('Impago');
   }
}

function desactivarBotones(estado) {
   const btnFactura = document.getElementById('BtnDescargarFactura');
   const btnComprobante = document.getElementById('BtnEnviarComprobante');
   const btnPago = document.getElementById('BtnEnviarPago');

   if (estado === 'Pago') {
      if (btnPago) btnPago.disabled = true;
      if (btnComprobante) btnComprobante.disabled = true;
      if (btnFactura) btnFactura.disabled = false;
   } else if (estado === 'Pendiente') {
      if (btnPago) btnPago.disabled = true;
      if (btnComprobante) btnComprobante.disabled = true;
      if (btnFactura) btnFactura.disabled = true;
   } else { // Impago
      if (btnPago) btnPago.disabled = true; // Habilitar solo cuando adjunte comprobante
      if (btnComprobante) btnComprobante.disabled = false;
      if (btnFactura) btnFactura.disabled = true;
   }
}

function enviarPagoAPI() {
   const idPagoInput = document.getElementById('idPagoActual');
   const idPago = idPagoInput ? idPagoInput.value : 0;
   const fileInput = document.getElementById('subir');

   if (!idPago || idPago === "0") {
      alert("No se encontró el ID de pago a actualizar.");
      return;
   }

   if (!fileInput.files || fileInput.files.length === 0) {
      alert("Por favor, selecciona una foto del comprobante primero.");
      return;
   }

   const archivo = fileInput.files[0];
   const reader = new FileReader();

   reader.readAsDataURL(archivo);
   reader.onload = function () {
      const fotoBase64 = reader.result;

      const datosEnvio = {
         recurso: 'registroPagos',
         consulta: 'UpdatePago',
         id_pago: parseInt(idPago),
         id_estado: 3, // ID 3 = Estado 'Pendiente' en la base de datos
         foto: fotoBase64
      };

      fetch('API.php', {
         method: 'POST',
         headers: { 'Content-Type': 'application/json' },
         body: JSON.stringify(datosEnvio)
      })
      .then(respuesta => respuesta.json())
      .then(datos => {
         if (datos.mensaje) {
            // Muestra pasarela + cuotaPendiente juntos
            mostrar('pasarela', 'cuotaPendiente');
            
            // Actualizar vista local a Pendiente
            const elementosEstado = document.querySelectorAll('#verEstado, #verEstadoPendiente');
            elementosEstado.forEach(el => el.textContent = 'Pendiente');
            
            desactivarBotones('Pendiente');
         } else {
            alert("Error: " + (datos.error || "No se pudo procesar el pago."));
         }
      })
      .catch(error => {
         console.error("Error al enviar el pago:", error);
         alert("Ocurrió un error al intentar conectar con el servidor.");
      });
   };
}

function descargarFactura() {
   const idPago = document.getElementById('idPagoActual').value;

   // 1. Obtener datos desde la API
   fetch('API.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
         recurso: 'registroPagos',
         consulta: 'Readpagos',
         id_pago: parseInt(idPago)
      })
   })
   .then(respuesta => {
      if (!respuesta.ok) throw new Error("Error en la llamada a API.php");
      return respuesta.json();
   })
   .then(datosAPI => {
      if (!datosAPI || datosAPI.error) {
         alert("Error al obtener los datos del pago: " + (datosAPI.error || "Sin respuesta."));
         return;
      }

      // 2. Enviar los datos obtenidos a factura.php
      return fetch('factura.php', {
         method: 'POST',
         headers: { 'Content-Type': 'application/json' },
         body: JSON.stringify(datosAPI)
      });
   })
   .then(res => {
      if (!res.ok) {
         // Si factura.php da un error (ej. 500), leemos el texto para saber la causa exacta
         return res.text().then(textoError => {
            console.error("Detalle del error en factura.php:", textoError);
            throw new Error("Error en el servidor al generar la factura.");
         });
      }
      return res.blob();
   })
   .then(blob => {
      // 3. Generar la descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `Factura.pdf`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);
   })
   .catch(error => {
      console.error("Error al descargar la factura:", error);
      alert("Ocurrió un error al intentar descargar la factura. Revisa la consola para más detalles.");
   });
}
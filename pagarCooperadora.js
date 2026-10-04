document.addEventListener('DOMContentLoaded', () => {
   obtenerDatosPagos();
});

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
      const mensajeComprobante = document.getElementById('mensajeComprobante');
      mensajeComprobante.innerText = "Debes adjuntar una foto del comprobante.";
      mensajeComprobante.style.display = "block";
      return;
   }
   // Al pulsar Aceptar, oculta 'enviarComprobante' y vuelve a 'pasarela'
   mostrar('pasarela');
}

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
      // Si está pendiente, se muestra PASARELA
      mostrar('pasarela');
      desactivarBotones('Pendiente');
   } else if (estado === 'Pago') {
      // Si el admin ya validó el pago, se muestra PASARELA
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
      if (btnPago) btnPago.disabled = false;
      if (btnComprobante) btnComprobante.disabled = false;
      if (btnFactura) btnFactura.disabled = true;
   }
}

function enviarPagoAPI() {
   const idPagoInput = document.getElementById('idPagoActual');
   const idPago = idPagoInput ? idPagoInput.value : 0;
   const fileInput = document.getElementById('subir');
   const mensajeEnvio = document.getElementById('mensajeEnvio');
   const mensajeComprobante = document.getElementById('mensajeComprobante');

   if (!idPago || idPago === "0") {
      mensajeEnvio = "No se encontró el ID del pago.";
      mensajeEnvio.style.display = "block";
      return;
   }

   if (!fileInput.files || fileInput.files.length === 0) {
      mensajeComprobante = "Por favor, selecciona una foto del comprobante primero.";
      mensajeComprobante.style.display = "block";
      return;
   }

   const archivo = fileInput.files[0];
   const reader = new FileReader();

   reader.readAsDataURL(archivo);
   reader.onload = function (e) {
      const img = new Image();
      img.src = e.target.result;

      img.onload = function () {
         // Crear canvas para redimensionar y comprimir la imagen
         const canvas = document.createElement('canvas');
         const MAX_WIDTH = 1000; // Ancho máximo de 1000px (suficiente para un comprobante)
         let scaleSize = 1;

         if (img.width > MAX_WIDTH) {
            scaleSize = MAX_WIDTH / img.width;
         }

         canvas.width = img.width * scaleSize;
         canvas.height = img.height * scaleSize;

         const ctx = canvas.getContext('2d');
         ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

         // Convertir a JPEG comprimido al 70% de calidad
         const fotoBase64Comprimida = canvas.toDataURL('image/jpeg', 0.7);

         const datosEnvio = {
            recurso: 'registroPagos',
            consulta: 'UpdatePago',
            id_pago: parseInt(idPago),
            id_estado: 3, 
            foto: fotoBase64Comprimida
         };

         // Enviar al servidor
         fetch('API.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosEnvio)
         })
         .then(async respuesta => {
            const texto = await respuesta.text();
            
            try {
               return JSON.parse(texto);
            } catch (err) {
               mensajeEnvio = "Respuesta cruda de PHP (no es JSON):" + texto;
               throw new Error("El servidor devolvió una respuesta no válida. Revisa la consola.");
            }
         })
         .then(datos => {
            if (datos.mensaje) {
               mensajeEnvio = "¡Pago enviado con éxito!";
               if (typeof mostrar === "function") mostrar('pasarela');

               const elementosEstado = document.querySelectorAll('#verEstado, #verEstadoPendiente');
               elementosEstado.forEach(el => el.textContent = 'Pendiente');
            } else {
               mensajeEnvio = "Error: " + (datos.error || "No se pudo actualizar el pago.");
            }
         })
         .catch(error => {
            console.error("Detalle del error:", error);
            mensajeEnvio = "Ocurrió un error al enviar el pago." + error;
         });
      };
   };
}

function descargarFactura() {
   const idPago = document.getElementById('idPagoActual').value;
   const mensajeFactura = document.getElementById('mensajeFactura');

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
         mensajeFactura.innerText = "Error al obtener los datos del pago: " + (datosAPI.error || "Sin respuesta.");
         mensajeFactura.style.display = "block";
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
            mensajeFactura.innerText = "Detalle del error en factura.php:", textoError;
            mensajeFactura.style.display = "block";
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
      mensajeFactura.innerText = "Ocurrió un error al intentar descargar la factura. Revisa la consola para más detalles." + error;
      mensajeFactura.style.display = "block";
   });
}
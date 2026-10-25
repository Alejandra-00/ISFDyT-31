document.addEventListener('DOMContentLoaded', () => {
   obtenerDatosPagos();
});

function copiarElemento(idElemento) {
   const elemento = document.getElementById(idElemento);
   const mensajeEstado = document.getElementById('mensajeEstado'); // Elemento para la UI

   if (!elemento) {
      console.error(`Error al copiar: No se encontró el elemento con ID '${idElemento}' en el DOM.`);
      if (mensajeEstado) {
         mensajeEstado.innerText = "No se pudo copiar el texto seleccionado.";
         mensajeEstado.style.display = "block";
      }
      return;
   }

   const texto = elemento.innerText;

   //navigator.clipboard: Es una API moderna del navegador que da acceso de lectura y escritura al portapapeles del dispositivo
   navigator.clipboard.writeText(texto)
      .then(() => {
         // Mensaje amigable para el usuario en la interfaz
         if (mensajeEstado) {
            mensajeEstado.innerText = "¡Copiado al portapapeles!";
            mensajeEstado.style.color = "#00ff88"; // Opcional: verde éxito
            mensajeEstado.style.display = "block";

            // Ocultar el mensaje automáticamente después de 3 segundos
            setTimeout(() => {
               mensajeEstado.style.display = "none";
            }, 3000);
         }
      })
      .catch(err => {
         // Detalle técnico para el desarrollador
         console.error("Detalle técnico del error al intentar copiar:", err);
         
         // Mensaje amigable para la interfaz
         if (mensajeEstado) {
            mensajeEstado.innerText = "No se pudo copiar el texto. Inténtalo manualmente.";
            mensajeEstado.style.color = "yellow";
            mensajeEstado.style.display = "block";
         }
      });
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
   const mensajeComprobante = document.getElementById('mensajeComprobante');
   
   if (!input.files || input.files.length === 0) {
      if (mensajeComprobante) {
         mensajeComprobante.innerText = "Debes seleccionar una foto del comprobante antes de continuar.";
         mensajeComprobante.style.display = "block";
      }
      return;
   }
   // Al pulsar Aceptar, oculta 'enviarComprobante' y vuelve a 'pasarela'
   mostrar('pasarela');
}

function obtenerDatosPagos() {
   const idPagoInput = document.getElementById('idPagoActual');
   const idPago = idPagoInput ? idPagoInput.value : 0;
   const mensaje = document.getElementById('mensaje');

   fetch('API.php', { 
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ recurso: 'registroPagos', consulta: 'Readpagos', id_pago: idPago })
   })
   .then(respuesta => {
      if (!respuesta.ok) throw new Error(`HTTP error! Estado: ${respuesta.status}`);
      return respuesta.json();
   })
   .then(datos => {
      if (datos && !datos.error) {
         document.getElementById("verMes").textContent = datos.meses ?? '';
         document.getElementById("verMonto").textContent = "$" + (datos.monto ?? '0');
         
         const textoEstado = datos.estadopago ?? 'Impago';
         
         // Actualizar textos de estado en la pasarela
         const elementosEstado = document.querySelectorAll('#verEstado, #verEstadoPendiente');
         elementosEstado.forEach(el => el.textContent = textoEstado);

         // Evaluar vistas y actualizar estado disabled de botones
         evaluarVistasSegunEstado(textoEstado);
      } else {
         console.error("Error devuelto por la API al leer el pago:", datos.error);
         if (mensaje) {
            mensaje.innerText = "No se pudo cargar la información de tu pago. Por favor, intenta de nuevo.";
            mensaje.style.display = "block";
         }
      }
   })
   .catch(error => {
      console.error("Detalle del error en obtenerDatosPagos:", error);
      if (mensaje) {
         mensaje.innerText = "Hubo un problema al conectar con el servidor.";
         mensaje.style.display = "block";
      }
   });
}

function evaluarVistasSegunEstado(estado) {
   if (estado === 'Pendiente' || estado === 'Pago') {
      mostrar('pasarela');
      desactivarBotones(estado);
   } else {
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

   // Limpiar mensajes previos
   if (mensajeEnvio) mensajeEnvio.style.display = "none";
   if (mensajeComprobante) mensajeComprobante.style.display = "none";

   if (!idPago || idPago === "0") {
      console.error("Error de validación local: No se encontró el ID del pago en el DOM.");
      if (mensajeEnvio) {
         mensajeEnvio.innerText = "No se pudo identificar el pago a actualizar.";
         mensajeEnvio.style.display = "block";
      }
      return;
   }

   if (!fileInput.files || fileInput.files.length === 0) {
      if (mensajeComprobante) {
         mensajeComprobante.innerText = "Por favor, adjunta la imagen del comprobante antes de realizar el envío.";
         mensajeComprobante.style.display = "block";
      }
      return;
   }

   const archivo = fileInput.files[0];
   const reader = new FileReader();

   reader.readAsDataURL(archivo);
   reader.onload = function (e) {
      const img = new Image();
      img.src = e.target.result;

      img.onload = function () {
         const canvas = document.createElement('canvas');
         const MAX_WIDTH = 1000;
         let scaleSize = 1;

         if (img.width > MAX_WIDTH) {
            scaleSize = MAX_WIDTH / img.width;
         }

         canvas.width = img.width * scaleSize;
         canvas.height = img.height * scaleSize;

         const ctx = canvas.getContext('2d');
         ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

         const fotoBase64Comprimida = canvas.toDataURL('image/jpeg', 0.7);

         const datosEnvio = {
            recurso: 'registroPagos',
            consulta: 'UpdatePago',
            id_pago: parseInt(idPago),
            id_estado: 3, // Estado 'Pendiente'
            foto: fotoBase64Comprimida
         };

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
               console.error("Error al parsear JSON desde API.php. Respuesta recibida:", texto);
               throw new Error("Respuesta inválida del servidor.");
            }
         })
         .then(datos => {
            if (datos.mensaje) {
               if (mensajeEnvio) {
                  mensajeEnvio.innerText = "¡Tu comprobante ha sido enviado con éxito! Queda pendiente de validación.";
                  mensajeEnvio.style.display = "block";
               }
               if (typeof mostrar === "function") mostrar('pasarela');

               const elementosEstado = document.querySelectorAll('#verEstado, #verEstadoPendiente');
               elementosEstado.forEach(el => el.textContent = 'Pendiente');

               desactivarBotones('Pendiente');
            } else {
               console.error("Error retornado por la API al actualizar pago:", datos.error);
               if (mensajeEnvio) {
                  mensajeEnvio.innerText = "No se pudo registrar el pago. Por favor, asegúrate de enviar un archivo de imagen válido.";
                  mensajeEnvio.style.display = "block";
               }
            }
         })
         .catch(error => {
            console.error("Error técnico durante la petición de envío del pago:", error);
            if (mensajeEnvio) {
               mensajeEnvio.innerText = "Ocurrió un inconveniente al conectar con el servidor. Intenta de nuevo más tarde.";
               mensajeEnvio.style.display = "block";
            }
         });
      };

      img.onerror = function(err) {
         console.error("Error al cargar la imagen seleccionada en el Canvas:", err);
         if (mensajeComprobante) {
            mensajeComprobante.innerText = "El archivo seleccionado no se pudo procesar como imagen.";
            mensajeComprobante.style.display = "block";
         }
      };
   };

   reader.onerror = function(err) {
      console.error("Error al leer el archivo con FileReader:", err);
   };
}

function descargarFactura() {
   const idPagoInput = document.getElementById('idPagoActual');
   const idPago = idPagoInput ? idPagoInput.value : 0;
   const mensajeFactura = document.getElementById('mensajeFactura');

   if (mensajeFactura) mensajeFactura.style.display = "none";

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
      if (!respuesta.ok) throw new Error(`HTTP error! Estado: ${respuesta.status}`);
      return respuesta.json();
   })
   .then(datosAPI => {
      if (!datosAPI || datosAPI.error) {
         console.error("Error obtenido desde Readpagos para la factura:", datosAPI.error);
         if (mensajeFactura) {
            mensajeFactura.innerText = "No fue posible obtener los datos de tu comprobante.";
            mensajeFactura.style.display = "block";
         }
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
      if (!res) return; // Se cortó en el paso anterior por error de datos
      if (!res.ok) {
         return res.text().then(textoError => {
            console.error("Error retornado por factura.php:", textoError);
            if (mensajeFactura) {
               mensajeFactura.innerText = "Ocurrió un problema al generar la factura en PDF.";
               mensajeFactura.style.display = "block";
            }
            throw new Error("Error en servidor generando factura.");
         });
      }
      return res.blob();
   })
   .then(blob => {
      if (!blob) return;
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
      console.error("Detalle del error en descargarFactura:", error);
      if (mensajeFactura && mensajeFactura.style.display !== "block") {
         mensajeFactura.innerText = "No se pudo procesar la descarga de la factura.";
         mensajeFactura.style.display = "block";
      }
   });
}
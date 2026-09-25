function copiarElemento(idElemento) {
   const texto = document.getElementById(idElemento).innerText;
   navigator.clipboard.writeText(texto)
      .then(() => {
         alert("Elemento copiado al portapapeles!");
      })
      .catch(err => console.error("Error: ", err));
} 
   
function mostrar(...ids) { 
   /* 
      Los tres puntos son un caracteristico parametro de JS llamado Rest parameter. 
      Sirven para indicarle a la función que puede recibir cualquier cantidad de 
      argumentos y que debe guardarlos todos juntos dentro de una lista (array) llamado ids. 
   */
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
   
   if (input.files && input.files[0]) {
      texto.innerText = "Imagen cargada: " + input.files[0].name;
   }
}

function aceptarComprobante() {
   const input = document.getElementById('subir');
   
   if (!input.files || input.files.length === 0) {
      alert("Debes adjuntar una foto del comprobante.");
      return;
   }
   
   // Vuelve a la pasarela dejando la imagen adjunta en el formulario
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
         document.getElementById("verMes").textContent = datos.meses ?? '0';
         document.getElementById("verMonto").textContent = "$" + (datos.monto ?? '0');
         document.getElementById("verEstado").textContent = (datos.estadopago ?? 'Impago');

         desactivarBotones(datos.estadopago);
      }
   })
   .catch(error => console.error("Error al obtener los datos del pago:", error));
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
   } else if (estado === 'Impago') {
      if (btnPago) btnPago.disabled = true;
      if (btnComprobante) btnComprobante.disabled = false;
      if (btnFactura) btnFactura.disabled = true;
   }
}
  // Se ejecuta cuando todo el HTML y el nav.php han sido cargados en la pantalla
document.addEventListener('DOMContentLoaded', () => {
   cargarPagos();
});

function cargarPagos() {
   const inputUsuario = document.getElementById('usuario');
   const idUsuario = inputUsuario ? inputUsuario.value : 0;

   if (!idUsuario || idUsuario === "0" || idUsuario === "") {
      console.error("No se encontró el ID de usuario en el nav.");
      return;
   }

   fetch('API.php', { 
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
         recurso: 'registroPagos', 
         consulta: 'Readusuarios', 
         id_usuario: parseInt(idUsuario) 
      })
   })
   .then(response => response.json())
   .then(data => {
      const tbody = document.getElementById('tablapagos');
      if (!tbody) return;
      tbody.innerHTML = '';

      if (!Array.isArray(data) || data.length === 0) {
         tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Los administradores no poseen registros de cobro de cuotas.</td></tr>';
         return;
      }

      data.forEach(pago => { 
         const fila = document.createElement('tr');
         fila.innerHTML = `
            <td>${pago.meses ?? ''}</td>
            <td>${pago.monto ?? ''}</td>
            <td>${pago.fecha ?? ''}</td>
            <td>${pago.estadopago ?? ''}</td>
         `;

         // Al hacer clic en la fila, envía el ID a la pasarela de pago
         fila.addEventListener('click', () => {
            const inputIdPago = document.getElementById('id_pago');
            if (inputIdPago) {
               inputIdPago.value = pago.id;
               inputIdPago.closest('form').submit();
            }
         });

         tbody.appendChild(fila);
      });
   })
   .catch(error => console.error('Error al cargar la tabla de pagos:', error));
}
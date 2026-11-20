function escapeHtml(str) {
   if (str === null || str === undefined) return '';
   return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
}

// Se ejecuta cuando todo el HTML y el nav.php han sido cargados en la pantalla
document.addEventListener('DOMContentLoaded', () => {
   cargarPagos();
});

function cargarPagos() {
   const inputUsuario = document.getElementById('usuario');
   const idUsuario = inputUsuario ? inputUsuario.value : 0;
   const tbody = document.getElementById('tablapagos');

   if (!tbody) return;

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
   .then(response => {
      if (!response.ok) throw new Error("Error HTTP: " + response.status);
      return response.json();
   })
   .then(data => {
      tbody.innerHTML = '';

      if (!Array.isArray(data) || data.length === 0) {
         tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Los administradores no poseen registros de cobro de cuotas.</td></tr>';
         return;
      }

      data.forEach(pago => { 
         const fila = document.createElement('tr');
         fila.innerHTML = `
            <td>${escapeHtml(pago.meses ?? '')}</td>
            <td>${escapeHtml(pago.monto ?? '')}</td>
            <td>${escapeHtml(pago.fecha ?? '')}</td>
            <td>${escapeHtml(pago.estadopago ?? '')}</td>
         `;

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
   .catch(error => { 
      tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Error al cargar tabla de pagos.</td></tr>';
      console.error(error);
   });
}
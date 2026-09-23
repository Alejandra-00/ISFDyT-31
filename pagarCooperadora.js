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
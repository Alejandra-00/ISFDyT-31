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
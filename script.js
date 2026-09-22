function CambioColor(apartado) {
   //Selecciono todos los elementos del nav.
   const elementos = document.querySelectorAll('nav a');

   //Recorro todos los elementos y quito la clase "activo".
   elementos.forEach(elementos => elementos.classList.remove('activo'));

   //Aplico la clase "activo" al elemento clickeado.
   apartado.classList.add('activo');
}

function toggleMenu(event) {
   event.preventDefault();
   event.stopPropagation();
   document.querySelector('.dropdown-menu').classList.toggle('mostrar');
}

document.addEventListener('click', function(event) {
   if (!event.target.closest('.dropdown')) {
      document.querySelector('.dropdown-menu').classList.remove('mostrar');
   }
});

function verClave(boton) {
   // Selecciona la imagen dentro del botón que recibió el clic
   const icono = boton.querySelector("img");
   // Selecciona el input de contraseña que está en el mismo contenedor
   const input = boton.parentElement.querySelector(".input-password");

   if (input.type === "password") {
      input.type = "text";
      icono.src = "iconos/noOjo.png";
   } else {
      input.type = "password";
      icono.src = "iconos/ojo.png";
   }
}

function mostrarCarrera() {
   const socioSelect = document.getElementById("socio").value;
   const carreraDiv = document.getElementById("carrera");
   if (socioSelect === "1") {
      carreraDiv.disabled = false;
   } else {
      carreraDiv.disabled = true;
   }

}

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
function CambioColor(apartado) {
   //Selecciono todos los elementos del nav.
   const elementos = document.querySelectorAll('nav a');

   //Recorro todos los elementos y quito la clase "activo".
   elementos.forEach(elementos => elementos.classList.remove('activo'));

   //Aplico la clase "activo" al elemento clickeado.
   apartado.classList.add('activo');
}

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
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
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

// Espera a que todo el HTML del DOM esté cargado y listo antes de ejecutar el JS
document.addEventListener("DOMContentLoaded", () => {

    // Selección de elementos del DOM por sus IDs
    const btnOlvido = document.getElementById("btnOlvido");         // Enlace/Botón "Olvidé mi contraseña"
    const modalOlvido = document.getElementById("modalOlvido");     // Contenedor principal del modal
    const cerrarModal = document.getElementById("cerrarModal");     // Botón 'X' para cerrar el modal
    const formOlvido = document.getElementById("formOlvido");       // Formulario de recuperación de clave
    const mensajeModal = document.getElementById("mensajeModal");   // Párrafo para mostrar respuestas/errores

    // Evento para abrir el modal
    btnOlvido.addEventListener("click", (e) => {
        e.preventDefault();                // Evita que la página recargue o navegue al hacer clic en el enlace
        mensajeModal.textContent = "";     // Limpia mensajes previos
        modalOlvido.style.display = "flex"; // Muestra la ventana modal usando flexbox
    });

    // Evento para cerrar el modal al hacer clic en la 'X'
    cerrarModal.addEventListener("click", () => {
        modalOlvido.style.display = "none"; // Oculta el modal
    });

    // Evento para cerrar el modal al hacer clic en el fondo oscuro (fuera del cuadro blanco)
    window.addEventListener("click", (e) => {
        if (e.target === modalOlvido) {
            modalOlvido.style.display = "none"; // Oculta el modal si el clic fue en el overlay
        }
    });

    // Evento para procesar el envío del formulario mediante Fetch (AJAX)
    formOlvido.addEventListener("submit", async (e) => {
        e.preventDefault(); // Evita el envío tradicional de formularios PHP (sin recargar la página)
        
        // Obtiene el valor del campo DNI introducido por el usuario
        const dniInput = document.getElementById("dniOlvido").value;
        
        // Crea un objeto FormData para estructurar la petición como si fuera un formulario HTML regular
        const formData = new FormData();
        formData.append("dni", dniInput); // Asigna la clave 'dni' que leerá PHP como $_POST['dni']

        // Feedback visual inmediato para indicar que la petición se está realizando
        mensajeModal.style.color = "black";
        mensajeModal.textContent = "Procesando envío...";

        try {
            // Realiza la petición asíncrona mediante POST a 'enviarMail.php'
            const respuesta = await fetch("enviarMail.php", {
                method: "POST",
                body: formData
            });

            // Lee la respuesta plana del servidor en formato texto
            const textoRespuesta = await respuesta.text();

            // Verifica si el código de estado HTTP indica éxito (200-299)
            if (respuesta.ok) {
                mensajeModal.style.color = "black";
                mensajeModal.textContent = textoRespuesta; // Muestra el mensaje de éxito de PHP
            } else {
                mensajeModal.style.color = "red";
                mensajeModal.textContent = textoRespuesta || "Ocurrió un error al procesar.";
            }
        } catch (error) {
            // Maneja fallos de red o errores inesperados en el cliente
            mensajeModal.style.color = "red";
            mensajeModal.textContent = "Error de conexión con el servidor.";
        }
    });
});
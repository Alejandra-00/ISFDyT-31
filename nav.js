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
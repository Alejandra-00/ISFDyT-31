function mostrarCarrera() {
   const socioSelect = document.getElementById("socio").value;
   const carreraDiv = document.getElementById("carrera");
   if (socioSelect === "1") {
      carreraDiv.disabled = false;
   } else {
      carreraDiv.disabled = true;
   }

}
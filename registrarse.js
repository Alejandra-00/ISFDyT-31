function mostrarCarrera() {
   const socioSelect = document.getElementById("socio").value;
   const carreraDiv = document.getElementById("carrera");
   if (socioSelect === "1") {
      carreraDiv.disabled = false;
      carreraSelect.required = true;
   } else {
      carreraDiv.disabled = true;
      carreraSelect.required = false;
      carreraSelect.value = "";
   }
}
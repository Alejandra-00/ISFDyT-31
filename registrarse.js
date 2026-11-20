function mostrarCarrera() {
   const socioSelect = document.getElementById("socio").value;
   const carreraSelect = document.getElementById("carrera");
   if (socioSelect === "1") {
      carreraSelect.disabled = false;
      carreraSelect.required = true;
   } else {
      carreraSelect.disabled = true;
      carreraSelect.required = false;
      carreraSelect.value = "";
   }
}

document.addEventListener("DOMContentLoaded", () => {
   mostrarCarrera();
});
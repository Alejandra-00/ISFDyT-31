<?php 
session_start();
include ('conexion.php'); 
$msjemail = ""; //Verifica los campos vacios
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $dni = trim($_POST["dni"]); 
    $nombre = trim($_POST["nombre_completo"]); 
    $email = trim($_POST["email"]); 
    $telefono = trim($_POST["telefono"]); 
    $contrasena = trim($_POST["contrasena"]);
    $activo = 1; // Establece el valor de activo en 1 (activo) 
    $socio = trim($_POST["socio"]); 
    $carrera = trim($_POST["carrera"]); 

if (empty($dni) || empty($nombre) || empty($email) || empty($telefono) || empty($contrasena)) { die("Todos los campos son obligatorios"); } //Verifica el correo 

$resultado = $conexion -> query("SELECT * FROM usuarios WHERE email='$email'"); 
if (mysqli_num_rows($resultado)>0) { $msjemail = "El correo ya esta registrado."; } 
else { // Encripta la contraseña e inserta usuarios 
$contraEncriptada = password_hash($contrasena, PASSWORD_DEFAULT); 
$sql_insert= "INSERT INTO usuarios(dni, nombre, email, telefono, contra, activo, socio, carrera) VALUES ('$dni','$nombre','$email','$telefono','$contraEncriptada', $activo, '$socio', '$carrera')";

if ($conexion->query($sql_insert) === TRUE) { // Iniciar sesión automáticamente 
    $_SESSION['usuario'] = $nombre; header("location: index.php"); 
} 
else { echo "Error: ".mysqli_error($conexion); } } } 
?> 


<!DOCTYPE html> 
<html lang="en"> 
    <head> 
        <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
         <title>Registrarse</title>
          <link rel="stylesheet" href="registrarse.css"> 
          </head> 
          
<body> 
    <div class="container"> 
        <form method="POST" action="registrarse.php" class="form">
             <h2>REGISTRARSE</h2> 
             <div class="campo"> 
                <label>DNI</label>
                 <input type="text" name="dni" placeholder="Ingrese su DNI" required> 
            </div> 
            <div class="campo">
                 <label>NOMBRE COMPLETO</label> 
                 <input type="text" name="nombre_completo" placeholder="Ingrese su nombre completo" required> 
                 </div> 
            <div class="campo email"> 
                <label>E-MAIL</label> 
                <input type="email" name="email" placeholder="Ingrese su e-mail" required>
                 </div>
             <div class="campo"> 
                <label>TIPO DE SOCIO</label>
                 <select name="socio" required>
                     <option value="">Seleccione socio</option> 
                     <option value="1">Alumno</option> 
                     <option value="2">Voluntario</option> 
                     <option value="3">Administrador</option> 
                     </select> 
                     </div> 
                <div class="campo"> 
                    <label>CARRERA</label>
                     <select name="carrera" required> 
                        <option value="">Seleccione carrera</option> 
                        <option value="1">Tecnica/o Superior en Administracion de PyMES</option> 
                        <option value="2">Tecnica/o Superior en Acompañamiento Terapeutico</option> 
                        <option value="3">Tecnica/o Superior en Produccion Agricola Ganadera</option> 
                        <option value="4">Profesor/a de Educacion Inicial</option>
                         <option value="5">Profesor/a de Educacion Primaria</option> 
                         <option value="6">Profesor/a de Educacion Fisica</option> 
                         <option value="7">Tecnica/o Superior en Psicopedagogia</option> 
                         <option value="8">Tecnica/o Superior en Trabajo Social</option> 
                         <option value="9">Enfermero/a</option> 
                         </select> 
                    </div> 
                <div class="campo"> 
                    <label>CONTRASEÑA</label> 
                    <input type="password" name="contrasena" placeholder="Ingrese su contraseña" required>
                </div> <div class="campo"> 
                    <label>TELÉFONO</label> 
                    <input type="tel" name="telefono" placeholder="Ingrese su teléfono" required> 
                </div> 
                
            <button type="submit">CREAR CUENTA</button> 
            
            <p class="login"> ¿Ya tienes una cuenta? <a href="iniciarSesion.php">¡Inicia sesión!</a> </p>
        </form> 
    </div> 
</body> 
</html> 
<?php 
$conexion->close(); 
?>
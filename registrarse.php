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
        <form method="POST" action="API.php" class="form">
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
                 <select id="socio" name="socio" required onchange="mostrarCarrera()" >
                     <option disabled selected hidden>¿Qué tipo de voluntario sos?</option>
                      <?php 
                            $sql_socio = "SELECT * FROM socio";
                            $resultadosocio = mysqli_query($conexion, $sql_socio);
                            while ($row = $resultadosocio->fetch_assoc()): ?>
                            <option value="<?= $row['id']?>"><?= $row['nombre']?></option>
                         <?php endwhile; ?>
                     </select> 
                     </div> 
                <div class="campo"> 
                    <label>CARRERA</label>
                     <select id="carrera" name="carrera" required> 
                        <option class="op" disabled selected hidden>Seleccione su carrera</option>
                        <?php 
                            $sql_carrera = "SELECT * FROM carrera";
                            $resultadoCarrera = mysqli_query($conexion, $sql_carrera);
                            while ($row = $resultadoCarrera->fetch_assoc()): ?>
                            <option value="<?= $row['id']?>"><?= $row['nombre']?></option>
                         <?php endwhile; ?>
                    </select> 
                    </div> 
                <div class="campo"> 
                    <label>CONTRASEÑA</label> 
                    <input type="password" name="contrasena" placeholder="Ingrese su contraseña" required>
                </div> <div class="campo"> 
                    <label>TELÉFONO</label> 
                    <input type="tel" name="telefono" placeholder="Ingrese su teléfono" required> 
                </div> 
             <input type="text" hidden name="recurso" value="usuarios">
            <input type="text" hidden name="consulta" value="Create">
           <button type="submit">CREAR CUENTA</button> 
            
            <p class="login"> ¿Ya tienes una cuenta? <a href="iniciarSesion.php">¡Inicia sesión!</a> </p>
        </form> 
    </div> 
</body> 
<script src="script.js"></script>
</html> 
<?php
?>

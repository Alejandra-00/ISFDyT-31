<?php
    include("conexion.php");
    session_start();

    $mensajeError = '';

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $dni = $_POST["dni"];
        $nombreCompleto = $_POST["nombre_completo"];
        $mail = $_POST["email"];
        $socio = $_POST["socio"] ?? ''; 
        $carrera = $_POST["carrera"] ?? '';
        $contrasena = $_POST["contrasena"];
        $telefono = $_POST["telefono"];
        $recurso = $_POST["recurso"];
        $consulta = $_POST["consulta"];

        $datos = [
            "mensaje" => "Usuario recibido correctamente",
            "dni" => $dni,
            "nombre_completo" => $nombreCompleto,
            "email" => $mail,
            "socio" => $socio,
            "carrera" => $carrera,
            "contrasena" => $contrasena,
            "telefono" => $telefono,
            "recurso" => $recurso,
            "consulta" => $consulta
        ];

        // Convertir a JSON
        $payload = json_encode($datos);

        // Configurar la petición HTTP hacia el otro archivo local
        $opciones = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => "POST",
                "content"  => $payload,
                "ignore_errors" => true
            ]
        ];

        $contexto = stream_context_create($opciones);
        // URL del archivo que va a recibir (ajusta la ruta según tu proyecto), enviar los datos y obtener la respuesta
        $resultado = file_get_contents("http://localhost/ISFDyT-31/API.php", false, $contexto);
        $respuesta = json_decode($resultado, true);

        if (isset($respuesta['mensaje'])) {
            $_SESSION['usuario'] = $dni;
            header("Location: inicio.php");
            exit;
        } else {
            $mensajeError = "Todos los campos son obligatorios.";
        }
    }
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
    <div class="EsquinaCirculo"></div>
    <img src="curvas/curva.png" alt="" class="EsquinaCurva">
    
    <div class="container"> 
        <form method="POST" action="" class="form">
            <h2>REGISTRARSE</h2> 
            <div class="campo"> 
                <label>DNI</label>
                <input type="text" name="dni" placeholder="Ingrese su DNI"> 
            </div> 
            <div class="campo">
                <label>NOMBRE COMPLETO</label> 
                <input type="text" name="nombre_completo" placeholder="Ingrese su nombre completo"> 
            </div> 
            <div class="campo email"> 
                <label>E-MAIL</label> 
                <input type="email" name="email" placeholder="Ingrese su e-mail">
            </div>
            <div class="campo"> 
                <label>TIPO DE SOCIO</label>
                <select id="socio" name="socio" onchange="mostrarCarrera()" >
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
                        <select id="carrera" name="carrera"> 
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
                    <input type="password" name="contrasena" placeholder="Ingrese su contraseña">
                </div> 
                <div class="campo"> 
                    <label>TELÉFONO</label> 
                    <input type="tel" name="telefono" placeholder="Ingrese su teléfono"> 
                </div> 

                <?php if (!empty($mensajeError)): ?>
                <div class="error"><?= htmlspecialchars($mensajeError) ?></div>
                <?php endif; ?>

                <input type="text" hidden name="recurso" value="usuarios">
                <input type="text" hidden name="consulta" value="Create">
                <button type="submit">CREAR CUENTA</button> 
            
            <p class="login"> ¿Ya tienes una cuenta? <a href="iniciarSesion.php">¡Inicia sesión!</a> </p>
        </form> 
    </div> 
    <script src="script.js"></script>
</body> 
</html> 
<?php
    $conexion->close();
?>
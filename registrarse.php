<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include("conexion.php");

    $mensajeError = '';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Validar y asignar valores por defecto en caso de venir vacíos
        $dni = trim($_POST["dni"] ?? '');
        $nombreCompleto = trim($_POST["nombre_completo"] ?? '');
        $mail = trim($_POST["email"] ?? '');
        $socio = !empty($_POST["socio"]) ? $_POST["socio"] : '1'; 
        $carrera = !empty($_POST["carrera"]) ? $_POST["carrera"] : '10';
        $contrasena = $_POST["contrasena"] ?? '';
        $telefono = trim($_POST["telefono"] ?? '');
        $recurso = $_POST["recurso"] ?? 'usuarios';
        $consulta = $_POST["consulta"] ?? 'Create';

        $datos = [  //declarar los datos a enviar a la API
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

        // Construir la URL codificando correctamente espacios y caracteres especiales
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $rutaLimpia = implode('/', array_map('rawurlencode', explode('/', dirname($_SERVER['PHP_SELF']))));
        $urlApi = $protocolo . $host . $rutaLimpia . "/API.php";

        // Configurar la petición HTTP hacia la API
        $opciones = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => "POST",
                "content" => $payload,
                "ignore_errors" => true
            ]
        ];

        $contexto = stream_context_create($opciones);
        $resultado = @file_get_contents($urlApi, false, $contexto);

        if ($resultado !== false) {
            $respuesta = json_decode($resultado, true);

            if (isset($respuesta['mensaje'])) {
                $_SESSION['id'] = $respuesta['idUsuario'];
                $_SESSION['usuario'] = $respuesta['usuario'];
                $_SESSION['admin'] = $respuesta['admin'] ?? 0;
                header("Location: inicio.php");
                exit;
            } else {
                $mensajeError = $respuesta['error'] ?? "Respuesta de la API: " . htmlspecialchars($resultado);
            }
        } else {
            $mensajeError = "No se pudo realizar la llamada HTTP a la API.";
        }
        
    }
?>
<!DOCTYPE html> 
<html lang="es"> 
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Registrarse</title>
    <link rel="stylesheet" href="registrarse.css"> 
</head> 
<body> 
    <div class="EsquinaCirculo"></div>
    <img src="curvas/curva.png" alt="" class="EsquinaCurva">
    
     <div class="FondoVerde"></div>
    <img src="curvas/curva2.png" alt="" class="fondoAzul">
    
    <div class="ContenedorLogo">
    <img src="iconos/logo.jpg" alt="" class="Logo">
    </div>
    <div class="Cooperadora">
    <h1><span class="Verde">A</span>SOCIACIÓN <span class="Verde">C</span>OOPERADORA</h1>
    </div>

    <div class="Instituto">
    <h2>Instituto Superior de Formación Docente y Técnica n°31</h2>
    </div>

    <div class="ContenedorLogoNegro">
    <img src="iconos/logoNegro.png" alt="" class="LogoNegro">
    </div>

    <div class="ISFDYT"><span class="Verde">ISFDYT N°31</span></div>
    <div class="Necochea">Necochea</div>

    <div class="Contenedor"> 
        <form method="POST" action="" class="form">
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
                <select id="socio" name="socio" onchange="mostrarCarrera()" required>
                    <option value="" disabled selected hidden>¿Qué tipo de voluntario sos?</option>
                    <?php 
                        $sql_socio = "SELECT * FROM socio";
                        $resultadosocio = mysqli_query($conexion, $sql_socio);
                        while ($row = $resultadosocio->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                    <?php endwhile; ?>
                </select> 
            </div> 

            <div class="campo"> 
                <label>CARRERA</label>
                <select id="carrera" name="carrera" required> 
                    <option value="" disabled selected hidden>Seleccione su carrera</option>
                    <?php 
                        $sql_carrera = "SELECT * FROM carrera";
                        $resultadoCarrera = mysqli_query($conexion, $sql_carrera);
                        while ($row = $resultadoCarrera->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                    <?php endwhile; ?>
                </select> 
            </div> 

            <div class="campo"> 
                <label>CONTRASEÑA</label> 
                <input type="password" name="contrasena" placeholder="Ingrese su contraseña" required>
            </div> 

            <div class="campo"> 
                <label>TELÉFONO</label> 
                <input type="tel" name="telefono" placeholder="Ingrese su teléfono" required> 
            </div> 

            <?php if (!empty($mensajeError)): ?>
                <div class="error" style="color: red; margin-bottom: 10px;"><?= htmlspecialchars($mensajeError) ?></div>
            <?php endif; ?>

            <input type="hidden" name="recurso" value="usuarios">
            <input type="hidden" name="consulta" value="Create">
            
            <button type="submit">CREAR CUENTA</button> 

            <p class="login"> ¿Ya tienes una cuenta? <a href="iniciarSesion.php">¡Inicia sesión!</a> </p>
        </form> 
    </div> 
    <script src="registrarse.js"></script>
</body> 
</html> 
<?php
$conexion->close();
?>
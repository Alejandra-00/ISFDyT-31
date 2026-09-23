<?php
    include('conexion.php');
    session_start();

    if($_SERVER["REQUEST_METHOD"] === "POST" ) {
        $dni = trim($_POST["dni"] ?? '');
        $contrasena = $_POST["contrasena"] ?? '';
        $recurso = $_POST["recurso"] ?? 'usuarios';
        $consulta = $_POST["consulta"] ?? 'Login';

        $datos = [ //declarar los datos a enviar a la API
            "mensaje" => "Usuario recibido correctamente",
            "dni" => $dni,
            "contrasena" => $contrasena,
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
        $resultado = @file_get_contents($urlApi, false, $contexto);

        if ($resultado !== false) {
            $respuesta = json_decode($resultado, true);

            if (isset($respuesta['mensaje'])) {
                $_SESSION['id'] = $respuesta['idUsuario'];
                $_SESSION['usuario'] = $respuesta['usuario'];
                header('Location: inicio.php');
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="iniciarSesion.css">   
    <title>Iniciar sesión</title>
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
        <h2>Instituto Superior de Formación Docente y Técnica N°31</h2>
    </div>

    <div class="ContenedorLogoNegro">
        <img src="iconos/logoNegro.png" alt="" class="LogoNegro">
    </div>
    
    <div class="ISFDYT"><span class="Verde">ISFDYT N°31</span></div>
    <div class="Necochea">Necochea</div>

    <div class="ContenedorFormulario">
        <form class="formulario" action="" method="POST">
            <h2 class="titulo">INICIAR SESIÓN</h2>

            <div class="grupoInput">
                <label for="dni" class="label">DNI</label>
                <input type="text" name="dni" maxlength="8" placeholder="Ingrese su DNI" class="input">
            </div>
            
            <div class="grupoInput">
                <label for="contraseña" class="label">CONTRASEÑA</label>
                <div class="contra">
                    <input type="password" name="contrasena" placeholder="Ingrese su contraseña" class="input input-password">
                    <i class="ojo" onclick="verClave(this)"><img id="iconoOjo" src="iconos/ojo.png" alt=""></i>
                </div>
                <p class="linkOlvido"><a href="olvideContrasena">Olvidé mi contraseña</a></p>
                <?php if (isset($error)): ?>
                    <p class="error"><?= $error ?></p>
                <?php endif; ?>
            </div>

            <input type="text" hidden name="recurso" value="usuarios">
            <input type="text" hidden name="consulta" value="Login">
            
            <button class="btn">Iniciar sesión</button>
            <p class="linkRegistro">¿No tienes una cuenta?<a href="registrarse.php">¡Registrate!</a></p>
        </form>
    </div>
    <script src="iniciarSesion.js"></script>
</body>
</html>
<?php 
    $conexion->close();
?> 
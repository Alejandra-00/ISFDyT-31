<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include('conexion.php');

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
                $_SESSION['admin'] = $respuesta['admin'] ?? 0;
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
    
    <div class="fondoAzul-Celular"><img src="curvas/curva2 - celular.png" alt="fondoCelular"></div>
    <div class="FondoVerde">
        <img src="curvas/curva2.png" alt="" class="fondoAzul">
        
        <div class="ContenedorLogo">
            <img src="iconos/logo.jpg" alt="" class="Logo">
        
            <div class="TextosLogo">
                <h1 class="Cooperadora"><span class="Verde">A</span>SOCIACIÓN <span class="Verde">C</span>OOPERADORA</h1>
                <h2 class="Instituto">Instituto Superior de Formación Docente y Técnica n°31</h2>
            </div>
        </div>

        <div class="ContenedorLogoNegro">
            <img src="iconos/logoNegro.png" alt="" class="LogoNegro">

            <div class="ISFDYT"><span class="Verde">ISFDYT N°31</span></div>
            <div class="Necochea">Necochea</div>
        </div>

    <div class="ContenedorFormulario">
        <form class="formulario" action="" method="POST">
            <h2 class="titulo">INICIAR SESIÓN</h2>

            <div class="grupoInput">
                <label for="dni" class="label">DNI</label>
                <input type="text" name="dni" maxlength="8" minlength="8" placeholder="Ingrese su DNI" class="input">
            </div>
            
            <div class="grupoInput">
                <label for="contraseña" class="label">CONTRASEÑA</label>
                <div class="contra">
                    <input type="password" name="contrasena" placeholder="Ingrese su contraseña" class="input input-password">
                    <i class="ojo" onclick="verClave(this)"><img id="iconoOjo" src="iconos/ojo.png" alt=""></i>
                </div>
                <p class="linkOlvido"><a href="#" id="btnOlvido">Olvidé mi contraseña</a></p>
                <?php if (isset($mensajeError)): ?>
                    <p class="error"><?= $mensajeError ?></p>
                <?php endif; ?>
            </div>

            <input type="text" hidden name="recurso" value="usuarios">
            <input type="text" hidden name="consulta" value="Login">
            
            <button class="btn">Iniciar sesión</button>
            <p class="linkRegistro">¿No tienes una cuenta?<a href="registrarse.php">¡Registrate!</a></p>
        </form>
    </div>

    <div id="modalOlvido" class="modal-olvido" style="display: none;">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="cerrarModal">&times;</span>
            <h3 style="text-align: center; margin-bottom: 10px; font-family: 'Tamrin'; font-size: 18px;">Recuperar Contraseña</h3>
            <p style="text-align: center; font-size: 12px; margin-bottom: 15px; color: black; font-family: 'Tamrin';">
                Ingrese su DNI. Se restablecerá la contraseña y se enviarán los datos a su correo electrónico.
            </p>
            
            <form id="formOlvido">
                <div class="grupoInput">
                    <label for="dniOlvido" class="label">DNI</label>
                    <input type="text" id="dniOlvido" name="dni" maxlength="8" minlength="8" required placeholder="Ingrese su DNI" class="input">
                </div>
                <button type="submit" class="btn" style="margin-top: 15px;">Enviar correo</button>
            </form>
            
            <p id="mensajeModal"></p>
        </div>
    </div>

    <script src="iniciarSesion.js"></script>
</body>
</html>
<?php 
    $conexion->close();
?> 
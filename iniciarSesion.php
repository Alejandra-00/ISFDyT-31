<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="iniciarSesion.css">
    <link rel="stylesheet" href="https://cloudflare.com">   
    <title>Iniciar sesión</title>
</head>
<body>
    <div class="EsquinaCirculo"></div>
    <img src="curvas/curva.png" alt="" class="EsquinaCurva">
    <div class="FondoVerde"></div>
    <img src="curvas/curva2.png" alt="" class="fondoAzul">
    <div class="ContenedorLogo"><img src="iconos/logo.jpg" alt="" class="Logo"></div>
    <div class="Cooperadora">
        <h1><span class="Verde">A</span>SOCIACIÓN <span class="Verde">C</span>OOPERADORA</h1>
    </div>
    <div class="Instituto">
        <h2>instituto superior de formación docente y técnica n°31</h2>
    </div>
    <div class="ContenedorLogoNegro"><img src="iconos/logoNegro.png" alt="" class="LogoNegro"></div>
    <div class="ISFDYT"><span class="Verde">ISFDYT N°31</span></div>
    <div class="Necochea">Necochea</div>
    <div class="ContenedorFormulario">
        <form class="formulario" action="API.php/iniciarSesion" method="POST">
            <h2 class="titulo">INICIAR SESIÓN</h2>
            <div class="grupoInput">
                <label for="dni" class="label">DNI</label>
                <input type="text" maxlength="8" placeholder="Ingrese su DNI" class="input">
            </div>
            <div class="grupoInput">
                <label for="contraseña" class="label">CONTRASEÑA</label>
                <div class="contra">
                    <input type="password" placeholder="Ingrese su contraseña" class="input input-password">
                    <i class="fa-solid fa-eye toggle-icon" onclick="verClave(this)"><img id="iconoOjo" src="iconos/ojo.png" alt=""></i>
                </div>
                <p class="linkOlvido"><a href="olvideContrasena">Olvidé mi contraseña</a></p>
            </div>

            <!--<input type="text" hidden id="recurso" value="registroPagos">-->
            <!--<input type="text" hidden id="consulta" value="Update"-->
            <button class="btn">Iniciar sesión</button>
            <p class="linkRegistro">¿No tienes una cuenta?<a href="registrarse.php">¡Registrate!</a></p>
        </form>
    </div>
</div>
<script src="script.js"></script>
</body>
</html> 
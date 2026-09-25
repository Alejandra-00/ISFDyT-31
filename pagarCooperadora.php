<?php
    include 'conexion.php';
    // Inicia la sesión únicamente si no se ha iniciado antes
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(!isset($_SESSION['usuario']) || !isset($_SESSION['id'])){
        header('Location: inicio.php');
        exit;
    }
    $id_usuario = $_SESSION['id'];

    $idPago = $_POST['id_pago'];

    $mensajeExito = '';
    $errorAPI = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="pagarCooperadora.css">
    <title>Pasarela de pagos</title>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="seccionPagos">
        <!-- Contenedor pasarela -->
        <div class="contenedor activo" id="pasarela">
            <h1 class="fuente titulo" id="verMes">Cargando...</h1>

            <div class="info">
                <div class="datos">
                    <p class="fuente">Monto a pagar</p>
                    <p class="fuente" id="verMonto">0</p>
                    <p id="alias" onclick="copiarElemento('alias')" class="fuente">Cooperadora.31</p>
                    <p id="cvu" onclick="copiarElemento('cvu')" class="fuente">0140354901617701138618</p>
                </div>
                <div class="estado">
                    <p class="fuente" id="verEstado"></p>
                </div>
            </div>

            <input type="hidden" id="idPagoActual" value="<?php echo htmlspecialchars($idPago); ?>">

            <div class="linea"></div>
            <div class="descargar">
                <button type="button" id="BtnDescargarFactura">
                    <span class="fuente" style="display: flex; align-items: center;">
                        <img src="iconos/descargar.png" alt="Descarga">
                        Descargar factura
                    </span>
                </button>
            </div>

            <div class="linea"></div>
            <div class="enviar">
                <button type="button" onclick="mostrar('enviarComprobante')" id="BtnEnviarComprobante">
                    <span class="fuente" style="display: flex; align-items: center;">
                        <img src="iconos/enviar.png" alt="Enviar">
                        Enviar comprobante de pago
                    </span>
                </button>
                <!-- Formulario POST que incluye el input del comprobante -->
                <form action="pagarCooperadora.php" method="POST" enctype="multipart/form-data" class="formulario" id="formPasarela">
                    
                    <input type="file" id="subir" name="comprobante" accept="image/*" style="display: none;" onchange="comprobanteSeleccionado()">
                    <button type="button" class="fuente btn" onclick="mostrar('pasarela', 'cuotaPendiente')" id="BtnEnviarPago">Enviar pago</button>
                </form>
            </div>   
        </div> 

        <!-- Contenedor enviar comprobante -->
        <div class="contenedor" id="enviarComprobante">
            <h1 class="fuente titulo">Enviar comprobante de pago</h1>
            
            <div class="cargar">
                <label for="subir" class="cargarImagen">
                    <img src="iconos/cargar.png" alt="Cargar">
                    <span class="fuente texto" id="textoSubir">Subir foto del comprobante</span>
                </label>
            </div>
            <button type="button" class="fuente btnEnviar" onclick="aceptarComprobante()">Aceptar</button>
        </div>

        <!-- Contenedor cuota pendiente -->
        <div class="contenedor" id="cuotaPendiente">
            <h1 class="fuente titulo">Comprobante de pago</h1>

            <div class="info">
                <div class="estado">
                    <p class="fuente">Estado</p>
                </div>
                <div class="datos">
                    <p class="fuente">Te informaremos cuando el pago haya sido validado.</p>
                </div>
            </div>

            <div class="linea"></div>
            <div class="enviar">
                <button type="button" onclick="mostrar('enviarComprobante')">
                    <span class="fuente" style="display: flex; align-items: center;">
                        <img src="iconos/enviar.png" alt="Enviar">
                        Enviar comprobante de pago
                    </span>
                </button>
            </div>
        </div>
    </div>
    <script src="pagarCooperadora.js"></script>
</body>
</html>
<?php
    $conexion->close();
?>
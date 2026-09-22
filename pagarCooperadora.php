<?php
    include 'conexion.php';
    //session_start();
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
            <h1 class="fuente titulo">Mes</h1>

            <div class="info">
                <div class="datos">
                    <p class="fuente">Monto a pagar</p>
                    <p class="fuente">$$$$</p>
                    <p id="alias" onclick="copiarElemento('alias')" class="fuente">Cooperadora.31</p>
                    <p id="cvu" onclick="copiarElemento('cvu')" class="fuente">0140354901617701138618</p>
                </div>
                <div class="estado">
                    <p class="fuente">Estado</p>
                </div>
            </div>

            <div class="linea"></div>
            <div class="descargar">
                <button>
                    <span class="fuente" style="display: flex; align-items: center;">
                        <img src="iconos/descargar.png" alt="Descarga">
                        Descargar factura
                    </span>
                </button>
            </div>

            <div class="linea"></div>
            <div class="enviar">
                <button type="button" onclick="mostrar('enviarComprobante')">
                    <span class="fuente" style="display: flex; align-items: center;">
                        <img src="iconos/enviar.png" alt="Enviar">
                        Enviar comprobante de pago
                    </span>
                </button>
                <button type="button" class="fuente btn" onclick="mostrar('pasarela', 'cuotaPendiente')">Enviar pago</button>
            </div>
        </div> 

        <!-- Contenedor enviar comprobante -->
        <div class="contenedor" id="enviarComprobante">
            <h1 class="fuente titulo">Enviar comprobante de pago</h1>
            
            <div class="cargar">
                <label for="subir" class="cargarImagen">
                    <img src="iconos/cargar.png" alt="Cargar">
                    <span class="fuente texto">Subir foto del comprobante</span>
                    <input type="file" id="subir" accept="image/*">
                </label>
            </div>
            <button type="button" class="fuente btnEnviar" onclick="mostrar('pasarela')">Enviar</button>
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
    //$conexion->close();
?>
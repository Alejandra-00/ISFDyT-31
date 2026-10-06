<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="inicio.css">
</head>
<body>
    <section class="inicio">
        <?php include 'nav.php' ?>
        <div class="montos">
            <section class="card">
                <p>
                    Monto actual<br>
                    <strong id="montoActual"></strong>
                </p>
            </section>
            <section class="card">
                <p>
                    Monto anterior<br>
                    <strong id="montoAnterior"></strong>
                </p>
            </section>
        </div>
    </section>
    <section class="preguntas">
        <!-- contenido de preguntas -->
        <div class="preguntas_contenido">
            <h1>Preguntas frecuentes</h1>
            <div class="preguntas_contenedor">
                <!-- Card 1 -->
                <div class="pregunta-flip">
                    <div class="pregunta-inner">
                        <div class="pregunta-front">
                            ¿Tenes hermanos?
                        </div>
                        <div class="pregunta-back">
                            Se registra uno y paga por los dos
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="pregunta-flip">
                    <div class="pregunta-inner">
                        <div class="pregunta-front">
                            ¿Dejás de pagar la cooperadora?
                        </div>
                        <div class="pregunta-back">
                            Acercate a bibliota y solicitá en dirección darte de baja del sistema
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="pregunta-flip">
                    <div class="pregunta-inner">
                        <div class="pregunta-front">
                            ¿Tu DNI es incorrecto?
                        </div>
                        <div class="pregunta-back">
                            Solicita darte de baja en biblioteca
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer_separacion">
            <?php include 'footer.php'; ?>
        </div>
    </section>
    <script src="inicio.js"></script>
</body>
</html>
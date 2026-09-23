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
                    <strong>$$$$</strong>
                </p>
            </section>
            <section class="card">
                <p>
                    Monto anterior<br>
                    <strong>$$$$</strong>
                </p>
            </section>
        </div>
    </section>
    <section class="preguntas">
        <!-- contenido de preguntas -->
        <div class="preguntas_contenido">
            <h1>Preguntas frecuentes</h1>
            <div class="preguntas_contenedor">
                <div class="pregunta">
                    Si tenes hermanos acercate a biblioteca
                </div>
                <div class="pregunta">
                    Dejar de pagar cooperadora
                </div>
                <div class="pregunta">
                    Si tu DNI es incorrecto solicita darte de baja en biblioteca
                </div>
            </div>
        </div>
        <div class="footer_separacion">
            <?php include 'footer.php'; ?>
        </div>
    </section>
</body>
</html>
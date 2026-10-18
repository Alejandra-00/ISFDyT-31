<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Define la codificacion de caracteres UTF-8. -->
    <meta charset="UTF-8">
    <!-- Configura el ancho adaptable para dispositivos moviles. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Define el titulo que aparece en la pestana del navegador. -->
    <title>Document</title>
    <!-- Carga la hoja de estilos local del footer. -->
    <link rel="stylesheet" href="footer.css">
    <!-- Carga Font Awesome para mostrar los iconos de redes sociales. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Cierra la seccion de configuracion del documento. -->
</head>
<!-- Abre el contenido visible de la pagina. -->
<body>
    <!-- Abre el pie de pagina y aplica los estilos de la clase footer. -->
   <footer class="footer">
    <!-- Agrupa el contenido principal del pie de pagina. -->
    <section class="section">
        <!-- Abre el bloque que contiene el logo y el texto lado a lado -->
        <div class="logo">
            <!-- Contiene la imagen circular del logo. -->
            <div class="ContenedorLogo">
                <img src="iconos/logo.jpg" alt="" class="Logo">
            </div>

            <!-- Abre el bloque que contiene SOLO los textos identificativos. -->
            <div class="logo-identificacion" id="footer-text">
                <div class="Cooperadora">
                    <h1>ASOCIACIÓN COOPERADORA</h1>
                </div>
                <div class="Instituto">
                    <h2>Instituto Superior de Formación Docente y Técnica N°31</h2>
                </div>
            </div>
        </div>

        <!-- Abre el bloque independiente con la informacion de contacto. -->
        <div class="footer-content">
            <h3>Contactanos</h3>
            <h4>Direccion: Avenida Jesuita Cardiel N°2130, Necochea. <br>
                Horarios: Lunes a viernes de 08.00 a 21:30. <br>
                Email: cooperadoraisfd31@gmail.com <br>
            </h4>
        </div>
    </section>

    <!-- Redes sociales AFUERA de section para que queden abajo de todo y centradas -->
    <div class="social-icons">
        <a href="https://www.facebook.com/profile.php?id=100063628540272" target="_blank" class="social-icon" title="Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://www.instagram.com/instituto_31?utm_source=ig_web_button_share_sheet&srtk=ZDNlZDc0MzIxNw==" target="_blank" class="social-icon" title="Instagram">
            <i class="fab fa-instagram"></i>
        </a>
    </div>
</footer>
</body>
</html>
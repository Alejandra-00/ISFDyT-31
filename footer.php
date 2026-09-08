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
            <!-- Abre el bloque que contiene el logo, los textos y el contacto. -->
            <div class="logo">
                <!-- Contiene la imagen circular del logo. -->
                <div class="ContenedorLogo">
                    <!-- Muestra la imagen del logo y aplica la clase Logo. -->
                    <img src="iconos/logo.jpg" alt="" class="Logo">
                <!-- Cierra el contenedor de la imagen. -->
                </div>
                <!-- Abre el bloque que contiene los textos identificativos. -->
                <div id="footer-text">
                    <!-- Abre el bloque del nombre de la cooperadora. -->
                    <div class="Cooperadora">
                        <!-- Muestra el nombre principal de la cooperadora. -->
                        <h1>ASOCIACIÓN COOPERADORA</h1>
                    <!-- Cierra el bloque de la cooperadora. -->
                    </div>
                    <!-- Abre el bloque del nombre del instituto. -->
                    <div class="Instituto">
                        <!-- Muestra el nombre del instituto. -->
                        <h2>instituto superior de formación docente y técnica n°31</h2>
                    <!-- Cierra el bloque del instituto. -->
                    </div>
                <!-- Cierra el bloque de textos identificativos. -->
                </div>

                <!-- Abre el bloque con la informacion de contacto. -->
                <div class="footer-content">
                    <!-- Muestra el titulo de la informacion de contacto. -->
                    <h3>Contactanos</h3>
                    <!-- Muestra la direccion, los horarios y el correo electronico. -->
                    <h4>Direccion: Avenida Jesuita Cardiel N°2130, Necochea <br>
                        <!-- Inserta un salto de linea entre la direccion y los horarios. -->
                        Horarios: Lunes a viernes de 08.00 a 21:30 <br>
                        <!-- Inserta un salto de linea antes del correo electronico. -->
                        Email: cooperadoraisfd31@gmail.com
                    <!-- Cierra el encabezado que contiene los datos de contacto. -->
                    </h4>
                <!-- Cierra el bloque de contacto. -->
                </div>
            <!-- Cierra el bloque principal del logo y su informacion. -->
            </div>

            <!-- Abre el contenedor de los iconos de redes sociales. -->
            <div class="social-icons">
                    <!-- Abre el enlace hacia Facebook en una pestana nueva. -->
                    <a href="https://www.facebook.com" target="_blank" class="social-icon" title="Facebook">
                        <!-- Muestra el icono de Facebook mediante Font Awesome. -->
                        <i class="fab fa-facebook-f"></i>
                    <!-- Cierra el enlace de Facebook. -->
                    </a>
                    <!-- Abre el enlace hacia Instagram en una pestana nueva. -->
                    <a href="https://www.instagram.com" target="_blank" class="social-icon" title="Instagram">
                        <!-- Muestra el icono de Instagram mediante Font Awesome. -->
                        <i class="fab fa-instagram"></i>
                    <!-- Cierra el enlace de Instagram. -->
                    </a>

            </div>
        </section>
    </footer>
</body>
</html>
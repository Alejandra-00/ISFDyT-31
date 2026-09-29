<?php
$url_login = 'https://ejemplo.com/login';

// Incluir conexion a bbdd (conexion.php)
require 'conexion.php';

// Incluir manualmente los archivos necesarios según la estructura de tu carpeta
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Importar los namespaces reales de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true);

//TRAER DATOS DE BBDD
$dniForm = $_POST['dni'];
$sql = "SELECT nombre_completo, dni, email FROM usuarios WHERE dni = '".$dniForm."'";

if (mysqli_num_rows($resultado) > 0) {
   $resultado = mysqli_query($conexion, $sql);
   $datos = mysqli_fetch_asssoc($resultado);
   $emailUsuario = $datos['email'];
   $nombre = $datos['nombre_completo'];
   $dni = $datos['DNI'];

   $contraEncriptada = password_hash($dni, PASSWORD_BCRYPT);
   $sqlResetContrasenia = "UPDATE usuarios SET contrasena = '".$contraEncriptada."' WHERE DNI = '".$dni."'";
   $resultadoReset = mysqli_query($conexion, $sqlResetContrasenia);

    try {
        // --- Configuración del Servidor SMTP ---
        $mail->isSMTP();                                            // Usar SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Servidor SMTP
        $mail->SMTPAuth   = true;                                 // Habilitar autenticación SMTP
        $mail->Username   = 'tu_correo@gmail.com';                // Tu correo de Gmail
        $mail->Password   = 'tu_contraseña_de_aplicacion';       // Tu contraseña de aplicación (16 caracteres)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Cifrado TLS
        $mail->Port       = 587;                                  // Puerto TCP

        // --- Remitente y Destinatario ---
        $mail->setFrom('tu_correo@gmail.com', 'Tu Nombre o Empresa'); //NOMBRE DEL INSTITUTO
        $mail->addAddress($emailUsuario, $nombre); //correo del usuario, nombre del usuario

        // --- Contenido del Correo ---
        $mail->isHTML(true);                                  // Formato HTML
        $mail->Subject = 'Reseteo de Contraseña - Instituto 31';
        $mail->Body    = '<h2>¡Hola, ' . $nombre . '!</h2>
                            <p>Tu contraseña ha sido restablecida con éxito.</p>
                            <p>A partir de este momento, tu nueva clave de acceso provisional es tu <b>número de DNI</b> (sin puntos ni espacios).</p>
                            <p><b>Recomendación de seguridad:</b> Te sugerimos iniciar sesión y cambiar esta contraseña provisional desde la sección de configuración de tu perfil lo antes posible.</p>
                            <p><a href="' . $url_login . '">Hacer clic aquí para iniciar sesión</a></p>
                            <hr>
                            <p><small>Si no solicitaste este cambio, por favor ponte en contacto con nuestro equipo de soporte de inmediato.</small></p>';
        $mail->AltBody = 'Hola ' . $nombre . ', tu contraseña ha sido restablecida correctamente. Tu nueva clave de acceso temporal es tu número de DNI (sin puntos ni espacios). 
        Por seguridad, te recomendamos cambiarla al ingresar a la plataforma: ' . $url_login;

        // --- Enviar ---
        $mail->send();
        echo 'El mensaje ha sido enviado correctamente.';
    } catch (Exception $e) {
        echo "Error al enviar el mensaje. Detalle: {$mail->ErrorInfo}";
    }
} else {
    //error si no hay usuario registrado con ese dni 
}

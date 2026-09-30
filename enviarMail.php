<?php
// Forzar encabezado en UTF-8 para que las respuestas del fetch lean bien eñes y tildes
header("Content-Type: text/html; charset=UTF-8");

$url_login = 'localhost/ISFDyT-31/iniciarSesion.php';

// Incluir conexion a bbdd (conexion.php)
require 'conexion.php';

//Asegurar que la base de datos devuelva caracteres en UTF-8
mysqli_set_charset($conexion, "utf8mb4");

// Incluir manualmente los archivos necesarios según la estructura de tu carpeta
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Importar los namespaces reales de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dniForm = trim($_POST['dni'] ?? '');

    if (empty($dniForm)) {
        http_response_code(400);
        echo "Debe ingresar un DNI válido.";
        exit;
    }

    //TRAER DATOS DE BBDD
    $sql = "SELECT nombre_completo, DNI, email FROM usuarios WHERE DNI = '".$dniForm."'";
    $resultado = mysqli_query($conexion, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $datos = mysqli_fetch_assoc($resultado);
        $emailUsuario = $datos['email'];
        $nombre = $datos['nombre_completo'];
        $dni = $datos['DNI'];

        // Encriptar DNI como clave provisional
        $contraEncriptada = password_hash($dni, PASSWORD_BCRYPT);
        $sqlResetContrasenia = "UPDATE usuarios SET contrasena = '".$contraEncriptada."' WHERE DNI = '".$dni."'";
        $resultadoReset = mysqli_query($conexion, $sqlResetContrasenia);

        $mail = new PHPMailer(true);

        try {
            // Configuración DE UTF-8
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // Configuración del Servidor SMTP
            $mail->isSMTP();                                            // Usar SMTP
            $mail->Host       = 'smtp.gmail.com';                     // Servidor SMTP
            $mail->SMTPAuth   = true;                                 // Habilitar autenticación SMTP
            $mail->Username   = 'cooperadoraisfd31@gmail.com';                // Tu correo de Gmail
            $mail->Password   = 'oytq mfud xkjc ebdl';       // Tu contraseña de aplicación (16 caracteres)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Cifrado TLS
            $mail->Port       = 587;                                  // Puerto TCP

            // Remitente y Destinatario 
            $mail->setFrom('cooperadoraisfd31@gmail.com', 'Instituto N°31'); //NOMBRE DEL INSTITUTO
            $mail->addAddress($emailUsuario, $nombre); //correo del usuario, nombre del usuario

            // Contenido del Correo 
            $mail->isHTML(true);                                  // Formato HTML
            $mail->Subject = 'Reestablecer Contraseña - Instituto 31';
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
              <meta charset="UTF-8">
            </head>
            <body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 30px 10px;">
                <tr>
                  <td align="center">
                    <!-- Tarjeta Principal -->
                    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 520px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                      
                      <!-- Encabezado con Verde Institucional -->
                      <tr>
                        <td style="background-color: #0d8a42; padding: 25px 20px; text-align: center;">
                          <h1 style="color: #ffffff; margin: 0; font-size: 20px; letter-spacing: 1px; font-weight: 700;">
                            Instituto Superior de Formación Docente y Técnica N°31
                          </h1>
                          <p style="color: #e0f2e9; margin: 5px 0 0 0; font-size: 13px;">
                            Asociación Cooperadora
                          </p>
                        </td>
                      </tr>

                      <!-- Contenido -->
                      <tr>
                        <td style="padding: 30px 25px; color: #333333;">
                          <h2 style="color: #2c3e50; font-size: 18px; margin-top: 0; margin-bottom: 15px;">
                            ¡Hola, ' . htmlspecialchars($nombre) . '!
                          </h2>
                          <p style="font-size: 14px; line-height: 1.6; color: #555555; margin-bottom: 20px;">
                            Hemos procesado tu solicitud de restablecimiento de contraseña correctamente.
                          </p>

                          <!-- Caja destacada con la clave provisional -->
                          <div style="background-color: #f0fdf4; border-left: 4px solid #0d8a42; padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                            <p style="margin: 0; font-size: 13px; color: #166534;">
                              <b>Tu nueva clave de acceso provisional es:</b>
                            </p>
                            <p style="margin: 8px 0 0 0; font-size: 18px; font-weight: bold; color: #0d8a42; letter-spacing: 1px;">
                              ' . htmlspecialchars($dni) . '
                            </p>
                            <p style="margin: 5px 0 0 0; font-size: 11px; color: #15803d;">
                              (Tu número de DNI sin puntos ni espacios)
                            </p>
                          </div>

                          <!-- Botón de acción -->
                          <div style="text-align: center; margin: 30px 0;">
                            <a href="' . $url_login . '" style="background-color: #0d8a42; color: #ffffff; padding: 12px 28px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 6px; display: inline-block;">
                              Iniciar Sesión Ahora
                            </a>
                          </div>

                          <p style="font-size: 12px; line-height: 1.5; color: #777777; margin-bottom: 0;">
                            <b>Recomendación de seguridad:</b> Te sugerimos cambiar esta contraseña provisional una vez ingreses a la plataforma desde tu perfil.
                          </p>
                        </td>
                      </tr>

                      <!-- Pie de página -->
                      <tr>
                        <td style="background-color: #f9fafb; padding: 15px 25px; border-top: 1px solid #edf2f7; text-align: center;">
                          <p style="margin: 0; font-size: 11px; color: #9aa0a6;">
                            Si no solicitaste este cambio, por favor ponte en contacto con nuestro equipo de soporte.
                          </p>
                        </td>
                      </tr>

                    </table>
                  </td>
                </tr>
              </table>
            </body>
            </html>';

            // Enviar 
            $mail->send();
            http_response_code(200);
            echo "El correo de recuperación se envió correctamente.";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error al enviar el correo. Intente más tarde. {$mail->ErrorInfo}";
        }
    } else {
        http_response_code(404);
        echo "No existe un usuario registrado con el DNI ingresado.";
    }
} else {
    http_response_code(405);
    echo "Método no permitido.";
}

$conexion->close();
?>
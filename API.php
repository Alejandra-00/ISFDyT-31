<?php
// Desactivar la impresión de warnings/errors HTML en la salida JSON
ini_set('display_errors', 0);
error_reporting(0);

// Limpiar cualquier buffer o espacio en blanco previo
if (ob_get_length()) ob_clean();

include("conexion.php");

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonRecibido = file_get_contents('php://input');
    $datos = json_decode($jsonRecibido, true);

    if (json_last_error() === JSON_ERROR_NONE) {

        $recurso = $datos['recurso'] ?? '';
        $consulta = $datos['consulta'] ?? '';
        
        switch ($recurso) {
            
            case 'registroPagos':
                switch ($consulta) {
                    case 'Readusuarios':
                        $id_usuarios = (int)($datos['id_usuario'] ?? 0);

                        if ($id_usuarios === 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "El usuario no se encontró."]);
                            break;
                        }

                        $sql = "SELECT 
                            registropagos.id, 
                            registropagos.fecha, 
                            monto.importe AS monto, 
                            estadopago.nombre AS estadopago, 
                            meses.nombre AS meses
                            FROM registropagos
                            LEFT JOIN monto ON registropagos.id_monto = monto.id
                            LEFT JOIN estadopago ON registropagos.id_estado = estadopago.id
                            LEFT JOIN meses ON registropagos.id_mes = meses.id_mes
                            WHERE registropagos.id_usuarios = ?
                        ";

                        $stmt = mysqli_prepare($conexion, $sql);

                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "i", $id_usuarios);
                            if (mysqli_stmt_execute($stmt)) {
                                $resultado = mysqli_stmt_get_result($stmt);
                                $pagos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                                echo json_encode($pagos);
                            } else {
                                http_response_code(500);
                                echo json_encode(["error" => "Error al ejecutar consulta SQL."]);
                            }
                            mysqli_stmt_close($stmt);
                        }
                    break;
                    
                    case 'Readpagos':
                        $id_pago = (int)($datos['id_pago'] ?? 0);

                        if ($id_pago === 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "El pago no se encontró."]);
                            break;
                        }

                        $sql = "SELECT 
                            registropagos.id, 
                            registropagos.fecha, 
                            monto.importe AS monto, 
                            estadopago.nombre AS estadopago, 
                            meses.nombre AS meses,
                            usuarios.nombre_completo,
                            usuarios.DNI,
                            usuarios.email
                            FROM registropagos
                            LEFT JOIN usuarios ON registropagos.id_usuarios = usuarios.id
                            LEFT JOIN monto ON registropagos.id_monto = monto.id
                            LEFT JOIN estadopago ON registropagos.id_estado = estadopago.id
                            LEFT JOIN meses ON registropagos.id_mes = meses.id_mes
                            WHERE registropagos.id = ?
                        ";

                        $stmt = mysqli_prepare($conexion, $sql);

                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "i", $id_pago);
                            mysqli_stmt_execute($stmt);
                            $resultado = mysqli_stmt_get_result($stmt);
                            $pago = mysqli_fetch_assoc($resultado);
                            
                            if ($pago) {
                                echo json_encode($pago);
                            } else {
                                http_response_code(404);
                                echo json_encode(["error" => "El pago no fue encontrado en la base de datos."]);
                            }
                            mysqli_stmt_close($stmt);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error al ejecutar la consulta SQL."]);
                        }
                    break;

                    case 'UpdatePago':
                        $id_pago = (int)($datos["id_pago"] ?? 0);
                        $id_estado = (int)($datos["id_estado"] ?? 0);
                        $fotoBase64 = $datos["foto"] ?? ""; 
                        $fecha = date('Y-m-d');

                        if ($id_pago === 0 || empty($fotoBase64)) {
                            http_response_code(400);
                            echo json_encode(["error" => "Faltan datos obligatorios (ID o Imagen)."]);
                            break;
                        }

                        // Decodificar Base64 a binario para guardarlo en MEDIUMBLOB
                        if (strpos($fotoBase64, ',') !== false) {
                            @list(, $fotoBase64) = explode(',', $fotoBase64);
                        }
                        $blobData = base64_decode($fotoBase64);

                        if ($blobData === false) {
                            http_response_code(400);
                            echo json_encode(["error" => "No se pudo procesar el formato de la imagen."]);
                            break;
                        }

                        // Insertar imagen en comprobante
                        $stmtFoto = $conexion->prepare("INSERT INTO comprobante (foto) VALUES (?)");
                        if ($stmtFoto) {
                            $stmtFoto->bind_param("s", $blobData);
                            
                            if ($stmtFoto->execute()) {
                                $id_comprobante = $stmtFoto->insert_id;
                                $stmtFoto->close();

                                // Actualizar registropagos con el nuevo comprobante y estado
                                $stmtPago = $conexion->prepare("UPDATE registropagos SET id_estado = ?, fecha = ?, id_comprobante = ? WHERE id = ?");
                                if ($stmtPago) {
                                    $stmtPago->bind_param("isii", $id_estado, $fecha, $id_comprobante, $id_pago);
                                    
                                    if ($stmtPago->execute()) {
                                        echo json_encode(["mensaje" => "Pago actualizado correctamente"]);
                                    } else {
                                        http_response_code(500);
                                        echo json_encode(["error" => "Error al actualizar la tabla de pagos."]);
                                    }
                                    $stmtPago->close();
                                }
                            } else {
                                http_response_code(500);
                                echo json_encode(["error" => "Error MySQL al guardar la imagen: " . $stmtFoto->error]);
                                $stmtFoto->close();
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error al preparar la consulta SQL: " . $conexion->error]);
                        }
                    break;

                    case 'Read':
                        $sql = "SELECT 
                            registropagos.id,
                            registropagos.id_estado,
                            usuarios.nombre_completo,
                            monto.importe,
                            estadopago.nombre AS estado,
                            TO_BASE64(comprobante.foto) AS foto_base64,
                            socio.nombre AS tipo_socio,
                            carrera.nombre AS carrera,
                            meses.nombre AS mes,
                            registropagos.fecha
                        FROM registropagos
                        LEFT JOIN usuarios ON registropagos.id_usuarios = usuarios.id
                        LEFT JOIN monto ON registropagos.id_monto = monto.id
                        LEFT JOIN estadopago ON registropagos.id_estado = estadopago.id
                        LEFT JOIN comprobante ON registropagos.id_comprobante = comprobante.id
                        LEFT JOIN socio ON usuarios.id_socio = socio.id
                        LEFT JOIN meses ON registropagos.id_mes = meses.id_mes
                        LEFT JOIN carrera ON usuarios.id_carrera = carrera.id";

                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            $pagos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            echo json_encode($pagos);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error de lectura en la base de datos."]);
                        }
                    break;

                    case 'Create':
                        // Extraemos y casteamos los IDs necesarios desde $datos
                        $id_usuario = (int)($datos['id_usuario'] ?? 0);
                        $id_monto = (int)($datos['id_monto'] ?? 0);
                        $id_estado = (int)($datos['id_estado'] ?? 0);
                        $id_comprobante = (int)($datos['id_comprobante'] ?? 0);

                        if ($id_usuario === 0 || $id_monto === 0 || $id_estado === 0 || $id_comprobante === 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "Faltan IDs obligatorios para registrar el pago."]);
                            exit;
                        }

                        $stmt = $conexion->prepare("INSERT INTO registropagos (id_usuarios, id_monto, id_estado, id_comprobante) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("iiii", $id_usuario, $id_monto, $id_estado, $id_comprobante);

                        if ($stmt->execute()) {
                            echo json_encode(["mensaje" => "Pago almacenado con éxito."]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error: No se pudo registrar el pago."]);
                        }
                        $stmt->close();
                    break;

                    case 'Update':
                        $id_pago = (int)($datos["id_pago"] ?? 0);
                        $id_estado = (int)($datos["id_estado"] ?? 0);

                        $stmt = $conexion->prepare("UPDATE registropagos SET id_estado = ? WHERE id = ?");
                        $stmt->bind_param("ii", $id_estado, $id_pago);

                        if ($stmt->execute()) {
                            echo json_encode(["mensaje" => "Estado actualizado correctamente."]);
                        } else {
                            http_response_code(502);
                            echo json_encode(["error" => "Error: Estado no actualizado."]);
                        }
                        $stmt->close();
                    break;

                    case 'ExportarExcelPagos':
                        $fechaInicio = $datos['fechaInicio'];
                        $fechaFinal = $datos['fechaFinal'];

                        if (empty($fechaInicio) || empty($fechaFinal)) {
                            http_response_code(400);
                            echo json_encode(["error" => "Debe ingresar una fecha de inicio y una fecha de fin."]);
                            break;
                        }

                        $sql = "SELECT 
                            usuarios.DNI,
                            usuarios.nombre_completo,
                            socio.nombre AS socio,
                            carrera.nombre AS carrera,
                            monto.importe AS monto,
                            registropagos.fecha
                        FROM registropagos
                        INNER JOIN usuarios ON registropagos.id_usuarios = usuarios.id
                        INNER JOIN monto ON registropagos.id_monto = monto.id
                        LEFT JOIN socio ON usuarios.id_socio = socio.id
                        LEFT JOIN carrera ON usuarios.id_carrera = carrera.id
                        WHERE registropagos.fecha BETWEEN ? AND ?
                        AND registropagos.id_estado = 1";

                        $stmt = mysqli_prepare($conexion, $sql);

                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "ss", $fechaInicio, $fechaFinal);
                            mysqli_stmt_execute($stmt);
                            $resultado = mysqli_stmt_get_result($stmt);
                            $datosExcel = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

                            http_response_code(200);
                            echo json_encode($datosExcel);
                            mysqli_stmt_close($stmt);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error al exportar datos de pagos."]);
                        }
                    break;
                        
                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                    break;
                }
            break; 
                
            case "usuarios":
                switch ($consulta) {
                    case "usuario":
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        // Obtener ID desde el JSON o desde la sesión PHP como fallback
                        $id_usuario = (int)($datos['id_usuario'] ?? $_SESSION['id'] ?? 0);
                        if ($id_usuario === 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "ID de usuario no proporcionado o sesión inválida."]);
                            break;
                        }
                        $sql = "SELECT 
                                    u.id,
                                    u.DNI,
                                    u.nombre_completo,
                                    u.email,
                                    u.telefono,
                                    u.activo,
                                    u.admin,
                                    s.nombre AS nombre_socio,
                                    c.nombre AS nombre_carrera
                                FROM usuarios u
                                LEFT JOIN socio s ON u.id_socio = s.id
                                LEFT JOIN carrera c ON u.id_carrera = c.id
                                WHERE u.id = ?";

                            $stmt = $conexion->prepare($sql);
                            if ($stmt) {
                                $stmt->bind_param("i", $id_usuario);
                                $stmt->execute();
                                $resultado = $stmt->get_result();
                                $usuario = $resultado->fetch_assoc();

                                if ($usuario) {
                                    echo json_encode($usuario);
                                } else {
                                    http_response_code(404);
                                    echo json_encode(["error" => "Usuario no encontrado."]);
                                }
                                $stmt->close();
                            } else {    
                                http_response_code(500);
                                echo json_encode(["error" => "Error en la consulta."]);
                            }
                    break;

                    case "socios":
                        $id_socio = $datos['id_socio'] ?? null;
                        $sql ="SELECT usuarios.id, usuarios.nombre_completo, usuarios.email, usuarios.telefono, usuarios.DNI, usuarios.activo, socio.nombre AS nombre_socio, carrera.nombre AS nombre_carrera
                               FROM usuarios 
                               INNER JOIN socio ON usuarios.id_socio = socio.id
                               INNER JOIN carrera ON usuarios.id_carrera = carrera.id";
                           
                           if ($id_socio) {
                               $sql .= " WHERE usuarios.id_socio = " . intval($id_socio);
                           }
                           $resultado = mysqli_query($conexion, $sql);
                           if ($resultado) {
                               $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                               echo json_encode($usuarios);
                           } else {
                               http_response_code(502);
                               echo json_encode(["error" => "Error de lectura."]);
                           }
                    break;
                    
                    case "Read":
                        $sql = "SELECT usuarios.id, usuarios.nombre_completo, usuarios.email, usuarios.telefono, usuarios.DNI, usuarios.activo, socio.nombre AS nombre_socio, carrera.nombre AS nombre_carrera
                                FROM usuarios 
                                INNER JOIN socio ON usuarios.id_socio = socio.id
                                INNER JOIN carrera ON usuarios.id_carrera = carrera.id";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            echo json_encode($usuarios);
                        } else {
                            http_response_code(502);
                            echo json_encode(["error" => "Error de lectura."]);
                        }
                    break;

                    case "Create":
                        $camposObligatorios = ['dni', 'nombre_completo', 'email', 'socio', 'carrera', 'contrasena', 'telefono'];
                        $errores = [];

                        foreach ($camposObligatorios as $campo) {
                            if (trim($datos[$campo] ?? '') === '') {
                                $errores[] = $campo;
                            }
                        }

                        if (!empty($errores)) {
                            http_response_code(400);
                            echo json_encode(['error' => 'Campos requeridos vacíos: ' . implode(', ', $errores)]);
                            exit;
                        }

                        $DNI = trim($datos["dni"]);
                        $nombre_completo = trim($datos["nombre_completo"]);
                        $email = trim($datos["email"]);
                        $telefono = trim($datos["telefono"]);
                        $contrasena = password_hash($datos["contrasena"], PASSWORD_BCRYPT); // Encriptación segura de contraseña
                        $id_socio = (int)$datos["socio"];
                        $id_carrera = (int)$datos["carrera"];
                        $activo = 1;

                        // Comprobar si el email o DNI ya existen con Prepared Statement
                        $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? OR DNI = ?");
                        $stmt_check->bind_param("ss", $email, $DNI);
                        $stmt_check->execute();
                        $res_check = $stmt_check->get_result();

                        if ($res_check->num_rows > 0) {
                            http_response_code(409);
                            echo json_encode(["error" => "El email o DNI ya se encuentra registrado."]);
                            $stmt_check->close();
                            exit;
                        }
                        $stmt_check->close();

                        // Inserción segura de usuario
                        $stmt = $conexion->prepare("INSERT INTO usuarios (DNI, nombre_completo, email, telefono, contrasena, activo, id_socio, id_carrera) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssiii", $DNI, $nombre_completo, $email, $telefono, $contrasena, $activo, $id_socio, $id_carrera);

                        if ($stmt->execute()) {
                            $nuevoId = $stmt->insert_id;

                            echo json_encode([
                                "mensaje" => "Usuario registrado correctamente.",
                                "idUsuario" => $nuevoId,
                                "usuario" => $nombre_completo,
                                "admin" => 0
                            ]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error: Cuenta no registrada."]);
                        }
                        $stmt->close();
                    break;

                    case "Update":
                        $id = $datos["id"];
                        $DNI = $datos["dni"];
                        $nombre_completo = $datos["nombre_completo"];
                        $email = $datos["email"];
                        $telefono = $datos["telefono"];
                        $contrasena = $datos["contrasena"];
                        $id_socio = $datos["socio"];
                        $id_carrera = $datos["carrera"];
                        $contrasena = password_hash($contrasena, PASSWORD_BCRYPT); // Encriptación segura de contraseña 
                        $sql = "UPDATE usuarios
                                SET DNI = '$DNI',
                                    nombre_completo = '$nombre_completo',
                                    email = '$email',
                                    telefono = '$telefono',
                                    contrasena = '$contrasena',
                                    id_socio = $id_socio,
                                    id_carrera = $id_carrera
                                WHERE id = $id";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode([
                                "mensaje" => "Usuario actualizado correctamente."
                            ]);
                        } else {
                            http_response_code(500);
                            echo json_encode([
                                "error" => "Error: Usuario no actualizado."
                            ]);
                        }
                    break;

                     case "ReadByDNI":
                        $dni = trim($datos["dni"] ?? '');
                        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE DNI = ?");
                        $stmt->bind_param("s", $dni);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($fila = $resultado->fetch_assoc()) {
                            echo json_encode($fila);
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Usuario no encontrado."]);
                        }
                        $stmt->close();
                    break;

                    case "UpdateDNI":
                        $id = (int)$datos["id"];
                        $dni = trim($datos["dni"]);

                        // Verificar que el nuevo DNI no esté en uso por otro usuario
                        $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE DNI = ? AND id != ?");
                        $stmt_check->bind_param("si", $dni, $id);
                        $stmt_check->execute();
                        $res_check = $stmt_check->get_result();

                        if ($res_check->num_rows > 0) {
                            http_response_code(409);
                            echo json_encode(["error" => "El DNI ya se encuentra registrado."]);
                            $stmt_check->close();
                            break;
                        }
                        $stmt_check->close();

                        $stmt = $conexion->prepare("UPDATE usuarios SET DNI = ? WHERE id = ?");
                        $stmt->bind_param("si", $dni, $id);
                        $stmt->execute();
                        if ($stmt->affected_rows >= 0) {
                            echo json_encode(["mensaje" => "DNI actualizado correctamente."]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error al actualizar el DNI."]);
                        }
                        $stmt->close();
                    break;


                    case "Login":
                        $dni = trim($datos["dni"] ?? '');
                        $contrasena = $datos["contrasena"] ?? '';

                        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE DNI = ? AND activo = 1");
                        $stmt->bind_param("s", $dni);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($fila = $resultado->fetch_assoc()) {
                            // Verificación de hash seguro
                            if (password_verify($contrasena, $fila['contrasena']) || $contrasena === $fila['contrasena']) { 
                                echo json_encode([
                                    "mensaje" => "Ok",
                                    "idUsuario" => $fila['id'],
                                    "usuario" => $fila['nombre_completo'],
                                    "admin" => $fila['admin'] ?? 0
                                ]);
                            } else {
                                http_response_code(401);
                                echo json_encode(["error" => "Contraseña incorrecta."]);
                            }
                        } else {
                            http_response_code(404);
                            echo json_encode(["error" => "Usuario no encontrado o inactivo."]);
                        }
                        $stmt->close();
                    break;

                      case "Inactivate":
                        $id = $datos["id"];
                        $sql = "UPDATE usuarios
                                SET activo = 0
                                WHERE id = $id";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode([
                                "mensaje" => "Usuario inactivo."
                            ]);
                        } else {
                            http_response_code(500);
                        }
                    break;

                    case "Activate":
                        $id = $datos["id"];
                        $sql = "UPDATE usuarios
                                SET activo = 1
                                WHERE id = $id";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode([
                                "mensaje" => "Usuario activo."
                            ]);
                        } else {
                            http_response_code(500);
                        }
                
                    break;

                    case "ExportarExcelUsuarios":
                        $sql = "SELECT 
                            usuarios.DNI,
                            usuarios.nombre_completo,
                            socio.nombre AS socio,
                            carrera.nombre AS carrera
                            FROM usuarios
                            LEFT JOIN socio ON usuarios.id_socio = socio.id
                            LEFT JOIN carrera ON usuarios.id_carrera = carrera.id
                        WHERE usuarios.activo = 1;";
                        
                        $resultado = mysqli_query($conexion, $sql);

                        if ($resultado) {
                            $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            http_response_code(200);
                            echo json_encode($usuarios);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error al exportar datos de usuarios."]);
                        }
                    break;

                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                    break;
                }
            break;

            case 'carreras':
                switch ($consulta) {
                    case 'Create':
                        $nombre = $datos["nombre"];
                        $sql = "INSERT INTO carrera (nombre) VALUES ('$nombre')";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode([ "mensaje" => "Carrera creada correctamente."
                        ]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "No se pudo crear la carrera."]);
                        }
                    break;

                    case 'Read':
                        $sql = "SELECT * FROM carrera";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode(mysqli_fetch_all($resultado, MYSQLI_ASSOC));
                        } else {
                            http_response_code(502);
                            echo json_encode(["error" => "Error de lectura."]);
                        }
                    break;  

                    case 'Delete':
                        $id = $datos["id"] ;
                        $sql = "DELETE FROM carrera WHERE id = $id";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode(["mensaje" => "Carrera eliminada con éxito."]);
                        } else {
                            http_response_code(502);
                            echo json_encode(["error" => "Error: Carrera no eliminada."]);
                        }
                    break;

                    case 'Update':
                        $id = $datos["id"];
                        $nombre = $datos["nombre"];
                        $sql = "UPDATE carrera SET nombre = '$nombre' WHERE id = $id";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode(["mensaje" => "Actualizado correctamente."]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error: Carrera no actualizada."]);
                        }
                    break;
                    
                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                    break;
                }
            break;

            case 'comprobantes':
                switch ($consulta) {
                    case 'Read':
                        $sql = "SELECT * FROM comprobante";
                        //INNER JOIN SIRVE PARA OBTENER ATRAVEZ DE CLAVES FORANEAS, LOS DATOS DE OTRAS TABLAS RELACIONADAS
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode(["mensaje" => "Ok."]);
                        } else {
                            http_response_code(502);
                            echo json_encode(["error" => "Error de lectura."]);
                        }
                    break;

                    case 'Create':
                        $foto = $datos["foto"] ?? "";
                        $sql = "INSERT INTO comprobante (foto) VALUES ('$foto')";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode(["mensaje" => "Comprobante almacenado con éxito."]);
                        } else {
                            http_response_code(502);
                            echo json_encode(["error" => "Error: Comprobante no almacenado."]);
                        }
                    break;

                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                    break;
                }
            break;

            case 'monto':
                switch ($consulta) {
                    case 'Inicio':
                        $sql = "SELECT importe, importe_anterior FROM monto ORDER BY fecha_efecto DESC 
                        LIMIT 1";
                        $resultado = mysqli_query($conexion, $sql);

                        if ($resultado) {
                            $monto = mysqli_fetch_assoc($resultado);
                            if ($monto) { echo json_encode($monto);
                            } else {
                                http_response_code(404);
                                echo json_encode(["error" => "No hay montos registrados."]);
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error de lectura."]);
                        }
                    break;

                    case 'Read':
                        $sql = "SELECT id, importe, importe_anterior, fecha_guardado, fecha_efecto FROM monto";
                        $resultado = mysqli_query($conexion, $sql);
                        $montos = [];
                        if ($resultado) {
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                $montos[] = $fila;
                            }
                        echo json_encode($montos);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error de lectura."]);
                        }
                    break;

                    case 'Update':
                        $id = $datos["id"];
                        $importe = $datos["importe"];
                        $importeAnterior = $datos["importe_anterior"];
                        $fechaEfecto = $datos["fecha_efecto"];
                        $sql = "UPDATE monto
                                SET importe = '$importe',
                                    importe_anterior = '$importeAnterior',
                                    fecha_efecto = '$fechaEfecto'
                                WHERE id = $id";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            echo json_encode(["mensaje" => "Monto actualizado correctamente."]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error de actualización."]);
                        }
                    break;

                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                    break;
                }
            break;

            case 'meses';
                switch($consulta) {
                    case "Read":
                        $sql = "SELECT nombre FROM meses";
                        $resultado = mysqli_query($conexion, $sql);
                        if ($resultado) {
                            $nombres = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                            echo json_encode($nombres);
                        } else {
                            http_response_code(502);
                            echo json_encode(["error" => "Error de lectura."]);
                        }
                    break;

                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                    break;
                }
            break;
            
            default:
                http_response_code(400);
                echo json_encode(["error" => "Recurso no válido"]);
            break;
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "El JSON enviado no es válido."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido."]);
} 

$conexion->close();
?>
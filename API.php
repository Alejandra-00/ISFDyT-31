<?php
include("conexion.php");

header("Content-Type: application/json; charset=UTF-8");

//contraseña de phpMailer: oytq mfud xkjc ebdl

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
                        if (ob_get_length()) ob_clean();

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
                            comprobante.foto, 
                            meses.nombre AS meses
                            FROM registropagos
                            LEFT JOIN usuarios ON registropagos.id_usuarios = usuarios.id
                            LEFT JOIN monto ON registropagos.id_monto = monto.id
                            LEFT JOIN estadopago ON registropagos.id_estado = estadopago.id
                            LEFT JOIN comprobante ON registropagos.id_comprobante = comprobante.id
                            LEFT JOIN meses ON registropagos.id_mes = meses.id_mes
                            WHERE registropagos.id_usuarios = ?
                        ";

                        $stmt = mysqli_prepare($conexion, $sql);

                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "i", $id_usuarios);
                            
                            if (mysqli_stmt_execute($stmt)) {
                                $resultado = mysqli_stmt_get_result($stmt);
                                $pagos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                                
                                http_response_code(200);
                                echo json_encode($pagos);
                            } else {
                                http_response_code(500);
                                echo json_encode(["error" => "Error SQL: " . mysqli_stmt_error($stmt)]);
                            }
                            
                            mysqli_stmt_close($stmt);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error al preparar SQL: " . mysqli_error($conexion)]);
                        }
                    break;
                    
                    case 'Readpagos':
                        $id_pago = (int)($datos['id_pago']);

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
                            
                            echo json_encode($pago);
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

                        if ($id_pago === 0) {
                            http_response_code(400);
                            echo json_encode(["error" => "El pago no se encontró."]);
                            break;
                        }

                        $stmtFoto = $conexion->prepare("INSERT INTO comprobante (foto) VALUES (?)");
                        if ($stmtFoto) {
                            $stmtFoto->bind_param("s", $fotoBase64);
                            
                            if ($stmtFoto->execute()) {
                                // Obtenemos el ID del comprobante que se acaba de crear
                                $id_comprobante = $stmtFoto->insert_id;
                                $stmtFoto->close();

                                // Actualizar registropagos con el nuevo id_comprobante, fecha e id_estado
                                $stmtPago = $conexion->prepare("UPDATE registropagos SET id_estado = ?, fecha = ?, id_comprobante = ? WHERE id = ?");
                                if ($stmtPago) {
                                    $stmtPago->bind_param("isii", $id_estado, $fecha, $id_comprobante, $id_pago);
                                    
                                    if ($stmtPago->execute()) {
                                        echo json_encode(["mensaje" => "Pago actualizado correctamente"]);
                                    } else {
                                        http_response_code(500);
                                        echo json_encode(["error" => "Error al actualizar el pago."]);
                                    }
                                    $stmtPago->close();
                                }
                            } else {
                                http_response_code(500);
                                echo json_encode(["error" => "Error al guardar el comprobante."]);
                                $stmtFoto->close();
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error al preparar la inserción del comprobante."]);
                        }
                        break;

                    case 'Read':
                                $sql = "SELECT
                                registropagos.id,
                                registropagos.id_estado,
                                usuarios.nombre_completo,
                                monto.importe,
                                estadopago.nombre AS estado,
                                comprobante.foto,
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
                            http_response_code(502);
                            echo json_encode(["error" => "Error de lectura."]);
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

                        $stmt = $conexion->prepare("INSERT INTO registropagos (id_usuario, id_monto, id_estado, id_comprobante) VALUES (?, ?, ?, ?)");
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
                        
                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Consulta no válida"]);
                    break;
                }
            break; 
                
            case "usuarios":
                switch ($consulta) {
                    case "usuario":
                        $sql = "SELECT * FROM usuarios WHERE id = ".$_SESSION["id"];
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
                        $sql = "DELETE FROM carrera WHERE id_carrera = $id";
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
                        $sql = "UPDATE carrera SET nombre = '$nombre' WHERE id_carrera = $id";
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
<?php
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
                    case 'Read':
                        $sql = "SELECT registroPagos.id_pago, usuarios.id_usuarios, monto.importe, estado.nombre AS estado, comprobante.foto
                                FROM registroPagos
                                INNER JOIN usuarios ON registroPagos.id_usuario = usuarios.id_usuarios
                                INNER JOIN monto ON registroPagos.id_monto = monto.id_monto
                                INNER JOIN estado ON registroPagos.id_estado = estado.id_estado
                                INNER JOIN comprobante ON registroPagos.id_comprobante = comprobante.id_comprobante";
                        
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

                        $stmt = $conexion->prepare("INSERT INTO registroPagos (id_usuario, id_monto, id_estado, id_comprobante) VALUES (?, ?, ?, ?)");
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

                        $stmt = $conexion->prepare("UPDATE registroPagos SET id_estado = ? WHERE id_pago = ?");
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
                            echo json_encode(["mensaje" => "Usuario registrado correctamente."]);
                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Error: Cuenta no registrada."]);
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
<?php

    header("Content-Type: application/json");

    //Conexión y error "500"
    $conexion = mysqli_connect("localhost", "root", "", "isfdyt-31");
    if(!$conexion){
        http_response_code(500);
        echo json_encode(["error" => "Error de conexión"]);
        exit;
    }
    mysqli_set_charset($conexion, "UTF8");

    $recurso = $_POST["recurso"] ?? "";
    $consulta = $_POST["consulta"] ?? "";
    $datos = json_encode(file_get_contents("php://input"), true);

    switch ($recurso) {
        case 'registroPagos':
            switch ($consulta) {
                case 'Read':
                    $sql = "SELECT registropagos.id_pago, usuarios.id_usuarios, monto.importe, estado.nombre AS estado, comprobante.foto
                    FROM registropagos
                    INNER JOIN usuarios
                        ON registropagos.id_usuario = usuarios.id_usuarios
                    INNER JOIN monto
                        ON registropagos.id_monto = monto.id_monto
                    INNER JOIN estado
                        ON registropagos.id_estado = estado.id_estado
                    INNER JOIN comprobante
                        ON registropagos.id_comprobante = comprobante.id_comprobante";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Ok."]);
                    } else {
                        http_response_code(502);
                        echo json_encode(["error" => "Error de lectura."]);
                    }
                break;

                case 'Create':
                    $select = "SELECT registropagos.id_pago, usuarios.id_usuarios, monto.importe, estado.nombre AS estado, comprobante.foto
                        FROM registropagos
                        INNER JOIN usuarios
                            ON registropagos.id_usuario = usuarios.id_usuarios
                        INNER JOIN monto
                            ON registropagos.id_monto = monto.id_monto
                        INNER JOIN estado
                            ON registropagos.id_estado = estado.id_estado
                        INNER JOIN comprobante
                            ON registropagos.id_comprobante = comprobante.id_comprobante";
                    $sql = "INSERT INTO registropagos (registropagos.id_usuario, registropagos.id_monto, registropagos.id_estado, registropagos.id_comprobante) VALUES ('$id_usuario', '$id_monto', '$id_estado', '$id_comprobante')";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Pago almacenado con exito."]);
                    } else {
                        http_response_code(402);
                        echo json_encode(["error" => "Error: Pago rechazado."]);
                    }
                break;

                case 'Update':
                    $id = $datos["estado"];
                    $sql = "UPDATE registropago SET estado = $id";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Actualizado correctamente."]);
                    } else {
                        http_response_code(502);
                        echo json_encode(["error" => "Error: Estado no actualizado."]);
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
                case "Read":
                    $sql = "SELECT
                        usuario.id,
                        usuario.nombre_completo,
                        usuario.email,
                        usuario.telefono,
                        usuario.DNI,
                        usuario.activo,
                        socio.nombre AS nombre_socio,
                        carrera.nombre AS nombre_carrera
                        FROM usuarios 
                        INNER JOIN socio ON usuarios.id_socio = socio.id
                        INNER JOIN carreras ON usuarios.id_carreras = carreras.id";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        $usuarios = [];
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            $usuarios[] = $fila;
                        }
                    echo json_encode($usuarios);
                    } else {
                        http_response_code(502);
                        echo json_encode(["error" => "Error de lectura."]);
                    }
                break;

                case "Create":
                    $DNI = $datos["dni"];
                    $nombre_completo = $datos["nombre_completo"];
                    $email = $datos["email"];
                    $telefono = $datos["telefono"];
                    $contrasena = $datos["contrasena"];
                    $id_socio = $datos["socio"];
                    $id_carrera = $datos["carrera"];
                    $activo = 1; 
                    $sql_check = "SELECT * FROM usuarios WHERE email = '$email'";
                    $resultado_check = mysqli_query($conexion, $sql_check);
                    if (mysqli_num_rows($resultado_check) > 0) {
                        http_response_code(409);
                        echo json_encode([
                            "error" => "Este email se encuetra registrado."
                        ]);
                        exit;
                    }
                    $sql = "INSERT INTO usuarios
                            (DNI, nombre_completo, email, telefono, contrasena, activo, id_socio, id_carrera)
                            VALUES
                            ('$DNI', '$nombre_completo', '$email', '$telefono', '$contrasena', $activo, $id_socio, $id_carrera)";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        header("Location: inicio.php");
                    } else {
                        http_response_code(500);
                        echo json_encode([
                            "error" => "Error: Cuenta no registrado."
                        ]);
                    }
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
                                socio = $id_socio,
                                carrera = $id_carrera
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
                        echo json_encode([
                            "error" => "Error: Usuario no inactivo."
                        ]);
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
                    $sql = "INSERT INTO carreras (nombre) VALUES ('$nombre')";
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
                    $sql = "SELECT * FROM carreras";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Ok."]);
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
                    $id = $datos["nombre"];
                    $sql = "UPDATE carreras SET nombre = $id";
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
            }
        break;

        default:
            http_response_code(400);
            echo json_encode(["error" => "Recurso no válido"]);
        break;
    }
?>
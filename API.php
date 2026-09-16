<?php
    /*<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "isfdyt-31";

    $conexion = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conexion -> connect_error) {
    echo "Failed to connect to MySQL: " . $conexion -> connect_error;
    exit();
    }

    ?>*/

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
                    $sql = "SELECT registroPagos.id_pago, usuarios.id_usuarios, monto.importe, estado.nombre AS estado, comprobante.foto
                    FROM registroPagos
                    INNER JOIN usuarios
                        ON registroPagos.id_usuario = usuarios.id_usuarios
                    INNER JOIN monto
                        ON registroPagos.id_monto = monto.id_monto
                    INNER JOIN estado
                        ON registroPagos.id_estado = estado.id_estado
                    INNER JOIN comprobante
                        ON registroPagos.id_comprobante = comprobante.id_comprobante";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Ok."]);
                    } else {
                        http_response_code(502);
                        echo json_encode(["error" => "Error de lectura."]);
                    }
                break;

                case 'Create':
                    $select = "SELECT registroPagos.id_pago, usuarios.id_usuarios, monto.importe, estado.nombre AS estado, comprobante.foto
                        FROM registroPagos
                        INNER JOIN usuarios
                            ON registroPagos.id_usuario = usuarios.id_usuarios
                        INNER JOIN monto
                            ON registroPagos.id_monto = monto.id_monto
                        INNER JOIN estado
                            ON registroPagos.id_estado = estado.id_estado
                        INNER JOIN comprobante
                            ON registroPagos.id_comprobante = comprobante.id_comprobante";
                    $sql = "INSERT INTO registroPagos (registroPagos.id_usuario, registroPagos.id_monto, registroPagos.id_estado, registroPagos.id_comprobante) VALUES ('$id_usuario', '$id_monto', '$id_estado', '$id_comprobante')";
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
                    $sql = "UPDATE registroPagos SET estado = $id";
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
                        INNER JOIN carrera ON usuarios.id_carrera = carrera.id";
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
            }
        break;

            case 'monto':
                switch ($consulta) {
                    case 'Read':
                        $sql = "SELECT id, id_usuarios, importe, importe_anterior, fecha_guardado, fecha_efecto FROM monto";
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
                        $fechaGuardado = $datos["fecha_guardado"];
                        $fechaEfecto = $datos["fecha_efecto"];

                        $sql = "UPDATE monto
                                SET importe = '$importe',
                                    importe_anterior = '$importeAnterior',
                                    fecha_guardado = '$fechaGuardado',
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

        default:
            http_response_code(400);
            echo json_encode(["error" => "Recurso no válido"]);
        break;
    }
?>
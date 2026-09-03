<?php

    header("Content-Type: application/json");

    //Conexión y error "500"
    $conexion = mysqli_connect("localhost", "root", "", "isfdyt-31");
    if(!conexion){
        http_response_code(500);
        echo json_encode(["error" => "Error de conexión"]);
        exit;
    }
    mysqli_set_charset($conexion, "UTF8");

    $recurso = $_POST["recurso"] ?? "";
    $datos = json_encode(file_get_contents("php://input"), true);

    
    switch ($recurso) {
        case 'registroPagos':
            switch ($consulta) {
                case 'Read':
                    $sql = "SELECT registro_pagos.id_pago, usuarios.id_usuarios, monto.importe, estado.nombre AS estado, comprobante.foto
                    FROM registro_pagos
                    INNER JOIN usuarios
                        ON registro_pagos.id_usuario = usuarios.id_usuarios
                    INNER JOIN monto
                        ON registro_pagos.id_monto = monto.id_monto
                    INNER JOIN estado
                        ON registro_pagos.id_estado = estado.id_estado
                    INNER JOIN comprobante
                        ON registro_pagos.id_comprobante = comprobante.id_comprobante";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Ok."]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["error" => "Error de lectura."]);
                    }
                break;

                case 'Create':
                    $select = "SELECT registro_pagos.id_pago, usuarios.id_usuarios, monto.importe, estado.nombre AS estado, comprobante.foto
                        FROM registro_pagos
                        INNER JOIN usuarios
                            ON registro_pagos.id_usuario = usuarios.id_usuarios
                        INNER JOIN monto
                            ON registro_pagos.id_monto = monto.id_monto
                        INNER JOIN estado
                            ON registro_pagos.id_estado = estado.id_estado
                        INNER JOIN comprobante
                            ON registro_pagos.id_comprobante = comprobante.id_comprobante";
                    $sql = "INSERT INTO registro_pagos ("registro_pagos.id_usuario, registro_pagos.id_monto, registro_pagos.id_estado, registro_pagos.id_comprobante")";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Pago almacenado con exito."]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["error" => "Pago rechazado."]);
                    }
                break;

                case 'Update':
                    $id = $datos["estado"];
                    $sql = "UPDATE registro_pago SET estado = $id";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Actualizado correctamente."]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["error" => "Error de actualización."]);
                    }
                break;
                        
                default:
                    http_response_code(400);
                    echo json_encode(["error" => "Método no válido"]);
                break;
            }
        break; 

        case 'carreras':
            switch ($consulta) {
                case 'Update':
                    $id = $datos["nombre"];
                    $sql = "UPDATE carreras SET nombre = $id";
                    $resultado = mysqli_query($conexion, $sql);
                    if ($resultado) {
                        echo json_encode(["mensaje" => "Actualizado correctamente."]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["error" => "Error de actualización."]);
                    }
                break;
                        
                default:
                    http_response_code(400);
                    echo json_encode(["error" => "Método no válido"]);
                break;
            }
        break;

        default:
            http_response_code(400);
            echo json_encode(["error" => "Recurso no válido"]);
        break;
    }
        break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
        break;
?>
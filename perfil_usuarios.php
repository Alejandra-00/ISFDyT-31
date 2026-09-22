<?php
    if($_SERVER["REQUEST_METHOD"] === "POST" ) {
        $dni = $_POST["dni"];
        $contrasena = $_POST['contrasena'];
        $recurso = $_POST["recurso"];
        $consulta = $_POST["consulta"];

        $datos = [
            "dni" => $dni,
            "contrasena" => $contrasena,
            "recurso" => $recurso,
            "consulta" => $consulta
        ];

         // Convertir a JSON
        $payload = json_encode($datos);

        // Configurar la petición HTTP hacia el otro archivo local
        $opciones = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => "POST",
                "content"  => $payload,
                "ignore_errors" => true
            ]
        ];

        $contexto = stream_context_create($opciones);
        // URL del archivo que va a recibir (ajusta la ruta según tu proyecto), enviar los datos y obtener la respuesta
        $resultado = file_get_contents("http://localhost/ISFDyT-31/API.php", false, $contexto);
        $respuesta = json_decode($resultado, true);

        if (isset($respuesta['mensaje'])) {
            $_SESSION['usuario'] = $respuesta['usuario'];
            $_SESSION['admin'] = $respuesta['admin'] ?? 0;
            header('Location: inicio.php');
            exit;
        } else {
            $error = $respuesta['error'] ?? 'Error desconocido';
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de usuario</title>
    <link rel="stylesheet" href="perfil_usuario.css">
</head>
<body>

<?php include 'nav.php' ?>

<div class="contenedor-usuario">

    <div class="fila perfil">
        <div class="informacion">
            <div class="foto-perfil">
                <img src="iconos/logo.jpg" alt="Logo de perfil">
                <h2>Perfil de Usuario</h2>
            </div>
        </div>
    </div>

    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Nombre completo</h3>
            <p id="ver_nombre_completo">
                <?= htmlspecialchars($usuario['nombre_completo'] ?? '') ?>
            </p>
            <input class="campo-edicion" id="editar_nombre_completo" type="text" name="valor" value="<?= htmlspecialchars($usuario['nombre_completo'] ?? '') ?>">
        </div>
        <input type="hidden" name="campo" value="nombre_completo">
        <button type="button" class="editar-btn" onclick="editarCampo('nombre_completo')">Editar</button>
        <div class="acciones-edicion" id="acciones_nombre_completo">
            <button type="button" class="guardar-btn" onclick="guardarCampo('nombre_completo')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('nombre_completo')">Cancelar</button>
        </div>
    </form>

    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Número de documento</h3>
            <p id="ver_numero_documento"><?= htmlspecialchars($usuario['numero_documento'] ?? '') ?></p>
            <input class="campo-edicion" id="editar_numero_documento" type="text" name="valor" value="<?= htmlspecialchars($usuario['numero_documento'] ?? '') ?>">
        </div>
        <input type="hidden" name="campo" value="numero_documento">
        <button type="button" class="editar-btn" onclick="editarCampo('numero_documento')">Editar</button>
        <div class="acciones-edicion" id="acciones_numero_documento">
            <button type="button" class="guardar-btn" onclick="guardarCampo('numero_documento')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('numero_documento')">Cancelar</button>
        </div>
    </form>

    <form method="POST" class="fila">
        <div class="informacion">
            <h3>E-mail</h3>
            <p id="ver_email"><?= htmlspecialchars($usuario['email'] ?? '') ?></p>
            <input class="campo-edicion" id="editar_email" type="email" name="valor" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
        </div>
        <input type="hidden" name="campo" value="email">
        <button type="button" class="editar-btn" onclick="editarCampo('email')">Editar</button>
        <div class="acciones-edicion" id="acciones_email">
            <button type="button" class="guardar-btn" onclick="guardarCampo('email')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('email')">Cancelar</button>
        </div>
    </form>

    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Contraseña</h3>
            <p id="ver_contrasena">********</p>
            <input class="campo-edicion" id="editar_contrasena" type="password" name="valor" placeholder="Nueva contraseña">
        </div>
        <input type="hidden" name="campo" value="contrasena">
        <button type="button" class="editar-btn" onclick="editarCampo('contrasena')">Editar</button>
        <div class="acciones-edicion" id="acciones_contrasena">
            <button type="button" class="guardar-btn" onclick="guardarCampo('contrasena')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('contrasena')">Cancelar</button>
        </div>
    </form>

    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Carrera</h3>
            <p id="ver_id_carrera"><?= htmlspecialchars($usuario['nombre_carrera'] ?? 'Sin carrera') ?></p>
            <select class="campo-edicion" id="editar_id_carrera" name="valor">
                <?php foreach ($carreras as $carrera): ?>
                    <option value="<?= $carrera['id'] ?>" <?= $carrera['id'] == $usuario['id_carrera'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($carrera['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="campo" value="id_carrera">
        <button type="button" class="editar-btn" onclick="editarCampo('id_carrera')">Editar</button>
        <div class="acciones-edicion" id="acciones_id_carrera">
            <button type="button" class="guardar-btn" onclick="guardarCampo('id_carrera')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('id_carrera')">Cancelar</button>
        </div>
    </form>

    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Teléfono</h3>
            <p id="ver_telefono"><?= htmlspecialchars($usuario['telefono'] ?? '') ?></p>
            <input class="campo-edicion" id="editar_telefono" type="text" name="valor" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
        </div>
        <input type="hidden" name="campo" value="telefono">
        <button type="button" class="editar-btn" onclick="editarCampo('telefono')">Editar</button>
        <div class="acciones-edicion" id="acciones_telefono">
            <button type="button" class="guardar-btn" onclick="guardarCampo('telefono')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('telefono')">Cancelar</button>
        </div>
    </form>

</div>

<script src="perfil_usuario.js"></script>
</body>
</html>

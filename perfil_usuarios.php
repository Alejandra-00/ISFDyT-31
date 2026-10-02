
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

    <!-- Nombre completo -->
    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Nombre completo</h3>
            <p id="vernombrecompleto">
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

    

    <!-- Número de documento (Cambios de $usuario['numero_documento'] a $usuario['DNI']) -->
    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Número de documento</h3>
            <p id="ver_numerodocumento"><?= htmlspecialchars($usuario['DNI'] ?? '') ?></p>
            <input class="campo-edicion" id="editar_numerodocumento" type="text" name="valor" value="<?= htmlspecialchars($usuario['DNI'] ?? '') ?>">
        </div>
    </form>

    <!-- E-mail -->
    <form method="POST" class="fila">
        <div class="informacion">
            <h3>E-mail</h3>
            <p id="veremail"><?= htmlspecialchars($usuario['email'] ?? '') ?></p>
            <input class="campo-edicion" id="editar_email" type="email" name="valor" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
        </div>
        <input type="hidden" name="campo" value="email">
        <button type="button" class="editar-btn" onclick="editarCampo('email')">Editar</button>
        <div class="acciones-edicion" id="acciones_email">
            <button type="button" class="guardar-btn" onclick="guardarCampo('email')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('email')">Cancelar</button>
        </div>
    </form>

    <!-- Contraseña -->
    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Contraseña</h3>
            <p id="vercontrasena">********</p>
            <input class="campo-edicion" id="editar_contrasena" type="password" name="valor" placeholder="Nueva contraseña">
        </div>
        <input type="hidden" name="campo" value="contrasena">
        <button type="button" class="editar-btn" onclick="editarCampo('contrasena')">Editar</button>
        <div class="acciones-edicion" id="acciones_contrasena">
            <button type="button" class="guardar-btn" onclick="guardarCampo('contrasena')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('contrasena')">Cancelar</button>
        </div>
    </form>

    <!-- Carrera -->
    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Carrera</h3>
            <p id="veridcarrera"><?= htmlspecialchars($usuario['nombre_carrera'] ?? 'Sin carrera') ?></p>
            <select class="campo-edicion" id="editar_id_carrera" name="valor">
                <?php foreach ($carreras as $carrera): ?>
                    <option value="<?= $carrera['id'] ?>" <?= (isset($usuario['id_carrera']) && $carrera['id'] == $usuario['id_carrera']) ? 'selected' : '' ?>>
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

    <!-- Teléfono -->
    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Teléfono</h3>
            <p id="vertelefono"><?= htmlspecialchars($usuario['telefono'] ?? '') ?></p>
            <input class="campo-edicion" id="editar_telefono" type="text" name="valor" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
        </div>
        <input type="hidden" name="campo" value="telefono">
        <button type="button" class="editar-btn" onclick="editarCampo('telefono')">Editar</button>
        <div class="acciones-edicion" id="acciones_telefono">
            <button type="button" class="guardar-btn" onclick="guardarCampo('telefono')">Guardar</button>
            <button type="button" class="cancelar-btn" onclick="cancelarEdicion('telefono')">Cancelar</button>
        </div>
    </form>

    <!-- Tipo de Socio -->
    <form method="POST" class="fila">
        <div class="informacion">
            <h3>Tipo de Socio</h3>
            <p id="vertiposocio"><?= htmlspecialchars($usuario['nombre_socio'] ?? '') ?></p>
            <input class="campo-edicion" id="editar_tiposocio" type="text" name="valor" value="<?= htmlspecialchars($usuario['nombre_socio'] ?? '') ?>">
        </div>
    </form>

</div>

<script src="perfil_usuario.js"></script>
</body>
</html>

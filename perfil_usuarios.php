<?php
// Modo demostración: no se consulta ni se modifica la base de datos.
// include("conexion.php");

/*
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: ../registrarse.php");
    exit();
}

$id_usuario = (int) $_SESSION['id_usuarios'];
*/

// Datos de ejemplo para poder visualizar el perfil sin usar la base de datos.
$usuario = [
    'nombre_completo' => 'María González',
    'numero_documento' => '12345678',
    'email' => 'maria.gonzalez@ejemplo.com',
    'telefono' => '11 4567-8901',
    'id_carrera' => 1,
    'nombre_carrera' => 'Tecnicatura en Desarrollo de Software'
];

/*
$id_usuario = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campo = $_POST['campo'] ?? '';

    $camposPermitidos = [
        'nombre_completo',
        'numero_documento',
        'email',
        'telefono',
        'id_carrera'
    ];

    if (in_array($campo, $camposPermitidos, true)) {
        $valor = trim($_POST['valor'] ?? '');

        if ($campo === 'id_carrera') {
            $valor = (int) $valor;
            $sql = "UPDATE usuarios SET id_carrera = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ii", $valor, $id_usuario);
        } else {
            $sql = "UPDATE usuarios SET `$campo` = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("si", $valor, $id_usuario);
        }

        $stmt->execute();
        $stmt->close();
    }

    if ($campo === 'contrasena') {
        $contrasena = trim($_POST['valor'] ?? '');

        if ($contrasena !== '') {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $stmt = $conexion->prepare(
                "UPDATE usuarios SET contrasena = ? WHERE id = ?"
            );
            $stmt->bind_param("si", $hash, $id_usuario);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: perfil_usuarios.php?actualizado=1");
    exit();
}

$stmt = $conexion->prepare("\n    SELECT \n        u.*,\n        c.nombre AS nombre_carrera\n    FROM usuarios u\n    LEFT JOIN carrera c ON u.id_carrera = c.id\n    WHERE u.id = ?\n");

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario) {
    session_destroy();
    header("Location: ../perfil_usuarios.php");
    exit();
}

$carreras = $conexion->query("SELECT id, nombre FROM carrera ORDER BY nombre");
*/

// Opciones de ejemplo para que el selector también pueda demostrarse.
$carreras = [
    ['id' => 1, 'nombre' => 'Tecnicatura en Desarrollo de Software'],
    ['id' => 2, 'nombre' => 'Tecnicatura en Administración']
];
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

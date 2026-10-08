<?php
    include 'conexion.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $usuario = $_SESSION['usuario'] ?? null;  
    $admin = $_SESSION['admin'] ?? 0;
    
    $pagina_actual = basename($_SERVER['PHP_SELF']); //$_SERVER['PHP_SELF']: Devuelve la ruta completa del script que se está ejecutando. basename(): Se queda solo con la última parte, el nombre del archivo
?>

<link rel="stylesheet" href="nav.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<input type="hidden" id="usuario" value="<?php echo $_SESSION['id'] ?? ''; ?>">
<div class="nav-contenedor">
    <nav class="nav-barra">

        <button class="menu-hamburguesa" id="hamburguesa" onclick="menuHamburguesaMovil(event)">
            <span class="icono-abrir"><img src="iconos/menu.png" alt="Abrir"></span>
            <span class="icono-cerrar"><img src="iconos/cerrar.png" alt="Cerrar"></span>
        </button>

        <ul class="nav-lista">
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                <li class="item-admin"><a href="panel.php" class="icono-nav">
                        <img src="iconos/admin.png" alt="Admin" class="usuarios">
                    </a>
                </li>
            <?php endif; ?>
            
            <li>
                <a href="inicio.php" onclick=CambioColor(this) class="<?= $pagina_actual == 'inicio.php' ? 'activo' : ''?>">
                    Inicio
                </a>
            </li>

            <li>
                <a href="registropagos.php" onclick=CambioColor(this) class="<?= $pagina_actual == 'registropagos.php' ? 'activo' : '' ?>">
                    Registro de pagos
                </a>
            </li>
            
            <li>
                <a href="registropagos.php" onclick=CambioColor(this) class="<?= $pagina_actual == 'pagarCooperadora.php' ? 'activo' : '' ?>">
                    Pagar cooperadora
                </a>
            </li>
            
            <?php if (isset($_SESSION['usuario'])): ?>
                <li class="dropdown item-usuario">
                    <a href="#" class="icono-nav dropdown-toggle" onclick="toggleMenu(event)">
                        <img src="iconos/usuario.png" alt="Usuario" class="usuarios">
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a href="perfil_usuarios.php">
                                Mi perfil
                            </a>
                        </li>
                        <li>
                            <a href="cerrarSesion.php">
                                Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </li> 
            <?php else: ?>
                <li class="item-usuario">
                    <a href="iniciarSesion.php" class="icono-nav">
                        <img src="iconos/usuario.png" alt="Usuario" class="usuarios">
                    </a>
                </li>
            <?php endif; ?>   
        </ul>
    </nav>
</div>
<script src="nav.js"></script>
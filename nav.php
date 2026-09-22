<?php
    session_start();
    include 'conexion.php';
    
    $usuario = $_SESSION['usuario'] ?? null;  
    $admin = $_SESSION['admin'] ?? 0;
    
    $pagina_actual = basename($_SERVER['PHP_SELF']); //$_SERVER['PHP_SELF']: Devuelve la ruta completa del script que se está ejecutando. basename(): Se queda solo con la última parte, el nombre del archivo
?>

<link rel="stylesheet" href="nav.css">
<div class="nav-contenedor">
    <nav class="nav-barra">
        <ul class="nav-lista">
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                <li><a href="panel.php" class="icono-nav">
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
                <a href="registroPagos.php" onclick=CambioColor(this) class="<?= $pagina_actual == 'registroPagos.php' ? 'activo' : '' ?>">
                    Registro de pagos
                </a>
            </li>
            
            <li>
                <a href="pagarCooperadora.php" onclick=CambioColor(this) class="<?= $pagina_actual == 'pagarCooperadora.php' ? 'activo' : '' ?>">
                    Pagar cooperadora
                </a>
            </li>
            
            <?php if (isset($_SESSION['usuario'])): ?>
                <li class="dropdown">
                    <a href="#" class="icono-nav dropdown-toggle" onclick="toggleMenu(event)">
                        <img src="iconos/usuario.png" alt="Usuario" class="usuarios">
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a href="perfil_usuarios.php" onclick=CambioColor(this)>
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
                <li>
                    <a href="iniciarSesion.php" class="icono-nav">
                        <img src="iconos/usuario.png" alt="Usuario" class="usuarios">
                    </a>
                </li>
            <?php endif; ?>   
        </ul>
    </nav>
</div>
<script src="script.js"></script>
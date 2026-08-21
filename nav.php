<link rel="stylesheet" href="nav.css">
<div class="contenedor">
    <nav class="barra">
        <ul class="lista">
            <?php 
                /*if(isset($_SESSION['usuario'])) {
                    if($_SESSION['admin'] == 1){
                        echo "";
                    }
                }*/
            ?> 
            <li><a href="panel.php"><img src="iconos/admin.png" alt="Admin" class="usuarios"></a></li>
            <li><a href="inicio.php" onclick=CambioColor(this) class="activo">Inicio</a></li>
            <li><a href="pagarCooperadora.php" onclick=CambioColor(this)>Pagar cooperadora</a></li>
            <li><a href="registroPagos.php" onclick=CambioColor(this)>Registro de pagos</a></li>
            <li><a href="iniciarSesion.php" class="icono-nav"><img src="iconos/usuario.png" alt="Usuario" class="usuarios"></a></li>
        </ul>
    </nav>
</div>
<script src="script.js"></script>
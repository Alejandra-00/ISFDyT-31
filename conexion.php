<?php
$servername = "sql210.infinityfree.com";
$username = "if0_43168690";
$password = "vRFlj03OH5uW0";
$dbname = "if0_43168690_isfdyt-31";

$conexion = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conexion -> connect_error) {
    echo "Failed to connect to MySQL: " . $conexion -> connect_error;
    exit();
}
?>
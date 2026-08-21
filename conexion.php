<?php
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

?>
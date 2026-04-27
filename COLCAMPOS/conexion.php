<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistema_avicola";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
?>

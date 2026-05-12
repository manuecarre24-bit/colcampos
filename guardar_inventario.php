<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_granja = mysqli_real_escape_string($conexion, $_POST['id_granja']);
    $guardados = 0;

    foreach ($_POST['articulo'] as $key => $art) {
        if (empty(trim($art))) continue;
        $fecha   = mysqli_real_escape_string($conexion, $_POST['fecha'][$key]);
        $articulo= mysqli_real_escape_string($conexion, $art);
        $cant    = floatval($_POST['cantidad'][$key] ?? 0);
        $desc    = mysqli_real_escape_string($conexion, $_POST['descripcion'][$key] ?? '');
        $ingreso = floatval($_POST['ingreso'][$key] ?? 0);

        $sql = "INSERT INTO inventario (id_granja, fecha, articulo, cantidad, descripcion, ingreso)
                VALUES ('$id_granja','$fecha','$articulo','$cant','$desc','$ingreso')";
        if (mysqli_query($conexion, $sql)) $guardados++;
    }

    $back = $id_granja == 'cafe' ? 'cafe_lotes.php' : "seleccionar_galpon.php?granja=$id_granja";
    echo "<script>alert('Inventario guardado: $guardados artículos.');window.location.href='$back';</script>";
} else { header("location: dashboard.php"); }
?>

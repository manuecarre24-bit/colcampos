<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_granja  = mysqli_real_escape_string($conexion, $_POST['id_granja']);
    $num_galpon = intval($_POST['num_galpon']);
    $guardados  = 0;

    foreach ($_POST['concepto'] as $key => $concepto) {
        if (empty(trim($concepto))) continue;
        $fecha    = mysqli_real_escape_string($conexion, $_POST['fecha'][$key]);
        $concepto = mysqli_real_escape_string($conexion, $concepto);
        $cantidad = floatval($_POST['cantidad'][$key] ?? 0);
        $valor    = floatval($_POST['valor'][$key] ?? 0);
        $obs      = mysqli_real_escape_string($conexion, $_POST['observaciones'][$key] ?? '');

        $sql = "INSERT INTO costos_generales
                    (id_granja, num_galpon, fecha, concepto, cantidad, valor, observaciones)
                VALUES ('$id_granja','$num_galpon','$fecha','$concepto','$cantidad','$valor','$obs')";
        if (mysqli_query($conexion, $sql)) $guardados++;
    }

    echo "<script>alert('Costos guardados: $guardados registros.');
    window.location.href='costos_generales.php?granja=$id_granja&galpon=$num_galpon';</script>";
} else { header("location: dashboard.php"); }
?>

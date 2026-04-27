<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_granja  = mysqli_real_escape_string($conexion, $_POST['id_granja']);
    $num_galpon = intval($_POST['num_galpon']);
    $guardados  = 0;

    foreach ($_POST['fecha'] as $key => $val) {
        $cantidad = floatval($_POST['cantidad'][$key] ?? 0);
        if ($cantidad <= 0) continue;

        $fecha    = mysqli_real_escape_string($conexion, $_POST['fecha'][$key]);
        $gramos   = floatval($_POST['gramos'][$key]   ?? 0);
        $unitario = floatval($_POST['unitario'][$key] ?? 0);
        $total    = floatval($_POST['total'][$key]    ?? 0);

        $sql = "INSERT INTO registros_alimentacion
                    (id_granja, num_galpon, fecha, cantidad_alimento, gramos_por_ave, v_unitario, v_total)
                VALUES
                    ('$id_granja', '$num_galpon', '$fecha', '$cantidad', '$gramos', '$unitario', '$total')";

        if (mysqli_query($conexion, $sql)) $guardados++;
    }

    echo "<script>
        alert('Alimentacion guardada: $guardados registros para Galpon $num_galpon.');
        window.location.href='seleccionar_galpon.php?granja=$id_granja';
    </script>";
} else {
    header("location: dashboard.php");
}
?>

<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lote = intval($_POST['num_lote']);
    $guardados = 0;

    foreach ($_POST['fecha'] as $key => $fecha_val) {
        $labor = trim($_POST['labor_realizada'][$key] ?? '');
        if (empty($labor)) continue;

        $fecha   = mysqli_real_escape_string($conexion, $fecha_val);
        $labor   = mysqli_real_escape_string($conexion, $labor);
        $insumo  = mysqli_real_escape_string($conexion, $_POST['insumo'][$key] ?? '');
        $vi      = floatval($_POST['valor_insumo'][$key] ?? 0);
        $vm      = floatval($_POST['valor_mano_obra'][$key] ?? 0);
        $total   = $vi + $vm;
        $obs     = mysqli_real_escape_string($conexion, $_POST['observaciones'][$key] ?? '');

        $sql = "INSERT INTO cafe_labores
                    (num_lote, fecha, labor_realizada, insumo, valor_insumo, valor_mano_obra, total, observaciones)
                VALUES ('$lote','$fecha','$labor','$insumo','$vi','$vm','$total','$obs')";

        if (mysqli_query($conexion, $sql)) $guardados++;
    }

    echo "<script>
        alert('Guardado: $guardados registros para el Lote $lote.');
        window.location.href='cafe_tabla.php?lote=$lote';
    </script>";
} else { header("location: dashboard.php"); }
?>

<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if (isset($_POST['confirmar_limpieza'])) {
    // Solo borramos registros de producción y alimentación; almacén y pagos se conservan
    mysqli_query($conexion, "DELETE FROM registros_produccion");
    mysqli_query($conexion, "DELETE FROM registros_alimentacion");

    echo "<script>
        alert('✅ ¡Mes cerrado! Los registros de producción y alimentación han sido reiniciados.');
        window.location.href='dashboard.php';
    </script>";
} else {
    header("Location: dashboard.php");
}
?>

<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_granja = mysqli_real_escape_string($conexion, $_POST['id_granja']);
    $guardados = 0;

    foreach ($_POST['nombre_empleado'] as $key => $nombre) {
        if (empty(trim($nombre))) continue;

        $nombre_emp = mysqli_real_escape_string($conexion, $nombre);
        $periodo    = mysqli_real_escape_string($conexion, $_POST['periodo_pago'][$key]  ?? '');
        $sueldo     = floatval($_POST['sueldo_base'][$key]  ?? 0);
        $bonos      = floatval($_POST['bonos'][$key]        ?? 0);
        $descuentos = floatval($_POST['descuentos'][$key]   ?? 0);
        $total_neto = floatval($_POST['total_neto'][$key]   ?? 0);

        // Columnas reales: id, id_granja, nombre_empleado, periodo_pago,
        //                  sueldo_base, bonos, descuentos, total_neto
        $sql = "INSERT INTO pagos
                    (id_granja, nombre_empleado, periodo_pago, sueldo_base, bonos, descuentos, total_neto)
                VALUES
                    ('$id_granja', '$nombre_emp', '$periodo', '$sueldo', '$bonos', '$descuentos', '$total_neto')";

        if (mysqli_query($conexion, $sql)) {
            $guardados++;
        } else {
            $err = mysqli_error($conexion);
            echo "<script>alert('Error BD pagos: $err');</script>";
            exit;
        }
    }

    echo "<script>
        alert('Pagos guardados: $guardados registros.');
        window.location.href='seleccionar_galpon.php?granja=$id_granja';
    </script>";
} else {
    header("location: dashboard.php");
}
?>

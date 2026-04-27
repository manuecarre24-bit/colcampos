<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_granja = mysqli_real_escape_string($conexion, $_POST['id_granja']);
    $guardados = 0;

    foreach ($_POST['nombre_articulo'] as $key => $nombre) {
        if (empty(trim($nombre))) continue;

        $categoria   = mysqli_real_escape_string($conexion, $_POST['categoria'][$key]);
        $nombre_art  = mysqli_real_escape_string($conexion, $nombre);
        $cantidad    = floatval($_POST['cantidad_actual'][$key] ?? 0);
        $unidad      = mysqli_real_escape_string($conexion, $_POST['unidad_medida'][$key] ?? '');
        $stock_min   = intval($_POST['stock_minimo'][$key] ?? 0);

        // Columnas reales: id_item, id_granja, categoria, nombre_articulo,
        //                  cantidad_actual, unidad_medida, stock_minimo
        $sql = "INSERT INTO almacen
                    (id_granja, categoria, nombre_articulo, cantidad_actual, unidad_medida, stock_minimo)
                VALUES
                    ('$id_granja', '$categoria', '$nombre_art', '$cantidad', '$unidad', '$stock_min')
                ON DUPLICATE KEY UPDATE
                    categoria='$categoria', cantidad_actual='$cantidad',
                    unidad_medida='$unidad', stock_minimo='$stock_min'";

        if (mysqli_query($conexion, $sql)) {
            $guardados++;
        } else {
            $err = mysqli_error($conexion);
            echo "<script>alert('Error BD almacen: $err');</script>";
            exit;
        }
    }

    echo "<script>
        alert('Inventario actualizado: $guardados articulos guardados.');
        window.location.href='seleccionar_galpon.php?granja=$id_granja';
    </script>";
} else {
    header("location: dashboard.php");
}
?>

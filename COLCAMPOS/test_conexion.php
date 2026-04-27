<?php
include 'conexion.php';
echo "<h2>Estructura de tus tablas</h2>";

$tablas = ['registros_alimentacion', 'registros_produccion', 'almacen', 'pagos', 'usuarios'];

foreach ($tablas as $tabla) {
    echo "<h3>Tabla: $tabla</h3>";
    $res = mysqli_query($conexion, "DESCRIBE $tabla");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            echo "— <b>" . $row['Field'] . "</b> (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "❌ No existe esta tabla<br>";
    }
    echo "<br>";
}
?>
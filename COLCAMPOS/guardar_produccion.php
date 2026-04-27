<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_granja  = mysqli_real_escape_string($conexion, $_POST['id_granja']);
    $num_galpon = intval($_POST['num_galpon']);
    $guardados  = 0;

    foreach ($_POST['fecha'] as $key => $fecha_val) {
        $c     = intval($_POST['h_c'][$key]        ?? 0);
        $b     = intval($_POST['h_b'][$key]        ?? 0);
        $a     = intval($_POST['h_a'][$key]        ?? 0);
        $aa    = intval($_POST['h_aa'][$key]       ?? 0);
        $aaa   = intval($_POST['h_aaa'][$key]      ?? 0);
        $jumbo = intval($_POST['h_jumbo'][$key]    ?? 0);
        $mort  = intval($_POST['mortalidad'][$key] ?? 0);

        if ($c + $b + $a + $aa + $aaa + $jumbo + $mort === 0) continue;

        $fecha   = mysqli_real_escape_string($conexion, $fecha_val);
        $sem     = intval($_POST['semana'][$key]    ?? 0);
        $averia  = intval($_POST['h_averia'][$key]  ?? 0);
        $saldo   = intval($_POST['saldo'][$key]     ?? 0);
        $total   = $c + $b + $a + $aa + $aaa + $jumbo;
        $porc_postura  = ($saldo > 0) ? round(($total / $saldo) * 100, 2) : 0;
        $porc_mort     = ($saldo > 0) ? round(($mort  / $saldo) * 100, 2) : 0;

        $sql = "INSERT INTO registros_produccion
                    (id_granja, num_galpon, fecha, semana_aves,
                     huevo_c, huevo_b, huevo_a, huevo_aa, huevo_aaa, huevo_jumbo,
                     huevo_averia, total_huevos, porcentaje_postura,
                     mortalidad, porcentaje_mortalidad, saldo_aves)
                VALUES
                    ('$id_granja', '$num_galpon', '$fecha', '$sem',
                     '$c', '$b', '$a', '$aa', '$aaa', '$jumbo',
                     '$averia', '$total', '$porc_postura',
                     '$mort', '$porc_mort', '$saldo')
                ON DUPLICATE KEY UPDATE
                    semana_aves='$sem',
                    huevo_c='$c', huevo_b='$b', huevo_a='$a',
                    huevo_aa='$aa', huevo_aaa='$aaa', huevo_jumbo='$jumbo',
                    huevo_averia='$averia', total_huevos='$total',
                    porcentaje_postura='$porc_postura',
                    mortalidad='$mort', porcentaje_mortalidad='$porc_mort',
                    saldo_aves='$saldo'";

        if (mysqli_query($conexion, $sql)) {
            $guardados++;
        } else {
            $err = mysqli_error($conexion);
            echo "<script>alert('Error BD produccion: $err');</script>";
            exit;
        }
    }

    echo "<script>
        alert('Produccion guardada: $guardados dias para Galpon $num_galpon.');
        window.location.href='seleccionar_galpon.php?granja=$id_granja';
    </script>";
} else {
    header("location: dashboard.php");
}
?>

<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_granja  = mysqli_real_escape_string($conexion, $_POST['id_granja']);
    $num_galpon = intval($_POST['num_galpon']);
    $guardados  = 0;
    $cats = ['c','b','a','aa','aaa','jumbo','averia'];

    foreach ($_POST['fecha'] as $key => $fecha_val) {
        // Calcular total con cartones*30 + unidades
        $total = 0;
        foreach ($cats as $cat) {
            $carton = intval($_POST['carton_'.$cat][$key] ?? 0);
            $und    = intval($_POST['h_'.$cat][$key] ?? 0);
            $total += ($carton * 30) + $und;
        }
        $mort = intval($_POST['mortalidad'][$key] ?? 0);
        if ($total + $mort === 0) continue;

        $fecha  = mysqli_real_escape_string($conexion, $fecha_val);
        $sem    = intval($_POST['semana'][$key] ?? 0);
        $saldo  = intval($_POST['saldo'][$key] ?? 0);
        $porc_postura = $saldo > 0 ? round(($total/$saldo)*100, 2) : 0;
        $porc_mort    = $saldo > 0 ? round(($mort/$saldo)*100, 2) : 0;

        // Cartones
        $cc = intval($_POST['carton_c'][$key] ?? 0);
        $cb = intval($_POST['carton_b'][$key] ?? 0);
        $ca = intval($_POST['carton_a'][$key] ?? 0);
        $caa = intval($_POST['carton_aa'][$key] ?? 0);
        $caaa = intval($_POST['carton_aaa'][$key] ?? 0);
        $cjumbo = intval($_POST['carton_jumbo'][$key] ?? 0);
        // Unidades
        $hc = intval($_POST['h_c'][$key] ?? 0);
        $hb = intval($_POST['h_b'][$key] ?? 0);
        $ha = intval($_POST['h_a'][$key] ?? 0);
        $haa = intval($_POST['h_aa'][$key] ?? 0);
        $haaa = intval($_POST['h_aaa'][$key] ?? 0);
        $hjumbo = intval($_POST['h_jumbo'][$key] ?? 0);
        $haveria = intval($_POST['h_averia'][$key] ?? 0);
        $caveria = intval($_POST['carton_averia'][$key] ?? 0);

        $sql = "INSERT INTO registros_produccion
                    (id_granja, num_galpon, fecha, semana_aves,
                     carton_c, huevo_c, carton_b, huevo_b, carton_a, huevo_a,
                     carton_aa, huevo_aa, carton_aaa, huevo_aaa, carton_jumbo, huevo_jumbo,
                     huevo_averia, total_huevos, porcentaje_postura, mortalidad, porcentaje_mortalidad, saldo_aves)
                VALUES
                    ('$id_granja','$num_galpon','$fecha','$sem',
                     '$cc','$hc','$cb','$hb','$ca','$ha',
                     '$caa','$haa','$caaa','$haaa','$cjumbo','$hjumbo',
                     '$haveria','$total','$porc_postura','$mort','$porc_mort','$saldo')
                ON DUPLICATE KEY UPDATE
                    semana_aves='$sem',
                    carton_c='$cc', huevo_c='$hc', carton_b='$cb', huevo_b='$hb',
                    carton_a='$ca', huevo_a='$ha', carton_aa='$caa', huevo_aa='$haa',
                    carton_aaa='$caaa', huevo_aaa='$haaa', carton_jumbo='$cjumbo', huevo_jumbo='$hjumbo',
                    huevo_averia='$haveria', total_huevos='$total',
                    porcentaje_postura='$porc_postura', mortalidad='$mort',
                    porcentaje_mortalidad='$porc_mort', saldo_aves='$saldo'";

        if (mysqli_query($conexion, $sql)) {
            $guardados++;
        } else {
            $err = mysqli_error($conexion);
            echo "<script>alert('Error: $err');</script>"; exit;
        }
    }
    echo "<script>alert('Producción guardada: $guardados días para Galpón $num_galpon.');
    window.location.href='seleccionar_galpon.php?granja=$id_granja';</script>";
} else { header("location: dashboard.php"); }
?>

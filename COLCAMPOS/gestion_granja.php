<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$granja        = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$galpon        = isset($_GET['galpon']) ? intval($_GET['galpon']) : 1;
$color         = ($granja == 'lupe') ? '#d35400' : '#064e22';
$color_light   = ($granja == 'lupe') ? '#fef3ec' : '#e8f5e9';
$nombre_granja = ($granja == 'lupe') ? 'La Lupe' : 'La Ponderosa';

// ── Cargar datos existentes del mes actual ──
$mes  = date('m');
$anio = date('Y');

$registros = [];
$sql = "SELECT * FROM registros_produccion
        WHERE id_granja='$granja' AND num_galpon='$galpon'
        AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'
        ORDER BY fecha ASC";
$res = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($res)) {
    $dia = intval(date('j', strtotime($row['fecha'])));
    $registros[$dia] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producción — <?php echo $nombre_granja; ?> · G<?php echo $galpon; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #e8ecef; font-family: 'Inter', 'Segoe UI', sans-serif; font-size: 12px; }

        .nav-top {
            background: <?php echo $color; ?>;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
        .nav-top .izq { display: flex; gap: 8px; align-items: center; }
        .nav-top a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 6px 13px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.4);
            transition: background 0.2s;
        }
        .nav-top a:hover { background: rgba(255,255,255,0.2); }
        .nav-top .titulo { font-weight: 700; font-size: 15px; }

        .sub-nav {
            background: <?php echo $color; ?>dd;
            padding: 6px 20px;
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .sub-nav span { color: rgba(255,255,255,0.65); font-size: 12px; }
        .sub-nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 5px;
            border: 1px solid rgba(255,255,255,0.3);
            transition: background 0.2s;
        }
        .sub-nav a:hover { background: rgba(255,255,255,0.2); }

        .mes-badge {
            background: #fff;
            color: <?php echo $color; ?>;
            font-weight: 700;
            font-size: 13px;
            text-align: center;
            padding: 10px;
            border-bottom: 2px solid <?php echo $color; ?>;
        }

        .table-wrap { padding: 14px; overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.10);
            min-width: 900px;
        }

        th {
            background: <?php echo $color; ?>;
            color: #fff;
            padding: 10px 5px;
            border: 1px solid rgba(255,255,255,0.2);
            font-size: 11px;
            font-weight: 700;
            text-align: center;
        }
        td { border: 1px solid #dde3e8; padding: 0; }

        .dia-cell {
            background: <?php echo $color; ?>22;
            font-weight: 700;
            color: #333;
            text-align: center;
            padding: 9px 4px;
            font-size: 11px;
        }
        /* Fila con datos ya guardados */
        tr.tiene-datos { background: #f0fff4; }
        tr.tiene-datos td { border-color: #b2dfdb; }

        .inp {
            width: 100%;
            border: none;
            padding: 9px 4px;
            background: <?php echo $color_light; ?>;
            text-align: center;
            outline: none;
            font-size: 11px;
            font-family: inherit;
            color: #222;
            transition: background 0.15s;
        }
        .inp:focus { background: #fff; border-bottom: 2px solid <?php echo $color; ?>; }
        .inp[readonly] { background: #c8e6c9; font-weight: 700; color: #1b5e20; cursor: default; }

        .footer-form {
            margin: 14px;
            background: #fff;
            padding: 18px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .footer-form p { color: #666; margin-bottom: 12px; font-size: 12px; }
        .btn-save {
            background: <?php echo $color; ?>;
            color: #fff;
            padding: 13px 34px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            transition: filter 0.2s, transform 0.2s;
        }
        .btn-save:hover { filter: brightness(0.88); transform: scale(1.02); }
    </style>
</head>
<body>

<div class="nav-top">
    <div class="izq">
        <a href="seleccionar_galpon.php?granja=<?php echo $granja; ?>">⬅ Galpones</a>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
    <div class="titulo">📍 <?php echo strtoupper($nombre_granja); ?> — GALPÓN <?php echo $galpon; ?></div>
</div>

<div class="sub-nav">
    <span>Ir a:</span>
    <a href="alimentacion.php?granja=<?php echo $granja; ?>&galpon=<?php echo $galpon; ?>">🌾 Alimentación</a>
    <a href="registro_pagos.php?granja=<?php echo $granja; ?>">💰 Pagos</a>
    <a href="gestion_almacen.php?granja=<?php echo $granja; ?>">📦 Almacén</a>
</div>

<div class="mes-badge">
    📅 Mes: <?php echo strtoupper(strftime('%B %Y') ?: date('m/Y')); ?> &nbsp;|&nbsp;
    Granja: <?php echo $nombre_granja; ?> &nbsp;|&nbsp; Galpón: <?php echo $galpon; ?>
</div>

<div class="table-wrap">
<form action="guardar_produccion.php" method="POST">
    <input type="hidden" name="id_granja"  value="<?php echo $granja; ?>">
    <input type="hidden" name="num_galpon" value="<?php echo $galpon; ?>">

    <table>
        <thead>
            <tr>
                <th style="width:32px">Día</th>
                <th style="width:105px">Fecha</th>
                <th style="width:48px">Sem.</th>
                <th>C</th><th>B</th><th>A</th><th>AA</th><th>AAA</th><th>Jumbo</th>
                <th>Avería</th>
                <th>Total</th>
                <th>% Post.</th>
                <th>Mort.</th>
                <th>Saldo Aves</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $dias_mes = date('t'); // días del mes actual
        for ($i = 1; $i <= $dias_mes; $i++):
            $r = $registros[$i] ?? null;
            $tiene = $r !== null;
            $fecha_val = date('Y-m-') . sprintf('%02d', $i);
        ?>
        <tr class="<?php echo $tiene ? 'tiene-datos' : ''; ?>">
            <td><div class="dia-cell"><?php echo $i; ?></div></td>
            <td><input type="date" name="fecha[]" class="inp" value="<?php echo $tiene ? $r['fecha'] : $fecha_val; ?>"></td>
            <td><input type="number" name="semana[]" class="inp" min="0" value="<?php echo $tiene ? $r['semana_aves'] : ''; ?>"></td>
            <td><input type="number" name="h_c[]"     class="inp prod-col" data-row="<?php echo $i; ?>" min="0" value="<?php echo $tiene ? $r['huevo_c']    : ''; ?>"></td>
            <td><input type="number" name="h_b[]"     class="inp prod-col" data-row="<?php echo $i; ?>" min="0" value="<?php echo $tiene ? $r['huevo_b']    : ''; ?>"></td>
            <td><input type="number" name="h_a[]"     class="inp prod-col" data-row="<?php echo $i; ?>" min="0" value="<?php echo $tiene ? $r['huevo_a']    : ''; ?>"></td>
            <td><input type="number" name="h_aa[]"    class="inp prod-col" data-row="<?php echo $i; ?>" min="0" value="<?php echo $tiene ? $r['huevo_aa']   : ''; ?>"></td>
            <td><input type="number" name="h_aaa[]"   class="inp prod-col" data-row="<?php echo $i; ?>" min="0" value="<?php echo $tiene ? $r['huevo_aaa']  : ''; ?>"></td>
            <td><input type="number" name="h_jumbo[]" class="inp prod-col" data-row="<?php echo $i; ?>" min="0" value="<?php echo $tiene ? $r['huevo_jumbo']: ''; ?>"></td>
            <td><input type="number" name="h_averia[]" class="inp" min="0" value="<?php echo $tiene ? $r['huevo_averia'] : ''; ?>"></td>
            <td><input type="number" name="total[]"   class="inp total-<?php echo $i; ?>" readonly value="<?php echo $tiene ? $r['total_huevos'] : ''; ?>"></td>
            <td><input type="text"   name="postura[]" class="inp postura-<?php echo $i; ?>" readonly value="<?php echo $tiene ? $r['porcentaje_postura'].'%' : ''; ?>"></td>
            <td><input type="number" name="mortalidad[]" class="inp" min="0" value="<?php echo $tiene ? $r['mortalidad'] : ''; ?>"></td>
            <td><input type="number" name="saldo[]"   class="inp saldo-<?php echo $i; ?>" min="0" value="<?php echo $tiene ? $r['saldo_aves'] : ''; ?>"></td>
        </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <div class="footer-form">
        <p>Los días con fondo verde ya tienen datos guardados. Puedes editarlos y volver a guardar.</p>
        <button type="submit" class="btn-save">💾 GUARDAR / ACTUALIZAR REPORTE</button>
    </div>
</form>
</div>

<script>
// Calcular total y % postura al escribir
document.querySelectorAll('.prod-col').forEach(function(inp) {
    inp.addEventListener('input', function() {
        var row = this.dataset.row;
        var cols = document.querySelectorAll('.prod-col[data-row="' + row + '"]');
        var sum = 0;
        cols.forEach(function(c) { sum += parseInt(c.value) || 0; });
        document.querySelector('.total-' + row).value = sum;
        var saldo = parseInt(document.querySelector('.saldo-' + row).value) || 0;
        var pos   = document.querySelector('.postura-' + row);
        pos.value = saldo > 0 ? ((sum / saldo) * 100).toFixed(2) + '%' : '';
    });
});
// Recalcular si cambia saldo
document.querySelectorAll('[name="saldo[]"]').forEach(function(inp, idx) {
    inp.addEventListener('input', function() {
        var row   = idx + 1;
        var total = parseInt(document.querySelector('.total-' + row).value) || 0;
        var saldo = parseInt(this.value) || 0;
        var pos   = document.querySelector('.postura-' + row);
        pos.value = saldo > 0 ? ((total / saldo) * 100).toFixed(2) + '%' : '';
    });
});
</script>
</body>
</html>

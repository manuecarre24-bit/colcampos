<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$granja        = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$nombre_granja = ($granja == 'lupe') ? 'La Lupe' : 'La Ponderosa';
$color         = '#1565c0';
$color_light   = '#e3f2fd';

// ── Cargar pagos del mes actual de esta granja ──
$mes  = date('m');
$anio = date('Y');

$registros = [];
$sql = "SELECT * FROM pagos
        WHERE id_granja='$granja'
        AND MONTH(id) = '$mes'
        ORDER BY id ASC";

// La tabla pagos no tiene fecha, así que traemos todos los de esta granja
// y mostramos los más recientes
$sql = "SELECT * FROM pagos WHERE id_granja='$granja' ORDER BY id DESC LIMIT 20";
$res = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($res)) {
    $registros[] = $row;
}
// Mostrar en orden normal
$registros = array_reverse($registros);
$total_filas = max(10, count($registros) + 3);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos — <?php echo $nombre_granja; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f0f4f8; }

        .header {
            background: <?php echo $color; ?>;
            color: #fff;
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
        .header h1 { font-size: 17px; font-weight: 700; }
        .header .nav { display: flex; gap: 8px; }
        .header a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 7px 13px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.4);
            transition: background 0.2s;
        }
        .header a:hover { background: rgba(255,255,255,0.2); }

        .info-badge {
            background: #fff;
            color: <?php echo $color; ?>;
            font-weight: 700;
            font-size: 13px;
            text-align: center;
            padding: 9px;
            border-bottom: 2px solid <?php echo $color; ?>;
        }

        .wrap { padding: 18px; }

        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.10);
        }
        th {
            background: <?php echo $color; ?>;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 12px 8px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }
        td { border: 1px solid #90caf9; padding: 0; }

        tr.tiene-datos { background: #e8f5e9; }
        tr.tiene-datos td { border-color: #a5d6a7; }

        .inp {
            width: 100%;
            border: none;
            padding: 12px 8px;
            background: <?php echo $color_light; ?>;
            text-align: center;
            font-size: 12px;
            font-family: inherit;
            outline: none;
            color: #222;
            transition: background 0.15s;
        }
        .inp:focus { background: #fff; border-bottom: 2px solid <?php echo $color; ?>; }
        .inp-neto { background: #bbdefb !important; font-weight: 700; color: #0d47a1; cursor: default; }

        .num-cell {
            text-align: center;
            padding: 8px 5px;
            font-weight: 700;
            color: #555;
            font-size: 11px;
            background: #f5f5f5;
        }

        .footer-form {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-form p { color: #666; font-size: 12px; }
        .btn-save {
            background: #2e7d32;
            color: #fff;
            padding: 13px 30px;
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

<div class="header">
    <h1>💰 NÓMINA Y PAGOS — <?php echo $nombre_granja; ?></h1>
    <div class="nav">
        <a href="seleccionar_galpon.php?granja=<?php echo $granja; ?>">⬅ Galpones</a>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
</div>

<div class="info-badge">
    Granja: <?php echo $nombre_granja; ?> &nbsp;|&nbsp;
    Registros cargados: <strong><?php echo count($registros); ?></strong>
</div>

<div class="wrap">
<form action="guardar_pagos.php" method="POST">
    <input type="hidden" name="id_granja" value="<?php echo $granja; ?>">

    <table>
        <thead>
            <tr>
                <th style="width:36px">#</th>
                <th>NOMBRE DEL EMPLEADO</th>
                <th style="width:130px">PERIODO DE PAGO</th>
                <th style="width:120px">SUELDO BASE ($)</th>
                <th style="width:110px">BONOS ($)</th>
                <th style="width:110px">DESCUENTOS ($)</th>
                <th style="width:120px">TOTAL NETO ($)</th>
            </tr>
        </thead>
        <tbody>
        <?php for ($i = 0; $i < $total_filas; $i++):
            $r     = $registros[$i] ?? null;
            $tiene = $r !== null;
        ?>
        <tr class="<?php echo $tiene ? 'tiene-datos' : ''; ?>">
            <td><div class="num-cell"><?php echo $i+1; ?></div></td>
            <td><input type="text"   name="nombre_empleado[]" class="inp"
                       value="<?php echo $tiene ? htmlspecialchars($r['nombre_empleado']) : ''; ?>"
                       placeholder="Nombre completo..."></td>
            <td><input type="text"   name="periodo_pago[]"    class="inp"
                       value="<?php echo $tiene ? htmlspecialchars($r['periodo_pago']) : ''; ?>"
                       placeholder="Ej: Mayo Q1"></td>
            <td><input type="number" name="sueldo_base[]"     class="inp" id="s_<?php echo $i; ?>"
                       step="0.01" min="0" oninput="calc(<?php echo $i; ?>)"
                       value="<?php echo $tiene ? $r['sueldo_base'] : '0'; ?>"></td>
            <td><input type="number" name="bonos[]"           class="inp" id="b_<?php echo $i; ?>"
                       step="0.01" min="0" oninput="calc(<?php echo $i; ?>)"
                       value="<?php echo $tiene ? $r['bonos'] : '0'; ?>"></td>
            <td><input type="number" name="descuentos[]"      class="inp" id="d_<?php echo $i; ?>"
                       step="0.01" min="0" oninput="calc(<?php echo $i; ?>)"
                       value="<?php echo $tiene ? $r['descuentos'] : '0'; ?>"></td>
            <td><input type="number" name="total_neto[]"      class="inp inp-neto" id="t_<?php echo $i; ?>"
                       readonly value="<?php echo $tiene ? $r['total_neto'] : '0'; ?>"></td>
        </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <div class="footer-form">
        <p>✅ Filas en verde = ya guardadas &nbsp;|&nbsp; Las nuevas filas vacías se agregan al guardar</p>
        <button type="submit" class="btn-save">💾 GUARDAR PAGOS</button>
    </div>
</form>
</div>

<script>
function calc(i) {
    var s = parseFloat(document.getElementById('s_' + i).value) || 0;
    var b = parseFloat(document.getElementById('b_' + i).value) || 0;
    var d = parseFloat(document.getElementById('d_' + i).value) || 0;
    document.getElementById('t_' + i).value = (s + b - d).toFixed(0);
}
// Calcular totales al cargar la página para filas existentes
document.addEventListener('DOMContentLoaded', function() {
    <?php for ($i = 0; $i < count($registros); $i++): ?>
    calc(<?php echo $i; ?>);
    <?php endfor; ?>
});
</script>

</body>
</html>

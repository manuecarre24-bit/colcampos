<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$granja        = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$galpon        = isset($_GET['galpon']) ? intval($_GET['galpon']) : 1;
$color         = ($granja == 'lupe') ? '#d35400' : '#2e7d32';
$color_light   = ($granja == 'lupe') ? '#fef3ec' : '#e8f5e9';
$nombre_granja = ($granja == 'lupe') ? 'La Lupe' : 'La Ponderosa';

// ── Cargar datos existentes del mes actual ──
$mes  = date('m');
$anio = date('Y');

$registros = [];
$sql = "SELECT * FROM registros_alimentacion
        WHERE id_granja='$granja' AND num_galpon='$galpon'
        AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'
        ORDER BY fecha ASC";
$res = mysqli_query($conexion, $sql);
$idx = 0;
while ($row = mysqli_fetch_assoc($res)) {
    $registros[$idx] = $row;
    $idx++;
}
// Siempre mostrar al menos 15 filas
$total_filas = max(31, count($registros) + 3);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alimentación — <?php echo $nombre_granja; ?> · G<?php echo $galpon; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f0f4f0; }

        .header {
            background: <?php echo $color; ?>;
            color: #fff;
            padding: 13px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
        .header h1 { font-size: 16px; font-weight: 700; }
        .header .nav { display: flex; gap: 8px; }
        .header a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 6px 13px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.4);
            transition: background 0.2s;
        }
        .header a:hover { background: rgba(255,255,255,0.2); }

        .mes-badge {
            background: #fff;
            color: <?php echo $color; ?>;
            font-weight: 700;
            font-size: 13px;
            text-align: center;
            padding: 9px;
            border-bottom: 2px solid <?php echo $color; ?>;
        }

        .table-wrap { padding: 18px; }

        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.10);
        }
        th {
            background: <?php echo $color; ?>;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 11px 8px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }
        td { border: 1px solid #c8e6c9; padding: 0; }

        tr.tiene-datos { background: #f0fff4; }
        tr.tiene-datos td { border-color: #a5d6a7; }

        .inp {
            width: 100%;
            border: none;
            padding: 11px 8px;
            background: <?php echo $color_light; ?>;
            text-align: center;
            outline: none;
            font-size: 12px;
            font-family: inherit;
            color: #222;
            transition: background 0.15s;
        }
        .inp:focus { background: #fff; border-bottom: 2px solid <?php echo $color; ?>; }
        .inp-total { background: #c8e6c9 !important; font-weight: 700; color: #1b5e20; cursor: default; }

        .footer-alim {
            margin-top: 18px;
            text-align: right;
        }
        .btn-save {
            background: <?php echo $color; ?>;
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
    <h1>🌾 ALIMENTACIÓN — <?php echo $nombre_granja; ?> · GALPÓN <?php echo $galpon; ?></h1>
    <div class="nav">
        <a href="gestion_granja.php?granja=<?php echo $granja; ?>&galpon=<?php echo $galpon; ?>">🥚 Producción</a>
        <a href="seleccionar_galpon.php?granja=<?php echo $granja; ?>">⬅ Galpones</a>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
</div>

<div class="mes-badge">
    📅 Mes: <?php echo date('m/Y'); ?> &nbsp;|&nbsp;
    Granja: <?php echo $nombre_granja; ?> &nbsp;|&nbsp; Galpón: <?php echo $galpon; ?>
</div>

<div class="table-wrap">
<form action="guardar_alimentacion.php" method="POST">
    <input type="hidden" name="id_granja"  value="<?php echo $granja; ?>">
    <input type="hidden" name="num_galpon" value="<?php echo $galpon; ?>">

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>FECHA</th>
                <th>CANTIDAD ALIMENTO (KG)</th>
                <th>GRAMOS POR AVE</th>
                <th>VALOR UNITARIO ($)</th>
                <th>VALOR TOTAL ($)</th>
            </tr>
        </thead>
        <tbody>
        <?php for ($i = 0; $i < $total_filas; $i++):
            $r      = $registros[$i] ?? null;
            $tiene  = $r !== null;
        ?>
        <tr class="<?php echo $tiene ? 'tiene-datos' : ''; ?>">
            <td style="text-align:center;padding:8px;font-weight:700;color:#555;font-size:11px;background:#f9fafb;"><?php echo $i+1; ?></td>
            <td><input type="date"   name="fecha[]"    class="inp" value="<?php echo $tiene ? $r['fecha'] : date('Y-m-d'); ?>"></td>
            <td><input type="number" name="cantidad[]" class="inp" id="c_<?php echo $i; ?>" step="0.01" min="0"
                       oninput="calc(<?php echo $i; ?>)" value="<?php echo $tiene ? $r['cantidad_alimento'] : ''; ?>"></td>
            <td><input type="number" name="gramos[]"   class="inp" step="0.01" min="0"
                       value="<?php echo $tiene ? $r['gramos_por_ave'] : ''; ?>"></td>
            <td><input type="number" name="unitario[]" class="inp" id="u_<?php echo $i; ?>" step="0.01" min="0"
                       oninput="calc(<?php echo $i; ?>)" value="<?php echo $tiene ? $r['v_unitario'] : ''; ?>"></td>
            <td><input type="number" name="total[]"    class="inp inp-total" id="t_<?php echo $i; ?>" readonly
                       value="<?php echo $tiene ? $r['v_total'] : '0.00'; ?>"></td>
        </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <div class="footer-alim">
        <p style="color:#666;font-size:12px;margin-bottom:10px;">Las filas con fondo verde ya están guardadas. Puedes agregar más o editar.</p>
        <button type="submit" class="btn-save">💾 GUARDAR CONSUMO</button>
    </div>
</form>
</div>

<script>
function calc(i) {
    var c = parseFloat(document.getElementById('c_' + i).value) || 0;
    var u = parseFloat(document.getElementById('u_' + i).value) || 0;
    document.getElementById('t_' + i).value = (c * u).toFixed(2);
}
</script>
</body>
</html>

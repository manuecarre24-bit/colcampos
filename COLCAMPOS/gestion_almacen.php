<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$granja        = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$nombre_granja = ($granja == 'lupe') ? 'La Lupe' : 'La Ponderosa';
$color         = ($granja == 'lupe') ? '#b84600' : '#37474f';
$color_light   = ($granja == 'lupe') ? '#fef3ec' : '#f5f7f8';

// ── Cargar inventario existente de esta granja ──
$registros = [];
$sql = "SELECT * FROM almacen WHERE id_granja='$granja' ORDER BY categoria, nombre_articulo";
$res = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($res)) {
    $registros[] = $row;
}
$total_filas = max(12, count($registros) + 3);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacén — <?php echo $nombre_granja; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f4f7f6; }

        .header {
            background: <?php echo $color; ?>;
            color: #fff;
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
        .header h2 { font-size: 17px; font-weight: 700; }
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
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.10);
        }
        th {
            background: <?php echo $color; ?>;
            color: #fff;
            padding: 12px 8px;
            border: 1px solid rgba(255,255,255,0.2);
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }
        td { border: 1px solid #cfd8dc; padding: 0; }

        tr.tiene-datos { background: #f1f8e9; }
        tr.tiene-datos td { border-color: #aed581; }

        /* Alerta stock bajo */
        tr.stock-bajo { background: #fff3e0 !important; }
        tr.stock-bajo td { border-color: #ffb74d !important; }

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
        select.inp { cursor: pointer; }

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
    <h2>📦 INVENTARIO — <?php echo $nombre_granja; ?></h2>
    <div class="nav">
        <a href="seleccionar_galpon.php?granja=<?php echo $granja; ?>">⬅ Galpones</a>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
</div>

<div class="info-badge">
    Granja: <?php echo $nombre_granja; ?> &nbsp;|&nbsp;
    Artículos registrados: <strong><?php echo count($registros); ?></strong>
    <?php if (count($registros) > 0): ?>
    &nbsp;|&nbsp; <span style="color:#e65100;">⚠ Fondo naranja = stock bajo</span>
    <?php endif; ?>
</div>

<div class="wrap">
<form action="guardar_almacen.php" method="POST">
    <input type="hidden" name="id_granja" value="<?php echo $granja; ?>">

    <table>
        <thead>
            <tr>
                <th style="width:36px">#</th>
                <th style="width:140px">Categoría</th>
                <th>Nombre del Artículo</th>
                <th style="width:120px">Cantidad Actual</th>
                <th style="width:100px">Unidad</th>
                <th style="width:110px">Stock Mínimo</th>
            </tr>
        </thead>
        <tbody>
        <?php for ($i = 0; $i < $total_filas; $i++):
            $r      = $registros[$i] ?? null;
            $tiene  = $r !== null;
            $bajo   = $tiene && floatval($r['cantidad_actual']) <= floatval($r['stock_minimo']) && floatval($r['stock_minimo']) > 0;
            $clase  = $bajo ? 'stock-bajo' : ($tiene ? 'tiene-datos' : '');
        ?>
        <tr class="<?php echo $clase; ?>">
            <td><div class="num-cell"><?php echo $i+1; ?></div></td>
            <td>
                <select name="categoria[]" class="inp">
                    <?php
                    $cats = ['Alimento','Medicamento','Herramienta','Insumo General'];
                    foreach ($cats as $cat):
                        $sel = ($tiene && $r['categoria'] == $cat) ? 'selected' : '';
                    ?>
                    <option value="<?php echo $cat; ?>" <?php echo $sel; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="text"   name="nombre_articulo[]" class="inp"
                       value="<?php echo $tiene ? htmlspecialchars($r['nombre_articulo']) : ''; ?>"
                       placeholder="Ej: Purina postura..."></td>
            <td><input type="number" name="cantidad_actual[]" class="inp" step="0.01" min="0"
                       value="<?php echo $tiene ? $r['cantidad_actual'] : '0'; ?>"></td>
            <td><input type="text"   name="unidad_medida[]"   class="inp"
                       value="<?php echo $tiene ? htmlspecialchars($r['unidad_medida']) : ''; ?>"
                       placeholder="Kg / L / Und"></td>
            <td><input type="number" name="stock_minimo[]"    class="inp" min="0"
                       value="<?php echo $tiene ? $r['stock_minimo'] : '0'; ?>"></td>
        </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <div class="footer-form">
        <p>✅ Filas en verde = ya guardadas &nbsp; ⚠️ Naranja = stock por debajo del mínimo</p>
        <button type="submit" class="btn-save">💾 ACTUALIZAR INVENTARIO</button>
    </div>
</form>
</div>

</body>
</html>

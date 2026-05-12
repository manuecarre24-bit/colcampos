<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$granja        = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$nombre_granja = ($granja == 'lupe') ? 'La Lupe' : 'La Ponderosa';
$color         = '#1565c0';
$color_light   = '#e3f2fd';

$registros = [];
$sql = "SELECT * FROM pagos WHERE id_granja='$granja' ORDER BY id ASC";
$res = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($res)) $registros[] = $row;
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
            color: #fff; text-decoration: none; font-weight: 600; font-size: 13px;
            padding: 7px 13px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.4);
            transition: background 0.2s;
        }
        .header a:hover { background: rgba(255,255,255,0.2); }

        .info-badge {
            background: #fff; color: <?php echo $color; ?>; font-weight: 700;
            font-size: 13px; text-align: center; padding: 9px;
            border-bottom: 2px solid <?php echo $color; ?>;
        }

        .wrap { padding: 18px; }

        table {
            border-collapse: collapse; width: 100%; background: #fff;
            border-radius: 8px; overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.10);
        }
        th {
            background: <?php echo $color; ?>; color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 12px 8px; font-size: 12px; font-weight: 700; text-align: center;
        }
        td { border: 1px solid #90caf9; padding: 0; }
        tr.tiene-datos { background: #e8f5e9; }
        tr.tiene-datos td { border-color: #a5d6a7; }
        tr.fila-nueva { background: #fff; }

        .inp {
            width: 100%; border: none; padding: 12px 8px;
            background: <?php echo $color_light; ?>;
            text-align: center; font-size: 12px; font-family: inherit;
            outline: none; color: #222; transition: background 0.15s;
        }
        .inp:focus { background: #fff; border-bottom: 2px solid <?php echo $color; ?>; }
        .inp-neto { background: #bbdefb !important; font-weight: 700; color: #0d47a1; cursor: default; }
        .inp-nueva { background: #f9f9f9 !important; }
        .inp-nueva:focus { background: #fff !important; border-bottom: 2px solid <?php echo $color; ?>; }

        .num-cell {
            text-align: center; padding: 8px 5px; font-weight: 700;
            color: #555; font-size: 11px; background: #f5f5f5;
        }

        /* ── Botón agregar fila ── */
        .btn-agregar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #e3f2fd;
            color: <?php echo $color; ?>;
            border: 2px dashed <?php echo $color; ?>;
            padding: 11px 22px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            font-family: inherit;
            transition: background 0.2s, transform 0.15s;
            margin-top: 14px;
        }
        .btn-agregar:hover { background: #bbdefb; transform: scale(1.02); }

        .footer-form {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .footer-form p { color: #666; font-size: 12px; }
        .btn-save {
            background: #2e7d32; color: #fff; padding: 13px 30px;
            border: none; border-radius: 7px; cursor: pointer;
            font-weight: 700; font-size: 15px;
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

    <table id="tablaPagos">
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
        <tbody id="cuerpo-pagos">
        <?php for ($i = 0; $i < $total_filas; $i++):
            $r = $registros[$i] ?? null; $tiene = $r !== null; ?>
        <tr class="<?php echo $tiene ? 'tiene-datos' : 'fila-nueva'; ?>">
            <td><div class="num-cell"><?php echo $i+1; ?></div></td>
            <td><input type="text" name="nombre_empleado[]" class="inp <?php echo !$tiene?'inp-nueva':''; ?>"
                value="<?php echo $tiene ? htmlspecialchars($r['nombre_empleado']) : ''; ?>" placeholder="Nombre completo..."></td>
            <td><input type="text" name="periodo_pago[]" class="inp <?php echo !$tiene?'inp-nueva':''; ?>"
                value="<?php echo $tiene ? htmlspecialchars($r['periodo_pago']) : ''; ?>" placeholder="Ej: Mayo Q1"></td>
            <td><input type="number" name="sueldo_base[]" class="inp <?php echo !$tiene?'inp-nueva':''; ?>"
                id="s_<?php echo $i; ?>" step="0.01" min="0" oninput="calc(<?php echo $i; ?>)"
                value="<?php echo $tiene ? $r['sueldo_base'] : '0'; ?>"></td>
            <td><input type="number" name="bonos[]" class="inp <?php echo !$tiene?'inp-nueva':''; ?>"
                id="b_<?php echo $i; ?>" step="0.01" min="0" oninput="calc(<?php echo $i; ?>)"
                value="<?php echo $tiene ? $r['bonos'] : '0'; ?>"></td>
            <td><input type="number" name="descuentos[]" class="inp <?php echo !$tiene?'inp-nueva':''; ?>"
                id="d_<?php echo $i; ?>" step="0.01" min="0" oninput="calc(<?php echo $i; ?>)"
                value="<?php echo $tiene ? $r['descuentos'] : '0'; ?>"></td>
            <td><input type="number" name="total_neto[]" class="inp inp-neto" id="t_<?php echo $i; ?>"
                readonly value="<?php echo $tiene ? $r['total_neto'] : '0'; ?>"></td>
        </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <!-- Botón agregar fila -->
    <button type="button" class="btn-agregar" onclick="agregarFila()">
        ➕ Agregar fila
    </button>

    <div class="footer-form">
        <p>✅ Verde = ya guardado &nbsp;|&nbsp; Filas en blanco = nuevas &nbsp;|&nbsp; Agrega las filas que necesites</p>
        <button type="submit" class="btn-save">💾 GUARDAR PAGOS</button>
    </div>
</form>
</div>

<script>
var contador = <?php echo $total_filas; ?>;

function calc(i) {
    var s = parseFloat(document.getElementById('s_' + i).value) || 0;
    var b = parseFloat(document.getElementById('b_' + i).value) || 0;
    var d = parseFloat(document.getElementById('d_' + i).value) || 0;
    document.getElementById('t_' + i).value = (s + b - d).toFixed(0);
}

function agregarFila() {
    var tbody = document.getElementById('cuerpo-pagos');
    var i = contador;
    var tr = document.createElement('tr');
    tr.className = 'fila-nueva';
    tr.innerHTML =
        '<td><div class="num-cell">' + (i + 1) + '</div></td>' +
        '<td><input type="text" name="nombre_empleado[]" class="inp inp-nueva" placeholder="Nombre completo..."></td>' +
        '<td><input type="text" name="periodo_pago[]" class="inp inp-nueva" placeholder="Ej: Mayo Q1"></td>' +
        '<td><input type="number" name="sueldo_base[]" class="inp inp-nueva" id="s_' + i + '" step="0.01" min="0" value="0" oninput="calc(' + i + ')"></td>' +
        '<td><input type="number" name="bonos[]" class="inp inp-nueva" id="b_' + i + '" step="0.01" min="0" value="0" oninput="calc(' + i + ')"></td>' +
        '<td><input type="number" name="descuentos[]" class="inp inp-nueva" id="d_' + i + '" step="0.01" min="0" value="0" oninput="calc(' + i + ')"></td>' +
        '<td><input type="number" name="total_neto[]" class="inp inp-neto" id="t_' + i + '" readonly value="0"></td>';
    tbody.appendChild(tr);
    contador++;
    // Hacer foco en el nombre del nuevo empleado
    tr.querySelector('input[name="nombre_empleado[]"]').focus();
}

// Recalcular totales de filas existentes al cargar
document.addEventListener('DOMContentLoaded', function() {
    for (var i = 0; i < <?php echo count($registros); ?>; i++) calc(i);
});
</script>
</body>
</html>

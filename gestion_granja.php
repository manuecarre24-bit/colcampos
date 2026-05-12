<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$granja        = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$galpon        = isset($_GET['galpon']) ? intval($_GET['galpon']) : 1;
$color         = ($granja == 'lupe') ? '#d35400' : '#064e22';
$color_light   = ($granja == 'lupe') ? '#fef3ec' : '#e8f5e9';
$nombre_granja = ($granja == 'lupe') ? 'La Lupe' : 'La Ponderosa';

$mes  = date('m'); $anio = date('Y');
$registros = [];
$sql = "SELECT * FROM registros_produccion
        WHERE id_granja='$granja' AND num_galpon='$galpon'
        AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio' ORDER BY fecha ASC";
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
    <title>Producción — <?php echo $nombre_granja;?> · G<?php echo $galpon;?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{background:#e8ecef;font-family:'Inter','Segoe UI',sans-serif;font-size:11px}

        .nav-top{background:<?php echo $color;?>;color:#fff;padding:11px 18px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 8px rgba(0,0,0,.25)}
        .nav-top .izq{display:flex;gap:8px}
        .nav-top a{color:#fff;text-decoration:none;font-weight:600;font-size:13px;padding:6px 12px;border-radius:6px;border:1px solid rgba(255,255,255,.4);transition:background .2s}
        .nav-top a:hover{background:rgba(255,255,255,.2)}
        .nav-top .titulo{font-weight:700;font-size:14px}

        .sub-nav{background:<?php echo $color;?>dd;padding:5px 18px;display:flex;gap:8px;align-items:center}
        .sub-nav span{color:rgba(255,255,255,.65);font-size:11px}
        .sub-nav a{color:#fff;text-decoration:none;font-weight:600;font-size:11px;padding:5px 10px;border-radius:5px;border:1px solid rgba(255,255,255,.3);transition:background .2s}
        .sub-nav a:hover{background:rgba(255,255,255,.2)}

        .mes-badge{background:#fff;color:<?php echo $color;?>;font-weight:700;font-size:12px;text-align:center;padding:8px;border-bottom:2px solid <?php echo $color;?>}

        .table-wrap{padding:12px;overflow-x:auto}

        table{border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.10);width:100%}

        /* Encabezado principal */
        th.grp{background:<?php echo $color;?>;color:#fff;text-align:center;font-size:11px;font-weight:700;padding:9px 5px;border:1px solid rgba(255,255,255,.2)}

        td{border:1px solid #dde3e8;padding:0;vertical-align:middle}

        /* Par de filas por día — fondo alterno */
        tr.par-a td{background:#f0fff4}
        tr.par-b td{background:#e2f5e8}
        tr.par-a-new td{background:#fff}
        tr.par-b-new td{background:#f5f5f5}

        /* Celda fecha+día — ocupa rowspan=2 */
        .dia-cell{background:<?php echo $color;?>;color:#fff;text-align:center;padding:6px 4px;font-size:11px;font-weight:700;vertical-align:middle}
        .fecha-cell{background:<?php echo $color;?>22;text-align:center;padding:6px 4px;font-size:10px;vertical-align:middle}

        /* Etiqueta CARTÓN / UNIDAD */
        .lbl-cu{background:<?php echo $color;?>33;color:<?php echo $color;?>;font-weight:700;font-size:10px;text-align:center;padding:7px 6px;white-space:nowrap;border-right:2px solid <?php echo $color;?>44}

        /* Inputs */
        .inp{width:100%;border:none;padding:7px 3px;background:transparent;text-align:center;outline:none;font-size:11px;font-family:inherit;color:#222;transition:background .15s}
        .inp:focus{background:#fffde7;border-bottom:2px solid <?php echo $color;?>}
        .inp[readonly]{font-weight:700;color:<?php echo $color;?>;cursor:default;background:transparent}

        /* Celdas de totales — readonly especiales */
        .td-total1{background:<?php echo $color;?>22!important}
        .td-total2{background:<?php echo $color;?>44!important}
        .td-postura{background:<?php echo $color;?>15!important}

        /* Fila separadora entre días */
        tr.separador td{height:3px;background:<?php echo $color;?>33;padding:0;border:none}

        .footer-form{margin:12px;background:#fff;padding:16px;text-align:center;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08)}
        .footer-form p{color:#666;margin-bottom:10px;font-size:11px;line-height:1.6}
        .btn-save{background:<?php echo $color;?>;color:#fff;padding:12px 32px;border:none;border-radius:7px;cursor:pointer;font-weight:700;font-size:14px;transition:filter .2s,transform .2s}
        .btn-save:hover{filter:brightness(.88);transform:scale(1.02)}

        @media print{
            .nav-top,.sub-nav,.mes-badge,.footer-form{display:none!important}
            body{background:#fff}.table-wrap{padding:0}
            table{box-shadow:none;border-radius:0}
            th.grp{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .dia-cell,.lbl-cu{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            tr.par-a td,tr.par-b td{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            @page{margin:1cm;size:A4 landscape}
        }
    </style>
</head>
<body>

<div class="nav-top">
    <div class="izq">
        <a href="seleccionar_galpon.php?granja=<?php echo $granja;?>">⬅ Galpones</a>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
    <div class="titulo">📍 <?php echo strtoupper($nombre_granja);?> — GALPÓN <?php echo $galpon;?></div>
</div>
<div class="sub-nav">
    <span>Ir a:</span>
    <a href="alimentacion.php?granja=<?php echo $granja;?>&galpon=<?php echo $galpon;?>">🌾 Alimentación</a>
    <a href="costos_generales.php?granja=<?php echo $granja;?>&galpon=<?php echo $galpon;?>">💰 Costos</a>
    <a href="inventario.php?granja=<?php echo $granja;?>">📦 Inventario</a>
</div>
<div class="mes-badge">
    📅 <?php echo date('m/Y');?> &nbsp;|&nbsp; <?php echo $nombre_granja;?> · Galpón <?php echo $galpon;?> &nbsp;|&nbsp;
    <span style="color:#888;font-size:11px">Ctrl+P imprime la tabla</span>
</div>

<div class="table-wrap">
<form action="guardar_produccion.php" method="POST">
    <input type="hidden" name="id_granja"  value="<?php echo $granja;?>">
    <input type="hidden" name="num_galpon" value="<?php echo $galpon;?>">

    <table>
        <thead>
            <tr>
                <th class="grp" style="width:28px">Día</th>
                <th class="grp" style="width:90px">Fecha</th>
                <th class="grp" style="width:34px">Sem.</th>
                <th class="grp" style="width:48px"> </th><!-- label CARTÓN/UNIDAD -->
                <th class="grp">C</th>
                <th class="grp">B</th>
                <th class="grp">A</th>
                <th class="grp">AA</th>
                <th class="grp">AAA</th>
                <th class="grp">Jumbo</th>
                <th class="grp">Averías</th>
                <th class="grp" style="width:110px">Total Cartones / Unidades</th>
                <th class="grp" style="width:62px">Total Huevos (×30)</th>
                <th class="grp" style="width:52px">% Post.</th>
                <th class="grp" style="width:40px">Mort.</th>
                <th class="grp" style="width:58px">Saldo Aves</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $dias_mes = date('t');
        $cats = ['c','b','a','aa','aaa','jumbo'];

        for ($i = 1; $i <= $dias_mes; $i++):
            $r     = $registros[$i] ?? null;
            $tiene = $r !== null;
            $fecha_val = date('Y-m-') . sprintf('%02d', $i);
            $par_a = $tiene ? 'par-a' : 'par-a-new';
            $par_b = $tiene ? 'par-b' : 'par-b-new';
        ?>

        <!-- ── FILA A: CARTÓN ── -->
        <tr class="<?php echo $par_a;?>">
            <!-- Día — rowspan 2 -->
            <td rowspan="2"><div class="dia-cell"><?php echo $i;?></div></td>
            <!-- Fecha — rowspan 2 -->
            <td rowspan="2"><input type="date" name="fecha[]" class="inp fecha-cell"
                value="<?php echo $tiene ? $r['fecha'] : $fecha_val;?>"></td>
            <!-- Semana — rowspan 2 -->
            <td rowspan="2"><input type="number" name="semana[]" class="inp" min="0"
                value="<?php echo $tiene ? $r['semana_aves'] : '';?>"></td>
            <!-- Label -->
            <td class="lbl-cu">CARTÓN</td>
            <!-- Cartones por categoría -->
            <?php foreach($cats as $cat):
                $v = ($tiene && isset($r['carton_'.$cat])) ? $r['carton_'.$cat] : '';
            ?>
            <td><input type="number" name="carton_<?php echo $cat;?>[]"
                class="inp prod-input" data-dia="<?php echo $i;?>" data-tipo="carton" data-cat="<?php echo $cat;?>"
                min="0" value="<?php echo $v;?>"></td>
            <?php endforeach;?>
            <!-- Averías cartón -->
            <td><input type="number" name="carton_averia[]"
                class="inp prod-input" data-dia="<?php echo $i;?>" data-tipo="carton" data-cat="averia"
                min="0" value="<?php echo ($tiene && isset($r['carton_averia'])) ? $r['carton_averia'] : '';?>"></td>
            <!-- Total cartones / unidades por separado — rowspan 2 -->
            <td rowspan="2" class="td-total1" style="padding:2px 4px;vertical-align:middle">
                <div style="font-size:9px;color:#555;text-align:center;margin-bottom:2px">Ctón:</div>
                <input type="number" name="total_carton[]" class="inp" readonly
                    id="tcart_<?php echo $i;?>" style="background:transparent;font-weight:700;color:<?php echo $color;?>;font-size:11px"
                    value="">
                <div style="font-size:9px;color:#555;text-align:center;margin-top:3px;margin-bottom:2px">Und:</div>
                <input type="number" name="total_und[]" class="inp" readonly
                    id="tund_<?php echo $i;?>" style="background:transparent;font-weight:700;color:#555;font-size:11px"
                    value="">
            </td>
            <!-- Total huevos reales — rowspan 2 -->
            <td rowspan="2" class="td-total2">
                <input type="number" name="total[]" class="inp" readonly
                    id="total_<?php echo $i;?>"
                    value="<?php echo $tiene ? $r['total_huevos'] : '';?>">
            </td>
            <!-- % Postura — rowspan 2 -->
            <td rowspan="2" class="td-postura">
                <input type="text" name="postura[]" class="inp" readonly
                    id="postura_<?php echo $i;?>"
                    value="<?php echo ($tiene && $r['porcentaje_postura']) ? $r['porcentaje_postura'].'%' : '';?>">
            </td>
            <!-- Mortalidad — rowspan 2 -->
            <td rowspan="2">
                <input type="number" name="mortalidad[]" class="inp" min="0"
                    value="<?php echo $tiene ? $r['mortalidad'] : '';?>">
            </td>
            <!-- Saldo aves — rowspan 2 -->
            <td rowspan="2">
                <input type="number" name="saldo[]" class="inp" min="0"
                    id="saldo_<?php echo $i;?>"
                    value="<?php echo $tiene ? $r['saldo_aves'] : '';?>">
            </td>
        </tr>

        <!-- ── FILA B: UNIDAD ── -->
        <tr class="<?php echo $par_b;?>">
            <td class="lbl-cu">UNIDAD</td>
            <?php foreach($cats as $cat):
                $v = $tiene ? ($r['huevo_'.$cat] ?? '') : '';
            ?>
            <td><input type="number" name="h_<?php echo $cat;?>[]"
                class="inp prod-input" data-dia="<?php echo $i;?>" data-tipo="unidad" data-cat="<?php echo $cat;?>"
                min="0" value="<?php echo $v;?>"></td>
            <?php endforeach;?>
            <!-- Averías unidad -->
            <td><input type="number" name="h_averia[]"
                class="inp prod-input" data-dia="<?php echo $i;?>" data-tipo="unidad" data-cat="averia"
                min="0" value="<?php echo $tiene ? ($r['huevo_averia'] ?? '') : '';?>"></td>
        </tr>

        <!-- Separador visual entre días -->
        <tr class="separador"><td colspan="16"></td></tr>

        <?php endfor;?>
        </tbody>
    </table>

    <div class="footer-form">
        <p>
            ✅ Verde = ya guardado &nbsp;|&nbsp;
            <strong>Total Cartones / Unidades</strong> = conteo separado por tipo &nbsp;|&nbsp;
            <strong>Total Huevos (×30)</strong> = (cartones × 30) + unidades sueltas — <em>Averías NO se suman</em>
        </p>
        <button type="submit" class="btn-save">💾 GUARDAR / ACTUALIZAR REPORTE</button>
    </div>
</form>
</div>

<script>
// Recalcular totales cuando cambia cualquier input de producción
document.addEventListener('input', function(e){
    if (!e.target.classList.contains('prod-input')) return;
    var dia = e.target.dataset.dia;
    recalcular(dia);
});
document.addEventListener('input', function(e){
    if (e.target.name === 'saldo[]') {
        // encontrar qué fila es
        var all = document.querySelectorAll('[name="saldo[]"]');
        all.forEach(function(el, idx){
            if (el === e.target) recalcular(idx+1);
        });
    }
});

function recalcular(dia) {
    var cats = ['c','b','a','aa','aaa','jumbo']; // averías NO se suman al total

    var total_carton = 0; // Suma solo cartones (sin averías)
    var total_und    = 0; // Suma solo unidades sueltas (sin averías)
    var total_huev   = 0; // Huevos reales: (cartones×30) + unidades (sin averías)

    cats.forEach(function(cat){
        var cInput = document.querySelector('[name="carton_'+cat+'[]"][data-dia="'+dia+'"]');
        var uInput = document.querySelector('[name="h_'+cat+'[]"][data-dia="'+dia+'"]');
        var c = parseInt(cInput ? cInput.value : 0) || 0;
        var u = parseInt(uInput ? uInput.value  : 0) || 0;
        total_carton += c;
        total_und    += u;
        total_huev   += (c * 30) + u;
    });

    var tcartEl = document.getElementById('tcart_' + dia);
    var tundEl  = document.getElementById('tund_'  + dia);
    var totalEl = document.getElementById('total_' + dia);
    var postEl  = document.getElementById('postura_'+ dia);
    var saldoEl = document.getElementById('saldo_' + dia);

    if (tcartEl) tcartEl.value = total_carton;
    if (tundEl)  tundEl.value  = total_und;
    if (totalEl) totalEl.value = total_huev;

    var saldo = parseInt(saldoEl ? saldoEl.value : 0) || 0;
    if (postEl) postEl.value = saldo > 0 ? ((total_huev / saldo) * 100).toFixed(2) + '%' : '';
}

// Recalcular al cargar para filas ya guardadas
document.addEventListener('DOMContentLoaded', function(){
    var dias = <?php echo date('t');?>;
    for (var i = 1; i <= dias; i++) recalcular(i);
});
</script>
</body>
</html>

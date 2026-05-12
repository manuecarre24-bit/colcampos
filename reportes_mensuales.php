<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$mes      = isset($_GET['mes'])    ? intval($_GET['mes'])    : intval(date('m'));
$anio     = isset($_GET['anio'])   ? intval($_GET['anio'])   : intval(date('Y'));
$granja_f = isset($_GET['granja']) ? $_GET['granja']         : 'todas';

$meses_nombres = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$mes_nombre = $meses_nombres[$mes];

// Granjas avícolas + café
$granjas_avicola = ($granja_f == 'todas' || $granja_f == 'avicola')
    ? ['ponderosa','lupe']
    : (in_array($granja_f,['ponderosa','lupe']) ? [$granja_f] : []);
$mostrar_cafe = ($granja_f == 'todas' || $granja_f == 'cafe');
$nombres_granjas = ['ponderosa'=>'La Ponderosa','lupe'=>'La Lupe'];

// ── Cargar datos ──
$datos = [];
foreach ($granjas_avicola as $g) {
    $r = mysqli_query($conexion,"SELECT * FROM registros_produccion WHERE id_granja='$g' AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio' ORDER BY num_galpon,fecha ASC");
    while ($row=mysqli_fetch_assoc($r)) $datos[$g]['produccion'][]=$row;

    $r = mysqli_query($conexion,"SELECT * FROM registros_alimentacion WHERE id_granja='$g' AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio' ORDER BY num_galpon,fecha ASC");
    while ($row=mysqli_fetch_assoc($r)) $datos[$g]['alimentacion'][]=$row;

    $r = mysqli_query($conexion,"SELECT * FROM costos_generales WHERE id_granja='$g' AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio' ORDER BY num_galpon,fecha ASC");
    while ($row=mysqli_fetch_assoc($r)) $datos[$g]['costos'][]=$row;

    $r = mysqli_query($conexion,"SELECT * FROM inventario WHERE id_granja='$g' ORDER BY fecha DESC");
    while ($row=mysqli_fetch_assoc($r)) $datos[$g]['inventario'][]=$row;
}

// Café
if ($mostrar_cafe) {
    for ($lote=1;$lote<=3;$lote++) {
        $r = mysqli_query($conexion,"SELECT * FROM cafe_labores WHERE num_lote='$lote' AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio' ORDER BY fecha ASC");
        while ($row=mysqli_fetch_assoc($r)) $datos['cafe']['lote'.$lote][]=$row;
    }
    $r = mysqli_query($conexion,"SELECT * FROM inventario WHERE id_granja='cafe' ORDER BY fecha DESC");
    while ($row=mysqli_fetch_assoc($r)) $datos['cafe']['inventario'][]=$row;
}

// Cerrar mes
if (isset($_POST['confirmar_limpieza'])) {
    mysqli_query($conexion,"DELETE FROM registros_produccion WHERE MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'");
    mysqli_query($conexion,"DELETE FROM registros_alimentacion WHERE MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'");
    mysqli_query($conexion,"DELETE FROM costos_generales WHERE MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'");
    mysqli_query($conexion,"DELETE FROM cafe_labores WHERE MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'");
    echo "<script>alert('Mes cerrado correctamente.');window.location.href='dashboard.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reportes — COLCAMPOS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter','Segoe UI',sans-serif;background:#f0f4f8}

/* ── BARRA CONTROLES ── */
.no-print{background:#263238;color:#fff;padding:12px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
.no-print h1{font-size:15px;font-weight:700}
.controles{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.np-btn{font-family:inherit;font-size:12px;font-weight:600;padding:7px 13px;border-radius:6px;cursor:pointer;border:1px solid rgba(255,255,255,.35);text-decoration:none;color:#fff;background:transparent;transition:background .2s}
.np-btn:hover{background:rgba(255,255,255,.15)}
.np-sel{font-family:inherit;font-size:12px;font-weight:600;padding:7px 13px;border-radius:6px;cursor:pointer;border:1px solid rgba(255,255,255,.35);color:#fff;background:rgba(255,255,255,.1)}
.np-sel option{color:#222;background:#fff}
.btn-pdf{background:#c62828!important;border:none!important}
.btn-pdf:hover{filter:brightness(.88)}
.btn-cerrar{background:#455a64!important;border:none!important}

/* ── REPORTE ── */
.reporte{max-width:1100px;margin:18px auto;padding:0 14px 40px}

.portada{background:#064e22;color:#fff;padding:22px 26px;border-radius:10px;margin-bottom:22px;display:flex;justify-content:space-between;align-items:center}
.portada h2{font-size:19px;font-weight:800}
.portada p{font-size:12px;opacity:.85;margin-top:3px}
.fecha-badge{background:rgba(255,255,255,.15);padding:9px 16px;border-radius:8px;font-size:14px;font-weight:700;text-align:center;white-space:nowrap}

/* ── SECCIONES ── */
.seccion{margin-bottom:26px;border:2px solid #ddd;border-radius:10px;overflow:hidden}
.granja-header{padding:12px 20px;font-size:14px;font-weight:700;color:#fff}
.gh-ponderosa{background:#064e22}
.gh-lupe{background:#d35400}
.gh-cafe{background:#6d0f2a}

.subtitulo{background:#eceff1;padding:8px 18px;font-size:11px;font-weight:700;color:#37474f;border-bottom:1px solid #cfd8dc}
.sin-datos{text-align:center;padding:14px;color:#888;font-size:11px;font-style:italic}
.sub-galpon{padding:4px 18px;font-size:11px;font-weight:700}

/* ── RESUMEN ESTADÍSTICAS ── */
.resumen{display:flex;gap:10px;padding:12px 16px;background:#fafafa;border-top:1px solid #e0e0e0;flex-wrap:wrap}
.stat{background:#fff;border:1px solid #e0e0e0;border-radius:7px;padding:8px 12px;text-align:center;min-width:105px}
.stat .val{font-size:16px;font-weight:800;color:#064e22}
.stat .lbl{font-size:9px;color:#666;margin-top:2px}

/* ── TABLAS ── */
table.tbl{width:100%;border-collapse:collapse;font-size:10px}
table.tbl th{padding:6px 3px;text-align:center;border:1px solid rgba(255,255,255,.2);font-size:9px;font-weight:700;color:#fff}
table.tbl th.grp{padding:7px 3px}
table.tbl th.sub{font-size:8px;font-weight:600;background:rgba(0,0,0,.15)}
table.tbl td{border:1px solid #cfd8dc;padding:5px 3px;text-align:center;color:#333}
table.tbl tr:nth-child(even) td{background:#f7f7f7}
table.tbl td.izq{text-align:left;padding-left:7px}
table.tbl tr.fila-total td{font-weight:700}
table.tbl tr.fila-total-verde td{background:#e8f5e9;font-weight:700}
table.tbl tr.fila-total-naranja td{background:#fff3e0;font-weight:700}
table.tbl tr.fila-total-cafe td{background:#f9f0f3;font-weight:700}
table.tbl tr.stock-bajo td{background:#fff3e0}

table.tbl.verde th{background:#064e22}
table.tbl.naranja th{background:#d35400}
table.tbl.azul th{background:#1565c0}
table.tbl.cafe th{background:#6d0f2a}
table.tbl.gris th{background:#37474f}

.pie{text-align:center;color:#aaa;font-size:9px;margin-top:16px;padding-top:8px;border-top:1px solid #e0e0e0}

/* ── IMPRESIÓN ── */
@media print{
    body{background:#fff;font-size:9px}
    .no-print{display:none!important}
    .reporte{max-width:100%;margin:0;padding:0}
    .portada{border-radius:0;-webkit-print-color-adjust:exact;print-color-adjust:exact}
    .seccion{page-break-inside:avoid;border:1px solid #ccc;margin-bottom:14px}
    .granja-header,.resumen{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    table.tbl th{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    table.tbl tr:nth-child(even) td{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    table.tbl tr.fila-total-verde td,
    table.tbl tr.fila-total-naranja td,
    table.tbl tr.fila-total-cafe td{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    .stat .val{color:#064e22!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
    @page{margin:1cm;size:A4 landscape}
}
</style>
</head>
<body>

<!-- BARRA CONTROLES -->
<div class="no-print">
    <h1>📊 REPORTES MENSUALES — COLCAMPOS</h1>
    <div class="controles">
        <a href="dashboard.php" class="np-btn">🏠 Inicio</a>
        <select class="np-sel" onchange="setParam('mes',this.value)">
            <?php for($m=1;$m<=12;$m++): ?>
            <option value="<?php echo $m;?>" <?php echo $m==$mes?'selected':'';?>><?php echo $meses_nombres[$m];?></option>
            <?php endfor;?>
        </select>
        <select class="np-sel" onchange="setParam('anio',this.value)">
            <?php for($a=2024;$a<=2030;$a++): ?>
            <option value="<?php echo $a;?>" <?php echo $a==$anio?'selected':'';?>><?php echo $a;?></option>
            <?php endfor;?>
        </select>
        <select class="np-sel" onchange="setParam('granja',this.value)">
            <option value="todas"     <?php echo $granja_f=='todas'?'selected':'';?>>Todo</option>
            <option value="ponderosa" <?php echo $granja_f=='ponderosa'?'selected':'';?>>La Ponderosa</option>
            <option value="lupe"      <?php echo $granja_f=='lupe'?'selected':'';?>>La Lupe</option>
            <option value="cafe"      <?php echo $granja_f=='cafe'?'selected':'';?>>Café</option>
        </select>
        <button class="np-btn btn-pdf" onclick="window.print()">🖨️ IMPRIMIR / PDF</button>
        <form method="POST" style="display:inline" onsubmit="return confirm('¿Seguro? Borrará producción, alimentación, costos y café de <?php echo $mes_nombre.' '.$anio;?>. ¿Ya guardaste el PDF?')">
            <input type="hidden" name="confirmar_limpieza" value="1">
            <button type="submit" class="np-btn btn-cerrar">🔄 Cerrar Mes</button>
        </form>
    </div>
</div>

<!-- REPORTE -->
<div class="reporte">

    <!-- Portada -->
    <div class="portada">
        <div>
            <h2>COLCAMPOS — Reporte Mensual</h2>
            <p>Granjas La Ponderosa, La Lupe &amp; Café</p>
            <p>Generado: <?php echo date('d/m/Y H:i');?></p>
        </div>
        <div class="fecha-badge"><?php echo strtoupper($mes_nombre);?><br><?php echo $anio;?></div>
    </div>

    <!-- ══════ GRANJAS AVÍCOLAS ══════ -->
    <?php foreach($granjas_avicola as $g):
        $ng  = $nombres_granjas[$g];
        $cls = $g=='ponderosa'?'verde':'naranja';
        $cg  = $g=='ponderosa'?'#064e22':'#d35400';
        $prod  = $datos[$g]['produccion']   ?? [];
        $alim  = $datos[$g]['alimentacion'] ?? [];
        $costos= $datos[$g]['costos']        ?? [];
        $inv   = $datos[$g]['inventario']    ?? [];

        // Totales resumen
        $th  = array_sum(array_column($prod,'total_huevos'));
        $tm  = array_sum(array_column($prod,'mortalidad'));
        $tak = array_sum(array_column($alim,'cantidad_alimento'));
        $tav = array_sum(array_column($alim,'v_total'));
        $tcv = array_sum(array_column($costos,'valor'));
    ?>
    <div class="seccion">
        <div class="granja-header gh-<?php echo $g;?>">
            🏡 <?php echo strtoupper($ng);?> — <?php echo strtoupper($mes_nombre.' '.$anio);?>
        </div>

        <!-- Resumen -->
        <div class="resumen">
            <div class="stat"><div class="val"><?php echo number_format($th);?></div><div class="lbl">Total Huevos</div></div>
            <div class="stat"><div class="val"><?php echo $tm;?></div><div class="lbl">Mortalidad</div></div>
            <div class="stat"><div class="val"><?php echo number_format($tak,1);?> kg</div><div class="lbl">Alimento consumido</div></div>
            <div class="stat"><div class="val">$<?php echo number_format($tav,0,',','.');?></div><div class="lbl">Costo alimentación</div></div>
            <div class="stat"><div class="val">$<?php echo number_format($tcv,0,',','.');?></div><div class="lbl">Costos generales</div></div>
        </div>

        <!-- PRODUCCIÓN con cartones y unidades -->
        <div class="subtitulo">🥚 PRODUCCIÓN — CARTONES Y UNIDADES POR GALPÓN</div>
        <?php if(empty($prod)): ?>
            <div class="sin-datos">Sin registros de producción para este mes.</div>
        <?php else:
            $pg=[];
            foreach($prod as $p) $pg[$p['num_galpon']][]=$p;
            ksort($pg);

            // Totales generales de la granja
            $g_cart_total=0; $g_und_total=0;

            foreach($pg as $ng2=>$filas):
                // Calcular totales del galpón
                $g_cart=0; $g_und=0; $g_huevos=0; $g_mort=0;
                foreach($filas as $f){
                    $cats=['c','b','a','aa','aaa','jumbo'];
                    foreach($cats as $cat){
                        $ck='carton_'.$cat; $hk='huevo_'.$cat;
                        $g_cart += isset($f[$ck])?intval($f[$ck]):0;
                        $g_und  += isset($f[$hk])?intval($f[$hk]):0;
                    }
                    $g_und += intval($f['huevo_averia']??0);
                    $g_huevos += $f['total_huevos'];
                    $g_mort   += $f['mortalidad'];
                }
                $g_cart_total += $g_cart;
                $g_und_total  += $g_und;
        ?>
            <div class="sub-galpon" style="color:<?php echo $cg;?>;padding:6px 18px 2px;">
                Galpón <?php echo $ng2;?> &nbsp;|&nbsp;
                <span style="font-weight:400;color:#555">
                    Total cartones: <strong><?php echo number_format($g_cart);?></strong> &nbsp;·&nbsp;
                    Total unidades: <strong><?php echo number_format($g_und);?></strong> &nbsp;·&nbsp;
                    Total huevos: <strong><?php echo number_format($g_huevos);?></strong> &nbsp;·&nbsp;
                    Mortalidad: <strong><?php echo $g_mort;?></strong>
                </span>
            </div>
            <table class="tbl <?php echo $cls;?>">
                <thead>
                    <tr>
                        <th class="grp" rowspan="2">Fecha</th>
                        <th class="grp" rowspan="2">Sem.</th>
                        <th class="grp" colspan="2">C</th>
                        <th class="grp" colspan="2">B</th>
                        <th class="grp" colspan="2">A</th>
                        <th class="grp" colspan="2">AA</th>
                        <th class="grp" colspan="2">AAA</th>
                        <th class="grp" colspan="2">Jumbo</th>
                        <th class="grp" rowspan="2">Avería</th>
                        <th class="grp" rowspan="2">Total</th>
                        <th class="grp" rowspan="2">% Post.</th>
                        <th class="grp" rowspan="2">Mort.</th>
                        <th class="grp" rowspan="2">Saldo</th>
                    </tr>
                    <tr>
                        <?php foreach(['c','b','a','aa','aaa','jumbo'] as $cat): ?>
                        <th class="sub">Ctón</th><th class="sub">Und</th>
                        <?php endforeach;?>
                    </tr>
                </thead>
                <tbody>
                <?php
                $tot_huev=0; $tot_mort=0; $tot_cart=0; $tot_und=0;
                foreach($filas as $f):
                    $fila_cart=0; $fila_und=0;
                    foreach(['c','b','a','aa','aaa','jumbo'] as $cat){
                        $fila_cart += intval($f['carton_'.$cat]??0);
                        $fila_und  += intval($f['huevo_'.$cat]??0);
                    }
                    $tot_huev += $f['total_huevos'];
                    $tot_mort += $f['mortalidad'];
                    $tot_cart += $fila_cart;
                    $tot_und  += $fila_und;
                ?>
                <tr>
                    <td><?php echo date('d/m',strtotime($f['fecha']));?></td>
                    <td><?php echo $f['semana_aves'];?></td>
                    <?php foreach(['c','b','a','aa','aaa','jumbo'] as $cat): ?>
                    <td><?php echo $f['carton_'.$cat]??0;?></td>
                    <td><?php echo $f['huevo_'.$cat]??0;?></td>
                    <?php endforeach;?>
                    <td><?php echo $f['huevo_averia']??0;?></td>
                    <td><strong><?php echo $f['total_huevos'];?></strong></td>
                    <td><?php echo $f['porcentaje_postura'];?>%</td>
                    <td><?php echo $f['mortalidad'];?></td>
                    <td><?php echo $f['saldo_aves'];?></td>
                </tr>
                <?php endforeach;?>
                <tr class="fila-total-<?php echo $g=='ponderosa'?'verde':'naranja';?>">
                    <td colspan="2" style="text-align:right;padding-right:6px">TOTAL G<?php echo $ng2;?>:</td>
                    <td colspan="2"><?php echo $tot_cart;?> ctón</td>
                    <td colspan="2"></td><td colspan="2"></td>
                    <td colspan="2"></td><td colspan="2"></td>
                    <td></td>
                    <td><?php echo $tot_und;?> und</td>
                    <td></td>
                    <td><strong><?php echo $tot_huev;?></strong></td>
                    <td>—</td>
                    <td><?php echo $tot_mort;?></td>
                    <td>—</td>
                </tr>
                </tbody>
            </table>
        <?php endforeach;?>

            <!-- Resumen total granja -->
            <div style="background:#e8f5e9;padding:8px 18px;font-size:11px;font-weight:700;color:#064e22;border-top:2px solid #a5d6a7;">
                📊 RESUMEN <?php echo strtoupper($ng);?>:
                Total cartones: <?php echo number_format($g_cart_total);?> &nbsp;|&nbsp;
                Total unidades: <?php echo number_format($g_und_total);?> &nbsp;|&nbsp;
                Total huevos: <?php echo number_format($th);?>
            </div>
        <?php endif;?>

        <!-- ALIMENTACIÓN -->
        <div class="subtitulo">🌾 ALIMENTACIÓN</div>
        <?php if(empty($alim)): ?>
            <div class="sin-datos">Sin registros de alimentación para este mes.</div>
        <?php else:
            $pa=[];foreach($alim as $a) $pa[$a['num_galpon']][]=$a; ksort($pa);
            foreach($pa as $ng2=>$filas):
                $tkg=0;$tv=0;
        ?>
            <div class="sub-galpon" style="color:<?php echo $cg;?>">Galpón <?php echo $ng2;?></div>
            <table class="tbl <?php echo $cls;?>">
                <thead><tr><th>Fecha</th><th>Cantidad (Kg)</th><th>Gramos/Ave</th><th>Valor Unitario</th><th>Valor Total</th></tr></thead>
                <tbody>
                <?php foreach($filas as $f): $tkg+=$f['cantidad_alimento'];$tv+=$f['v_total'];?>
                <tr>
                    <td><?php echo date('d/m',strtotime($f['fecha']));?></td>
                    <td><?php echo number_format($f['cantidad_alimento'],2);?></td>
                    <td><?php echo number_format($f['gramos_por_ave'],2);?></td>
                    <td>$<?php echo number_format($f['v_unitario'],2);?></td>
                    <td><strong>$<?php echo number_format($f['v_total'],2);?></strong></td>
                </tr>
                <?php endforeach;?>
                <tr class="fila-total-<?php echo $g=='ponderosa'?'verde':'naranja';?>">
                    <td style="text-align:right;padding-right:6px">TOTAL:</td>
                    <td><?php echo number_format($tkg,2);?> kg</td><td>—</td><td>—</td>
                    <td>$<?php echo number_format($tv,2);?></td>
                </tr>
                </tbody>
            </table>
        <?php endforeach; endif;?>

        <!-- COSTOS GENERALES -->
        <div class="subtitulo">💰 COSTOS GENERALES</div>
        <?php if(empty($costos)): ?>
            <div class="sin-datos">Sin registros de costos para este mes.</div>
        <?php else:
            $pc=[];foreach($costos as $c) $pc[$c['num_galpon']][]=$c; ksort($pc);
            foreach($pc as $ng2=>$filas):
                $tcosto=0;
        ?>
            <div class="sub-galpon" style="color:<?php echo $cg;?>">Galpón <?php echo $ng2;?></div>
            <table class="tbl gris">
                <thead><tr><th>Fecha</th><th>Concepto</th><th>Cantidad</th><th>Valor</th><th>Observaciones</th></tr></thead>
                <tbody>
                <?php foreach($filas as $f): $tcosto+=$f['valor'];?>
                <tr>
                    <td><?php echo date('d/m',strtotime($f['fecha']));?></td>
                    <td class="izq"><?php echo htmlspecialchars($f['concepto']);?></td>
                    <td><?php echo $f['cantidad'];?></td>
                    <td>$<?php echo number_format($f['valor'],0,',','.');?></td>
                    <td class="izq"><?php echo htmlspecialchars($f['observaciones']);?></td>
                </tr>
                <?php endforeach;?>
                <tr class="fila-total-<?php echo $g=='ponderosa'?'verde':'naranja';?>">
                    <td colspan="3" style="text-align:right;padding-right:6px">TOTAL:</td>
                    <td>$<?php echo number_format($tcosto,0,',','.');?></td><td></td>
                </tr>
                </tbody>
            </table>
        <?php endforeach; endif;?>

        <!-- INVENTARIO -->
        <div class="subtitulo">📦 INVENTARIO</div>
        <?php if(empty($inv)): ?>
            <div class="sin-datos">Sin registros de inventario.</div>
        <?php else:?>
        <table class="tbl azul">
            <thead><tr><th>#</th><th>Fecha</th><th>Artículo</th><th>Cantidad</th><th>Descripción</th><th>Ingreso ($)</th></tr></thead>
            <tbody>
            <?php $tinv=0; foreach($inv as $idx=>$item): $tinv+=$item['ingreso'];?>
            <tr>
                <td><?php echo $idx+1;?></td>
                <td><?php echo date('d/m/Y',strtotime($item['fecha']));?></td>
                <td class="izq"><?php echo htmlspecialchars($item['articulo']);?></td>
                <td><?php echo number_format($item['cantidad'],2);?></td>
                <td class="izq"><?php echo htmlspecialchars($item['descripcion']);?></td>
                <td>$<?php echo number_format($item['ingreso'],0,',','.');?></td>
            </tr>
            <?php endforeach;?>
            <tr class="fila-total-<?php echo $g=='ponderosa'?'verde':'naranja';?>">
                <td colspan="5" style="text-align:right;padding-right:6px">TOTAL INGRESOS:</td>
                <td>$<?php echo number_format($tinv,0,',','.');?></td>
            </tr>
            </tbody>
        </table>
        <?php endif;?>

    </div><!-- fin seccion granja -->
    <?php endforeach;?>

    <!-- ══════ CAFÉ ══════ -->
    <?php if($mostrar_cafe):
        $cafe_total_general = 0;
    ?>
    <div class="seccion">
        <div class="granja-header gh-cafe">☕ CAFÉ — <?php echo strtoupper($mes_nombre.' '.$anio);?></div>

        <?php for($lote=1;$lote<=3;$lote++):
            $labores = $datos['cafe']['lote'.$lote] ?? [];
            $t_ins=0; $t_mo=0; $t_tot=0;
        ?>
        <div class="subtitulo">🌱 LOTE <?php echo $lote;?></div>
        <?php if(empty($labores)): ?>
            <div class="sin-datos">Sin registros para el Lote <?php echo $lote;?> en este mes.</div>
        <?php else:?>
        <table class="tbl cafe">
            <thead><tr><th>Fecha</th><th>Labor Realizada</th><th>Insumo</th><th>Valor Insumo</th><th>Mano de Obra</th><th>Total</th><th>Observaciones</th></tr></thead>
            <tbody>
            <?php foreach($labores as $lab): $t_ins+=$lab['valor_insumo'];$t_mo+=$lab['valor_mano_obra'];$t_tot+=$lab['total'];?>
            <tr>
                <td><?php echo date('d/m',strtotime($lab['fecha']));?></td>
                <td class="izq"><?php echo htmlspecialchars($lab['labor_realizada']);?></td>
                <td class="izq"><?php echo htmlspecialchars($lab['insumo']);?></td>
                <td>$<?php echo number_format($lab['valor_insumo'],0,',','.');?></td>
                <td>$<?php echo number_format($lab['valor_mano_obra'],0,',','.');?></td>
                <td><strong>$<?php echo number_format($lab['total'],0,',','.');?></strong></td>
                <td class="izq"><?php echo htmlspecialchars($lab['observaciones']);?></td>
            </tr>
            <?php endforeach;?>
            <tr class="fila-total-cafe">
                <td colspan="3" style="text-align:right;padding-right:6px">TOTAL LOTE <?php echo $lote;?>:</td>
                <td>$<?php echo number_format($t_ins,0,',','.');?></td>
                <td>$<?php echo number_format($t_mo,0,',','.');?></td>
                <td>$<?php echo number_format($t_tot,0,',','.');?></td>
                <td></td>
            </tr>
            </tbody>
        </table>
        <?php $cafe_total_general+=$t_tot; endif;?>
        <?php endfor;?>

        <!-- Inventario café -->
        <?php $inv_cafe = $datos['cafe']['inventario'] ?? [];?>
        <div class="subtitulo">📦 INVENTARIO CAFÉ</div>
        <?php if(empty($inv_cafe)):?>
            <div class="sin-datos">Sin registros de inventario para café.</div>
        <?php else:?>
        <table class="tbl cafe">
            <thead><tr><th>#</th><th>Fecha</th><th>Artículo</th><th>Cantidad</th><th>Descripción</th><th>Ingreso ($)</th></tr></thead>
            <tbody>
            <?php $tinvc=0; foreach($inv_cafe as $idx=>$item): $tinvc+=$item['ingreso'];?>
            <tr>
                <td><?php echo $idx+1;?></td>
                <td><?php echo date('d/m/Y',strtotime($item['fecha']));?></td>
                <td class="izq"><?php echo htmlspecialchars($item['articulo']);?></td>
                <td><?php echo number_format($item['cantidad'],2);?></td>
                <td class="izq"><?php echo htmlspecialchars($item['descripcion']);?></td>
                <td>$<?php echo number_format($item['ingreso'],0,',','.');?></td>
            </tr>
            <?php endforeach;?>
            <tr class="fila-total-cafe">
                <td colspan="5" style="text-align:right;padding-right:6px">TOTAL INGRESOS:</td>
                <td>$<?php echo number_format($tinvc,0,',','.');?></td>
            </tr>
            </tbody>
        </table>
        <?php endif;?>

        <!-- Resumen total café -->
        <div style="background:#f9f0f3;padding:8px 18px;font-size:11px;font-weight:700;color:#6d0f2a;border-top:2px solid #c9a0ac;">
            ☕ TOTAL GENERAL CAFÉ <?php echo strtoupper($mes_nombre.' '.$anio);?>: $<?php echo number_format($cafe_total_general,0,',','.');?>
        </div>

    </div><!-- fin seccion café -->
    <?php endif;?>

    <div class="pie">COLCAMPOS v3.0 — Reporte generado el <?php echo date('d/m/Y H:i');?> — La Ponderosa · La Lupe · Café</div>
</div>

<script>
function setParam(k,v){var u=new URL(window.location.href);u.searchParams.set(k,v);window.location.href=u.toString();}
</script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$granja = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$galpon = isset($_GET['galpon']) ? intval($_GET['galpon']) : 1;
$color  = $granja == 'lupe' ? '#b84600' : '#37474f';
$color_light = $granja == 'lupe' ? '#fef3ec' : '#f5f7f8';
$nombre_granja = $granja == 'lupe' ? 'La Lupe' : 'La Ponderosa';
$mes = date('m'); $anio = date('Y');

$registros = [];
$sql = "SELECT * FROM costos_generales WHERE id_granja='$granja' AND num_galpon='$galpon'
        AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio' ORDER BY fecha ASC";
$res = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($res)) $registros[] = $row;
$total_filas = max(15, count($registros) + 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Costos Generales — <?php echo $nombre_granja;?> G<?php echo $galpon;?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter','Segoe UI',sans-serif;background:#f4f7f6}
        .nav-top{background:<?php echo $color;?>;color:#fff;padding:12px 20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 8px rgba(0,0,0,.25)}
        .nav-top .izq{display:flex;gap:8px}
        .nav-top a{color:#fff;text-decoration:none;font-weight:600;font-size:13px;padding:6px 13px;border-radius:6px;border:1px solid rgba(255,255,255,.4);transition:background .2s}
        .nav-top a:hover{background:rgba(255,255,255,.2)}
        .nav-top .titulo{font-weight:700;font-size:15px}
        .mes-badge{background:#fff;color:<?php echo $color;?>;font-weight:700;font-size:13px;text-align:center;padding:9px;border-bottom:2px solid <?php echo $color;?>}
        .table-wrap{padding:16px;overflow-x:auto}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.1);min-width:700px}
        th{background:<?php echo $color;?>;color:#fff;padding:10px 6px;border:1px solid rgba(255,255,255,.2);font-size:11px;font-weight:700;text-align:center}
        td{border:1px solid #dde3e8;padding:0}
        tr.tiene-datos{background:#f1f8e9}
        tr.tiene-datos td{border-color:#b2dfdb}
        .inp{width:100%;border:none;padding:10px 6px;background:<?php echo $color_light;?>;text-align:center;outline:none;font-size:12px;font-family:inherit;color:#222;transition:background .15s}
        .inp:focus{background:#fff;border-bottom:2px solid <?php echo $color;?>}
        .inp.izq{text-align:left;padding-left:8px}
        .inp[readonly]{background:#c8ddc8;font-weight:700;color:#1b5e20;cursor:default}
        .num-cell{text-align:center;padding:8px 4px;font-weight:700;color:#555;font-size:11px;background:#f5f5f5}
        .btn-agregar{display:flex;align-items:center;gap:8px;background:<?php echo $color_light;?>;color:<?php echo $color;?>;border:2px dashed <?php echo $color;?>;padding:11px 22px;border-radius:7px;cursor:pointer;font-weight:700;font-size:14px;font-family:inherit;transition:background .2s;margin-top:14px}
        .btn-agregar:hover{filter:brightness(.93);transform:scale(1.02)}
        .footer-form{margin:14px;background:#fff;padding:18px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
        .footer-form p{color:#666;font-size:12px}
        .btn-save{background:<?php echo $color;?>;color:#fff;padding:13px 34px;border:none;border-radius:7px;cursor:pointer;font-weight:700;font-size:15px;transition:filter .2s}
        .btn-save:hover{filter:brightness(.88)}
        @media print{
            .nav-top,.mes-badge,.btn-agregar,.footer-form{display:none!important}
            body{background:#fff}.table-wrap{padding:0}
            table{box-shadow:none;border-radius:0;min-width:100%}
            th{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            @page{margin:1cm;size:A4 landscape}
        }
    </style>
</head>
<body>
<div class="nav-top">
    <div class="izq">
        <a href="menu_galpon.php?granja=<?php echo $granja;?>&galpon=<?php echo $galpon;?>">⬅ Menú</a>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
    <div class="titulo">💰 COSTOS GENERALES — <?php echo strtoupper($nombre_granja);?> · GALPÓN <?php echo $galpon;?></div>
</div>
<div class="mes-badge">
    📅 <?php echo date('m/Y');?> &nbsp;|&nbsp; <?php echo $nombre_granja;?> · Galpón <?php echo $galpon;?> &nbsp;|&nbsp;
    Registros: <strong><?php echo count($registros);?></strong> &nbsp;|&nbsp;
    <span style="color:#888;font-size:12px">Ctrl+P para imprimir</span>
</div>
<div class="table-wrap">
<form action="guardar_costos.php" method="POST">
    <input type="hidden" name="id_granja" value="<?php echo $granja;?>">
    <input type="hidden" name="num_galpon" value="<?php echo $galpon;?>">
    <table id="tablaCostos">
        <thead>
            <tr>
                <th style="width:34px">#</th>
                <th style="width:105px">FECHA</th>
                <th>CONCEPTO</th>
                <th style="width:110px">CANTIDAD</th>
                <th style="width:130px">VALOR ($)</th>
                <th>OBSERVACIONES</th>
            </tr>
        </thead>
        <tbody id="cuerpo-costos">
        <?php for($i=0;$i<$total_filas;$i++):
            $r=$registros[$i]??null; $tiene=$r!==null;?>
        <tr class="<?php echo $tiene?'tiene-datos':'';?>">
            <td><div class="num-cell"><?php echo $i+1;?></div></td>
            <td><input type="date" name="fecha[]" class="inp" value="<?php echo $tiene?$r['fecha']:date('Y-m-d');?>"></td>
            <td><input type="text" name="concepto[]" class="inp izq" value="<?php echo $tiene?htmlspecialchars($r['concepto']):'';?>" placeholder="Ej: Vacuna, Mantenimiento..."></td>
            <td><input type="number" name="cantidad[]" class="inp" step="0.01" min="0" value="<?php echo $tiene?$r['cantidad']:'0';?>"></td>
            <td><input type="number" name="valor[]" class="inp" step="0.01" min="0" value="<?php echo $tiene?$r['valor']:'0';?>"></td>
            <td><input type="text" name="observaciones[]" class="inp izq" value="<?php echo $tiene?htmlspecialchars($r['observaciones']):'';?>" placeholder="Observaciones..."></td>
        </tr>
        <?php endfor;?>
        </tbody>
    </table>
    <button type="button" class="btn-agregar" onclick="agregarFilaCosto()">➕ Agregar fila</button>
    <div class="footer-form">
        <p>✅ Verde = ya guardado &nbsp;|&nbsp; Ctrl+P imprime solo la tabla</p>
        <button type="submit" class="btn-save">💾 GUARDAR COSTOS</button>
    </div>
</form>
</div>
<script>
var contCosto=<?php echo $total_filas;?>;
function agregarFilaCosto(){
    var tbody=document.getElementById('cuerpo-costos');
    var i=contCosto;
    var tr=document.createElement('tr');
    tr.innerHTML='<td><div class="num-cell">'+(i+1)+'</div></td>'+
        '<td><input type="date" name="fecha[]" class="inp" value="<?php echo date('Y-m-d');?>"></td>'+
        '<td><input type="text" name="concepto[]" class="inp izq" placeholder="Ej: Vacuna..."></td>'+
        '<td><input type="number" name="cantidad[]" class="inp" step="0.01" min="0" value="0"></td>'+
        '<td><input type="number" name="valor[]" class="inp" step="0.01" min="0" value="0"></td>'+
        '<td><input type="text" name="observaciones[]" class="inp izq" placeholder="Observaciones..."></td>';
    tbody.appendChild(tr);
    contCosto++;
    tr.querySelector('input[name="concepto[]"]').focus();
}
</script>
</body>
</html>

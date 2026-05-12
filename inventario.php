<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$granja = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$nombre_granja = $granja == 'lupe' ? 'La Lupe' : ($granja == 'cafe' ? 'Café' : 'La Ponderosa');
$color  = $granja == 'lupe' ? '#d35400' : ($granja == 'cafe' ? '#6d0f2a' : '#37474f');
$color_light = $granja == 'lupe' ? '#fef3ec' : ($granja == 'cafe' ? '#fdf0f3' : '#f5f7f8');

$registros = [];
$sql = "SELECT * FROM inventario WHERE id_granja='$granja' ORDER BY fecha DESC, id DESC";
$res = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($res)) $registros[] = $row;
$total_filas = max(12, count($registros) + 3);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario — <?php echo $nombre_granja;?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter','Segoe UI',sans-serif;background:#f4f7f6}
        .header{background:<?php echo $color;?>;color:#fff;padding:14px 22px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 8px rgba(0,0,0,.25)}
        .header h2{font-size:17px;font-weight:700}
        .header .nav{display:flex;gap:8px}
        .header a{color:#fff;text-decoration:none;font-weight:600;font-size:13px;padding:7px 13px;border-radius:6px;border:1px solid rgba(255,255,255,.4);transition:background .2s}
        .header a:hover{background:rgba(255,255,255,.2)}
        .info-badge{background:#fff;color:<?php echo $color;?>;font-weight:700;font-size:13px;text-align:center;padding:9px;border-bottom:2px solid <?php echo $color;?>}
        .wrap{padding:18px}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.1)}
        th{background:<?php echo $color;?>;color:#fff;padding:12px 8px;border:1px solid rgba(255,255,255,.2);font-size:12px;font-weight:700;text-align:center}
        td{border:1px solid #cfd8dc;padding:0}
        tr.tiene-datos{background:#f1f8e9}
        tr.tiene-datos td{border-color:#aed581}
        .inp{width:100%;border:none;padding:11px 8px;background:<?php echo $color_light;?>;text-align:center;outline:none;font-size:12px;font-family:inherit;color:#222;transition:background .15s}
        .inp:focus{background:#fff;border-bottom:2px solid <?php echo $color;?>}
        .inp.izq{text-align:left;padding-left:8px}
        .num-cell{text-align:center;padding:8px 5px;font-weight:700;color:#555;font-size:11px;background:#f5f5f5}
        .btn-agregar{display:flex;align-items:center;gap:8px;background:<?php echo $color_light;?>;color:<?php echo $color;?>;border:2px dashed <?php echo $color;?>;padding:11px 22px;border-radius:7px;cursor:pointer;font-weight:700;font-size:14px;font-family:inherit;transition:background .2s,transform .15s;margin-top:14px}
        .btn-agregar:hover{filter:brightness(.93);transform:scale(1.02)}
        .footer-form{margin-top:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
        .footer-form p{color:#666;font-size:12px}
        .btn-save{background:<?php echo $color;?>;color:#fff;padding:13px 30px;border:none;border-radius:7px;cursor:pointer;font-weight:700;font-size:15px;transition:filter .2s,transform .2s}
        .btn-save:hover{filter:brightness(.88);transform:scale(1.02)}

        @media print{
            .header,.btn-agregar,.footer-form,.info-badge{display:none!important}
            body{background:#fff}
            .wrap{padding:0}
            table{box-shadow:none;border-radius:0}
            th{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            @page{margin:1cm;size:A4 landscape}
        }
    </style>
</head>
<body>
<div class="header">
    <h2>📦 INVENTARIO — <?php echo $nombre_granja;?></h2>
    <div class="nav">
        <?php if($granja=='cafe'): ?>
        <a href="cafe_lotes.php">⬅ Café</a>
        <?php else: ?>
        <a href="seleccionar_galpon.php?granja=<?php echo $granja;?>">⬅ Galpones</a>
        <?php endif;?>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
</div>
<div class="info-badge">
    <?php echo $nombre_granja;?> &nbsp;|&nbsp; Artículos: <strong><?php echo count($registros);?></strong>
    &nbsp;|&nbsp; <span style="color:#888;font-size:12px">Ctrl+P para imprimir</span>
</div>
<div class="wrap">
<form action="guardar_inventario.php" method="POST">
    <input type="hidden" name="id_granja" value="<?php echo $granja;?>">
    <table id="tablaInventario">
        <thead>
            <tr>
                <th style="width:36px">#</th>
                <th style="width:105px">FECHA</th>
                <th>ARTÍCULO</th>
                <th style="width:110px">CANTIDAD</th>
                <th>DESCRIPCIÓN</th>
                <th style="width:120px">INGRESO ($)</th>
            </tr>
        </thead>
        <tbody id="cuerpo-inventario">
        <?php for($i=0;$i<$total_filas;$i++):
            $r=$registros[$i]??null; $tiene=$r!==null;?>
        <tr class="<?php echo $tiene?'tiene-datos':'';?>">
            <td><div class="num-cell"><?php echo $i+1;?></div></td>
            <td><input type="date" name="fecha[]" class="inp" value="<?php echo $tiene?$r['fecha']:date('Y-m-d');?>"></td>
            <td><input type="text" name="articulo[]" class="inp izq" value="<?php echo $tiene?htmlspecialchars($r['articulo']):'';?>" placeholder="Nombre del artículo..."></td>
            <td><input type="number" name="cantidad[]" class="inp" step="0.01" min="0" value="<?php echo $tiene?$r['cantidad']:'0';?>"></td>
            <td><input type="text" name="descripcion[]" class="inp izq" value="<?php echo $tiene?htmlspecialchars($r['descripcion']):'';?>" placeholder="Descripción..."></td>
            <td><input type="number" name="ingreso[]" class="inp" step="0.01" min="0" value="<?php echo $tiene?$r['ingreso']:'0';?>"></td>
        </tr>
        <?php endfor;?>
        </tbody>
    </table>
    <button type="button" class="btn-agregar" onclick="agregarFilaInv()">➕ Agregar artículo</button>
    <div class="footer-form">
        <p>✅ Verde = ya guardado &nbsp;|&nbsp; Ctrl+P imprime solo la tabla</p>
        <button type="submit" class="btn-save">💾 GUARDAR INVENTARIO</button>
    </div>
</form>
</div>
<script>
var contInv=<?php echo $total_filas;?>;
function agregarFilaInv(){
    var tbody=document.getElementById('cuerpo-inventario');
    var i=contInv;
    var tr=document.createElement('tr');
    tr.innerHTML=
        '<td><div class="num-cell">'+(i+1)+'</div></td>'+
        '<td><input type="date" name="fecha[]" class="inp" value="<?php echo date('Y-m-d');?>"></td>'+
        '<td><input type="text" name="articulo[]" class="inp izq" placeholder="Nombre del artículo..."></td>'+
        '<td><input type="number" name="cantidad[]" class="inp" step="0.01" min="0" value="0"></td>'+
        '<td><input type="text" name="descripcion[]" class="inp izq" placeholder="Descripción..."></td>'+
        '<td><input type="number" name="ingreso[]" class="inp" step="0.01" min="0" value="0"></td>';
    tbody.appendChild(tr);
    contInv++;
    tr.querySelector('input[name="articulo[]"]').focus();
}
</script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';

$lote = isset($_GET['lote']) ? intval($_GET['lote']) : 1;
$mes  = date('m'); $anio = date('Y');

$registros = [];
$sql = "SELECT * FROM cafe_labores WHERE num_lote='$lote'
        AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio' ORDER BY fecha ASC";
$res = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($res)) $registros[] = $row;
$total_filas = max(20, count($registros) + 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Café — Lote <?php echo $lote;?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter','Segoe UI',sans-serif;background:#f4f0f2}

        .nav-top{background:#6d0f2a;color:#fff;padding:12px 20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 8px rgba(0,0,0,.25)}
        .nav-top .izq{display:flex;gap:8px;align-items:center}
        .nav-top a{color:#fff;text-decoration:none;font-weight:600;font-size:13px;padding:6px 13px;border-radius:6px;border:1px solid rgba(255,255,255,.4);transition:background .2s}
        .nav-top a:hover{background:rgba(255,255,255,.2)}
        .nav-top .titulo{font-weight:700;font-size:15px}

        .mes-badge{background:#fff;color:#6d0f2a;font-weight:700;font-size:13px;text-align:center;padding:9px;border-bottom:2px solid #6d0f2a}

        .table-wrap{padding:16px;overflow-x:auto}

        table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.1);min-width:800px}
        th{background:#6d0f2a;color:#fff;padding:10px 6px;border:1px solid rgba(255,255,255,.2);font-size:11px;font-weight:700;text-align:center}
        td{border:1px solid #e0d0d5;padding:0}
        tr.tiene-datos{background:#fdf5f7}
        tr.tiene-datos td{border-color:#c9a0ac}

        .inp{width:100%;border:none;padding:10px 6px;background:#fdf0f3;text-align:center;outline:none;font-size:12px;font-family:inherit;color:#222;transition:background .15s}
        .inp:focus{background:#fff;border-bottom:2px solid #6d0f2a}
        .inp[readonly]{background:#f0dde3;font-weight:700;color:#4a0a1d;cursor:default}
        .inp.izq{text-align:left;padding-left:8px}
        .num-cell{text-align:center;padding:8px 4px;font-weight:700;color:#555;font-size:11px;background:#f9f0f2}

        .btn-agregar{display:flex;align-items:center;gap:8px;background:#fdf0f3;color:#6d0f2a;border:2px dashed #6d0f2a;padding:11px 22px;border-radius:7px;cursor:pointer;font-weight:700;font-size:14px;font-family:inherit;transition:background .2s,transform .15s;margin-top:14px}
        .btn-agregar:hover{background:#f0dde3;transform:scale(1.02)}

        .footer-form{margin:14px;background:#fff;padding:18px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
        .footer-form p{color:#666;font-size:12px}
        .btn-save{background:#6d0f2a;color:#fff;padding:13px 34px;border:none;border-radius:7px;cursor:pointer;font-weight:700;font-size:15px;transition:filter .2s,transform .2s}
        .btn-save:hover{filter:brightness(.88);transform:scale(1.02)}

        /* IMPRESIÓN */
        @media print{
            .nav-top,.mes-badge,.btn-agregar,.footer-form{display:none!important}
            body{background:#fff}
            .table-wrap{padding:0}
            table{box-shadow:none;border-radius:0;min-width:100%}
            th{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            tr.tiene-datos td{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            @page{margin:1cm;size:A4 landscape}
        }
    </style>
</head>
<body>
<div class="nav-top">
    <div class="izq">
        <a href="cafe_lotes.php">⬅ Lotes</a>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
    <div class="titulo">☕ CAFÉ — LOTE <?php echo $lote;?></div>
</div>
<div class="mes-badge">
    📅 Mes: <?php echo date('m/Y');?> &nbsp;|&nbsp; Lote <?php echo $lote;?> &nbsp;|&nbsp;
    Registros: <strong><?php echo count($registros);?></strong> &nbsp;|&nbsp;
    <span style="color:#888;font-size:12px">Ctrl+P para imprimir solo la tabla</span>
</div>

<div class="table-wrap">
<form action="guardar_cafe.php" method="POST">
    <input type="hidden" name="num_lote" value="<?php echo $lote;?>">
    <table id="tablaCafe">
        <thead>
            <tr>
                <th style="width:34px">#</th>
                <th style="width:105px">FECHA</th>
                <th>LABOR REALIZADA</th>
                <th style="width:130px">INSUMO</th>
                <th style="width:120px">VALOR INSUMO ($)</th>
                <th style="width:120px">VALOR MANO OBRA ($)</th>
                <th style="width:110px">TOTAL ($)</th>
                <th>OBSERVACIONES</th>
            </tr>
        </thead>
        <tbody id="cuerpo-cafe">
        <?php for($i=0;$i<$total_filas;$i++):
            $r=$registros[$i]??null; $tiene=$r!==null;?>
        <tr class="<?php echo $tiene?'tiene-datos':'';?>">
            <td><div class="num-cell"><?php echo $i+1;?></div></td>
            <td><input type="date" name="fecha[]" class="inp" value="<?php echo $tiene?$r['fecha']:date('Y-m-d');?>"></td>
            <td><input type="text" name="labor_realizada[]" class="inp izq" value="<?php echo $tiene?htmlspecialchars($r['labor_realizada']):'';?>" placeholder="Ej: Fertilización..."></td>
            <td><input type="text" name="insumo[]" class="inp izq" value="<?php echo $tiene?htmlspecialchars($r['insumo']):'';?>" placeholder="Nombre insumo..."></td>
            <td><input type="number" name="valor_insumo[]" class="inp" id="vi_<?php echo $i;?>" step="0.01" min="0" oninput="calcCafe(<?php echo $i;?>)" value="<?php echo $tiene?$r['valor_insumo']:'0';?>"></td>
            <td><input type="number" name="valor_mano_obra[]" class="inp" id="vm_<?php echo $i;?>" step="0.01" min="0" oninput="calcCafe(<?php echo $i;?>)" value="<?php echo $tiene?$r['valor_mano_obra']:'0';?>"></td>
            <td><input type="number" name="total[]" class="inp" id="tc_<?php echo $i;?>" readonly value="<?php echo $tiene?$r['total']:'0';?>"></td>
            <td><input type="text" name="observaciones[]" class="inp izq" value="<?php echo $tiene?htmlspecialchars($r['observaciones']):'';?>" placeholder="Observaciones..."></td>
        </tr>
        <?php endfor;?>
        </tbody>
    </table>

    <button type="button" class="btn-agregar" onclick="agregarFilaCafe()">➕ Agregar fila</button>

    <div class="footer-form">
        <p>✅ Filas con fondo rosado = ya guardadas &nbsp;|&nbsp; Total se calcula automáticamente</p>
        <button type="submit" class="btn-save">💾 GUARDAR REGISTRO</button>
    </div>
</form>
</div>

<script>
var contCafe = <?php echo $total_filas;?>;
function calcCafe(i){
    var vi=parseFloat(document.getElementById('vi_'+i).value)||0;
    var vm=parseFloat(document.getElementById('vm_'+i).value)||0;
    document.getElementById('tc_'+i).value=(vi+vm).toFixed(0);
}
function agregarFilaCafe(){
    var tbody=document.getElementById('cuerpo-cafe');
    var i=contCafe;
    var tr=document.createElement('tr');
    tr.innerHTML=
        '<td><div class="num-cell">'+(i+1)+'</div></td>'+
        '<td><input type="date" name="fecha[]" class="inp" value="<?php echo date('Y-m-d');?>"></td>'+
        '<td><input type="text" name="labor_realizada[]" class="inp izq" placeholder="Ej: Fertilización..."></td>'+
        '<td><input type="text" name="insumo[]" class="inp izq" placeholder="Nombre insumo..."></td>'+
        '<td><input type="number" name="valor_insumo[]" class="inp" id="vi_'+i+'" step="0.01" min="0" value="0" oninput="calcCafe('+i+')"></td>'+
        '<td><input type="number" name="valor_mano_obra[]" class="inp" id="vm_'+i+'" step="0.01" min="0" value="0" oninput="calcCafe('+i+')"></td>'+
        '<td><input type="number" name="total[]" class="inp" id="tc_'+i+'" readonly value="0"></td>'+
        '<td><input type="text" name="observaciones[]" class="inp izq" placeholder="Observaciones..."></td>';
    tbody.appendChild(tr);
    contCafe++;
    tr.querySelector('input[name="labor_realizada[]"]').focus();
}
document.addEventListener('DOMContentLoaded',function(){
    for(var i=0;i<<?php echo count($registros);?>;i++) calcCafe(i);
});
</script>
</body>
</html>

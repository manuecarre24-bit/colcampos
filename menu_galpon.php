<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
$granja = isset($_GET['granja']) ? $_GET['granja'] : 'ponderosa';
$galpon = isset($_GET['galpon']) ? intval($_GET['galpon']) : 1;
$nombre_granja = $granja == 'lupe' ? 'La Lupe' : 'La Ponderosa';
$color    = $granja == 'lupe' ? '#d35400' : '#064e22';
$color_grad = $granja == 'lupe' ? 'linear-gradient(135deg,#d35400,#a03800)' : 'linear-gradient(135deg,#064e22,#033015)';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galpón <?php echo $galpon;?> — <?php echo $nombre_granja;?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter','Segoe UI',sans-serif;background:#f0f4f0;min-height:100vh}
        .top-bar{background:<?php echo $color;?>;color:#fff;padding:14px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 8px rgba(0,0,0,.25)}
        .top-bar h1{font-size:18px;font-weight:700}
        .top-bar .nav{display:flex;gap:8px}
        .top-bar a{color:#fff;text-decoration:none;font-weight:600;font-size:13px;padding:7px 14px;border-radius:6px;border:1px solid rgba(255,255,255,.4);transition:background .2s}
        .top-bar a:hover{background:rgba(255,255,255,.2)}
        .hero{background:<?php echo $color_grad;?>;color:#fff;text-align:center;padding:40px 20px 32px}
        .hero .badge{display:inline-block;background:rgba(255,255,255,.18);color:#fff;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:5px 14px;border-radius:20px;margin-bottom:14px}
        .hero h2{font-size:32px;font-weight:800;margin-bottom:6px}
        .hero p{font-size:14px;opacity:.85}
        .opciones{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:900px;margin:36px auto;padding:0 24px}
        .opcion-card{background:#fff;border-radius:14px;padding:30px 20px;text-align:center;text-decoration:none;color:#222;box-shadow:0 4px 14px rgba(0,0,0,.10);border:2px solid #eee;transition:transform .22s,border-color .22s,box-shadow .22s;display:flex;flex-direction:column;align-items:center;gap:10px}
        .opcion-card:hover{transform:translateY(-6px);border-color:<?php echo $color;?>;box-shadow:0 12px 28px rgba(0,0,0,.15)}
        .opcion-card .card-ico{font-size:40px;line-height:1}
        .opcion-card h3{font-size:16px;font-weight:700;color:<?php echo $color;?>;margin:0}
        .opcion-card p{font-size:12px;color:#666;line-height:1.5;margin:0}
        .opcion-card .tag-go{margin-top:6px;background:<?php echo $color;?>;color:#fff;font-size:12px;font-weight:700;padding:6px 18px;border-radius:20px;transition:filter .2s}
        .opcion-card:hover .tag-go{filter:brightness(.88)}
    </style>
</head>
<body>
<div class="top-bar">
    <h1>📍 <?php echo strtoupper($nombre_granja);?> — GALPÓN <?php echo $galpon;?></h1>
    <div class="nav">
        <a href="seleccionar_galpon.php?granja=<?php echo $granja;?>">⬅ Galpones</a>
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
</div>
<div class="hero">
    <div class="badge"><?php echo strtoupper($nombre_granja);?></div>
    <h2>Galpón <?php echo $galpon;?></h2>
    <p>¿Qué deseas registrar hoy?</p>
</div>
<div class="opciones">
    <a href="gestion_granja.php?granja=<?php echo $granja;?>&galpon=<?php echo $galpon;?>" class="opcion-card">
        <span class="card-ico">🥚</span>
        <h3>Producción</h3>
        <p>Registro diario de huevos por categoría, cartones y unidades, mortalidad y saldo.</p>
        <span class="tag-go">Ir a producción →</span>
    </a>
    <a href="alimentacion.php?granja=<?php echo $granja;?>&galpon=<?php echo $galpon;?>" class="opcion-card">
        <span class="card-ico">🌾</span>
        <h3>Alimentación</h3>
        <p>Control de cantidad de alimento, gramos por ave y valor del consumo.</p>
        <span class="tag-go">Ir a alimentación →</span>
    </a>
    <a href="costos_generales.php?granja=<?php echo $granja;?>&galpon=<?php echo $galpon;?>" class="opcion-card">
        <span class="card-ico">💰</span>
        <h3>Costos Generales</h3>
        <p>Registro de gastos, insumos, servicios y observaciones del galpón.</p>
        <span class="tag-go">Ir a costos →</span>
    </a>
</div>
</body>
</html>

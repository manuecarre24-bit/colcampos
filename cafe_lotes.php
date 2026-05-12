<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Café — COLCAMPOS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter','Segoe UI',sans-serif;background:url('img_fondo.jpeg') no-repeat center center fixed;background-size:cover;min-height:100vh;display:flex}

        .sidebar{width:230px;min-height:100vh;background:rgba(40,5,15,0.88);backdrop-filter:blur(14px);display:flex;flex-direction:column;padding:28px 16px;gap:10px;box-shadow:3px 0 18px rgba(0,0,0,0.5);flex-shrink:0}
        .sidebar-title{color:#fff;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;text-align:center;padding-bottom:14px;border-bottom:1px solid rgba(255,255,255,0.15);margin-bottom:4px}
        .btn-side{display:block;background:#6d0f2a;color:#fff;padding:12px 14px;text-decoration:none;text-align:center;font-weight:600;font-size:13px;border-radius:8px;border:1px solid rgba(255,255,255,0.15);transition:background .2s,transform .15s}
        .btn-side:hover{background:#4a0a1d;transform:translateX(3px)}
        .sidebar-spacer{flex:1}
        .btn-inicio{display:flex;align-items:center;gap:8px;color:#ccc;text-decoration:none;font-size:13px;font-weight:600;padding:10px 12px;border-radius:7px;transition:background .2s,color .2s}
        .btn-inicio:hover{background:rgba(255,255,255,.12);color:#fff}

        .main{flex:1;display:flex;flex-direction:column;align-items:center;padding:50px 24px 40px}
        .main-title{color:#fff;font-size:32px;font-weight:800;text-shadow:0 2px 10px rgba(0,0,0,.75);margin-bottom:6px}
        .main-sub{color:rgba(255,255,255,.88);font-size:14px;text-shadow:0 1px 5px rgba(0,0,0,.6);margin-bottom:38px}

        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:680px;width:100%}
        .card-lote{display:block;text-decoration:none;background:rgba(255,255,255,0.97);border-radius:14px;padding:34px 16px 26px;text-align:center;border-bottom:5px solid #6d0f2a;box-shadow:0 8px 20px rgba(0,0,0,.22);transition:transform .22s,box-shadow .22s}
        .card-lote:hover{transform:translateY(-7px);box-shadow:0 16px 32px rgba(0,0,0,.32)}
        .card-lote .ico{font-size:28px;display:block;margin-bottom:10px}
        .card-lote .lbl{font-weight:700;font-size:15px;color:#6d0f2a}
        .card-lote .sub{font-size:11px;color:#888;margin-top:4px}
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-title">☕ CAFÉ</div>
    <a href="inventario.php?granja=cafe" class="btn-side">📦 Inventario</a>
    <div class="sidebar-spacer"></div>
    <a href="dashboard.php" class="btn-inicio">⬅ Volver al Inicio</a>
</aside>
<main class="main">
    <h1 class="main-title">☕ CAFÉ</h1>
    <p class="main-sub">Selecciona un lote para registrar las labores</p>
    <div class="grid">
        <?php for($i=1;$i<=3;$i++): ?>
        <a class="card-lote" href="cafe_tabla.php?lote=<?php echo $i;?>">
            <span class="ico">🌱</span>
            <div class="lbl">LOTE <?php echo $i;?></div>
            <div class="sub">Ver labores →</div>
        </a>
        <?php endfor;?>
    </div>
</main>
</body>
</html>

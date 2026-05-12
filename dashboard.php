<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - COLCAMPOS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter','Segoe UI',sans-serif;background:url('img_fondo.jpeg') no-repeat center center fixed;background-size:cover;min-height:100vh}

        .overlay{background:rgba(0,0,0,0.55);min-height:100vh;display:flex;flex-direction:column}

        /* Header */
        .header{background:rgba(0,0,0,0.7);backdrop-filter:blur(10px);padding:14px 28px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(255,255,255,0.1)}
        .brand{display:flex;align-items:center;gap:12px}
        .brand img{width:42px;height:42px;border-radius:6px;object-fit:cover}
        .brand span{color:#fff;font-size:20px;font-weight:800;letter-spacing:1px}
        .user-info{display:flex;align-items:center;gap:12px;color:rgba(255,255,255,0.85);font-size:13px}
        .user-info strong{color:#fff}
        .btn-salir{background:#c62828;color:#fff;text-decoration:none;padding:7px 16px;border-radius:6px;font-size:13px;font-weight:600;transition:background .2s}
        .btn-salir:hover{background:#b71c1c}

        /* Contenido */
        .content{flex:1;display:flex;flex-direction:column;align-items:center;padding:50px 24px 40px}
        .page-title{color:#fff;font-size:28px;font-weight:800;text-shadow:0 2px 8px rgba(0,0,0,0.6);margin-bottom:8px;text-align:center}
        .page-sub{color:rgba(255,255,255,0.8);font-size:14px;margin-bottom:40px;text-align:center}

        /* Grid de granjas */
        .farm-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;max-width:980px;width:100%}

        .farm-card{text-decoration:none;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.35);transition:transform .25s,box-shadow .25s;display:flex;flex-direction:column}
        .farm-card:hover{transform:translateY(-8px);box-shadow:0 20px 40px rgba(0,0,0,0.45)}

        /* Ponderosa - verde */
        .farm-card.verde .card-top{background:linear-gradient(135deg,#064e22,#1a7a3a);padding:28px 24px 20px}
        .farm-card.verde .card-footer{background:#064e22}
        /* Lupe - naranja */
        .farm-card.naranja .card-top{background:linear-gradient(135deg,#d35400,#a03800);padding:28px 24px 20px}
        .farm-card.naranja .card-footer{background:#d35400}
        /* Café - vinotinto */
        .farm-card.vinotinto .card-top{background:linear-gradient(135deg,#6d0f2a,#4a0a1d);padding:28px 24px 20px}
        .farm-card.vinotinto .card-footer{background:#6d0f2a}

        .card-tag{display:inline-block;background:rgba(255,255,255,0.2);color:#fff;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:4px 12px;border-radius:20px;margin-bottom:12px}
        .card-top h2{color:#fff;font-size:22px;font-weight:800;margin-bottom:6px}
        .card-top p{color:rgba(255,255,255,0.8);font-size:13px;line-height:1.5}
        .card-footer{color:#fff;padding:13px 24px;font-weight:700;font-size:13px;display:flex;justify-content:space-between;align-items:center}
        .card-footer span{opacity:.85}

        /* Botón reportes */
        .report-section{margin-top:44px}
        .btn-reporte{display:inline-flex;align-items:center;gap:10px;background:rgba(255,255,255,0.12);color:#fff;text-decoration:none;padding:13px 32px;border-radius:50px;border:1px solid rgba(255,255,255,0.3);font-weight:700;font-size:14px;backdrop-filter:blur(10px);transition:.3s}
        .btn-reporte:hover{background:#fff;color:#333}

        @media(max-width:768px){.farm-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="overlay">
    <header class="header">
        <div class="brand">
            <img src="img.jpeg" alt="Logo">
            <span>COLCAMPOS</span>
        </div>
        <div class="user-info">
            <span>Usuario: <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></span>
            <a href="logout.php" class="btn-salir">Cerrar Sesión</a>
        </div>
    </header>

    <div class="content">
        <h1 class="page-title">Panel de Control</h1>
        <p class="page-sub">Selecciona una unidad productiva para gestionar</p>

        <div class="farm-grid">
            <!-- La Ponderosa -->
            <a href="seleccionar_galpon.php?granja=ponderosa" class="farm-card verde">
                <div class="card-top">
                    <span class="card-tag">Unidad 01</span>
                    <h2>🏡 La Ponderosa</h2>
                    <p>Gestión de producción, alimentación, costos e inventario. 6 galpones activos.</p>
                </div>
                <div class="card-footer">
                    <span>Granja Avícola</span>
                    <span>Gestionar →</span>
                </div>
            </a>

            <!-- La Lupe -->
            <a href="seleccionar_galpon.php?granja=lupe" class="farm-card naranja">
                <div class="card-top">
                    <span class="card-tag">Unidad 02</span>
                    <h2>🏡 La Lupe</h2>
                    <p>Gestión de producción, alimentación, costos e inventario. 3 galpones activos.</p>
                </div>
                <div class="card-footer">
                    <span>Granja Avícola</span>
                    <span>Gestionar →</span>
                </div>
            </a>

            <!-- Café -->
            <a href="cafe_lotes.php" class="farm-card vinotinto">
                <div class="card-top">
                    <span class="card-tag">Unidad 03</span>
                    <h2>☕ Café</h2>
                    <p>Registro de labores, insumos, mano de obra y costos por lote de producción.</p>
                </div>
                <div class="card-footer">
                    <span>Cultivo de Café</span>
                    <span>Gestionar →</span>
                </div>
            </a>
        </div>

        <div class="report-section">
            <a href="reportes_mensuales.php" class="btn-reporte">📊 REPORTES MENSUALES</a>
        </div>
    </div>
</div>
</body>
</html>

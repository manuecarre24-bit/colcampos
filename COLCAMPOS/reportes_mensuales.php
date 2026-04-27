<?php
session_start();
if (!isset($_SESSION['usuario'])) { header("location: index.php"); exit(); }
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Mensuales - COLCAMPOS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f0f4f8; min-height: 100vh; }

        .header {
            background: #263238;
            color: #ffffff;
            padding: 16px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .header h1 { font-size: 18px; font-weight: 700; }
        .header .nav-links { display: flex; gap: 8px; }
        .header a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 7px 14px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.35);
            transition: background 0.2s;
        }
        .header a:hover { background: rgba(255,255,255,0.15); }

        .content { max-width: 800px; margin: 40px auto; padding: 0 20px; }

        .card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        .card h2 { color: #263238; font-size: 18px; margin-bottom: 8px; }
        .card p  { color: #546e7a; font-size: 14px; margin-bottom: 20px; line-height: 1.5; }

        .btn-descarga {
            background: #c62828;
            color: #ffffff;
            padding: 13px 28px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            transition: filter 0.2s, transform 0.2s;
        }
        .btn-descarga:hover { filter: brightness(0.88); transform: scale(1.02); }

        .btn-limpiar {
            background: #37474f;
            color: #ffffff;
            padding: 13px 28px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            transition: filter 0.2s, transform 0.2s;
        }
        .btn-limpiar:hover { filter: brightness(0.85); }

        .warning-box {
            background: #fff3e0;
            border-left: 4px solid #e65100;
            padding: 14px 18px;
            border-radius: 6px;
            font-size: 13px;
            color: #bf360c;
            margin-bottom: 16px;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>📊 REPORTES MENSUALES</h1>
    <div class="nav-links">
        <a href="dashboard.php">🏠 Inicio</a>
    </div>
</div>

<div class="content">

    <div class="card">
        <h2>📂 Descargar Reporte Completo</h2>
        <p>Genera y descarga un archivo con el resumen mensual de producción y alimentación de todas las granjas y galpones.</p>
        <form method="POST" action="reportes_mensuales.php">
            <button type="submit" name="descargar_todo" class="btn-descarga">📥 DESCARGAR REPORTE (.ZIP)</button>
        </form>
    </div>

    <div class="card">
        <h2>🗑️ Cerrar Mes y Reiniciar</h2>
        <div class="warning-box">
            ⚠️ <strong>Advertencia:</strong> Esta acción borrará todos los registros de producción y alimentación del mes actual. El almacén y los pagos NO se borran. Asegúrese de haber descargado los reportes antes de continuar.
        </div>
        <form method="POST" action="limpiar_mes.php"
              onsubmit="return confirm('¿SEGURO? Esto borrará toda la producción y alimentación registrada. ¿Ya descargaste los reportes?');">
            <button type="submit" name="confirmar_limpieza" class="btn-limpiar">🔄 CERRAR MES Y REINICIAR TABLAS</button>
        </form>
    </div>

</div>

</body>
</html>

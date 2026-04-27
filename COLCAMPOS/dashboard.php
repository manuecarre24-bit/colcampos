<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - COLCAMPOS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=2.0">
</head>
<body class="dashboard-body">

<div class="overlay-luz-blanca">
    <div class="dashboard-container">

        <header class="dash-header">
            <div class="brand">
                <img src="img.jpeg" alt="Logo" class="mini-logo">
                <span>COLCAMPOS</span>
            </div>
            <div class="user-info">
                <span>Usuario: <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></span>
                <a href="logout.php" class="btn-salir">Cerrar Sesión</a>
            </div>
        </header>

        <div class="farm-grid">
            <a href="seleccionar_galpon.php?granja=ponderosa" class="farm-card verde">
                <div class="farm-header">
                    <span class="tag">Unidad 01</span>
                    <h2>La Ponderosa</h2>
                    <p>Gestión de producción, alimentación y registros diarios.</p>
                </div>
                <div class="farm-footer">Gestionar Granja →</div>
            </a>

            <a href="seleccionar_galpon.php?granja=lupe" class="farm-card naranja">
                <div class="farm-header">
                    <span class="tag">Unidad 02</span>
                    <h2>La Lupe</h2>
                    <p>Gestión de producción, alimentación y registros diarios.</p>
                </div>
                <div class="farm-footer">Gestionar Granja →</div>
            </a>
        </div>

        <div class="report-section">
            <a href="reportes_mensuales.php" class="btn-reporte-footer">
                <span>📊</span> GENERAR REPORTES MENSUALES
            </a>
        </div>

    </div>
</div>

</body>
</html>

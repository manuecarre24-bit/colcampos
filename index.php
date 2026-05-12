<?php
include("conexion.php");
session_start();

if (isset($_SESSION['usuario'])) {
    header("location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $pass = mysqli_real_escape_string($conexion, $_POST['password']);

    $query = "SELECT * FROM usuarios WHERE usuario = '$user' AND password = '$pass'";
    $resultado = mysqli_query($conexion, $query);

    if (mysqli_num_rows($resultado) > 0) {
        $_SESSION['usuario'] = $user;
        header("location: dashboard.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COLCAMPOS - Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-container">

    <div class="sidebar">
        <div class="brand-header">
            <img src="img.jpeg" alt="Logo Colcampos" class="logo-colcampos">
            <div class="brand-text">
                <h1>COLCAMPOS</h1>
                <p>Gestión Avícola</p>
            </div>
        </div>
        <h2 class="main-headline">Control total de tus granjas ponedoras</h2>
        <p class="sub-headline">Registro diario de producción, mortalidad, consumo y viabilidad en un solo lugar.</p>
        <div class="badges-container">
            <span class="badge active">2 granjas activas</span>
            <span class="badge">La Ponderosa &amp; La Lupe</span>
        </div>
    </div>

    <div class="login-content">
        <div class="form-box">
            <h2>Bienvenido de nuevo</h2>
            <p class="form-instructions">Ingresa tus credenciales para continuar</p>

            <?php if (isset($error)): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" id="formLogin">
                <div class="input-group">
                    <label for="usuario">USUARIO</label>
                    <input type="text" id="usuario" name="usuario" placeholder="colcampos" required>
                </div>
                <div class="input-group">
                    <label for="password">CONTRASEÑA</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-ingresar">Ingresar al sistema</button>
            </form>

            <div class="form-footer">
                <p class="version-text">COLcampos v2.0 &mdash; <?php echo date('Y'); ?></p>
            </div>
        </div>
    </div>
</div>

<script src="js/main.js"></script>
</body>
</html>

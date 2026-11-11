<?php
require_once 'php/auth.php';
// Si ya inició sesión, va directo al panel
if (!empty($_SESSION['usuario_id'])) {
    header('Location: panel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - Clínica Dental</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="login-fondo">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4 login-caja">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="text-center mb-1"><i class="fa fa-heartbeat text-teal"></i> Clínica Dental</h4>
                    <p class="text-center text-muted mb-4">Inicie sesión para continuar</p>
                    <div id="alertas"></div>
                    <form id="form-login">
                        <div class="form-group">
                            <label for="correo">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" maxlength="100" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="clave">Contraseña</label>
                            <input type="password" class="form-control" id="clave" name="clave" required>
                        </div>
                        <button type="submit" class="btn btn-teal btn-block"><i class="fa fa-sign-in"></i> Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/login.js"></script>
</body>
</html>

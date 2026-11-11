<?php
// Cabecera y menú comunes. Variables esperadas: $titulo (texto) y $activa (panel|pacientes|procedimientos)
$nombre_usuario = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : '';
function activo($a, $b) { return $a === $b ? ' active' : ''; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titulo) ?> - Clínica Dental</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark navbar-clinica">
    <a class="navbar-brand" href="panel.php"><i class="fa fa-heartbeat"></i> Clínica Dental</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu" aria-label="Menú">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item<?= activo($activa, 'panel') ?>"><a class="nav-link" href="panel.php"><i class="fa fa-home"></i> Panel</a></li>
            <li class="nav-item<?= activo($activa, 'pacientes') ?>"><a class="nav-link" href="pacientes.php"><i class="fa fa-users"></i> Pacientes</a></li>
            <li class="nav-item<?= activo($activa, 'procedimientos') ?>"><a class="nav-link" href="procedimientos.php"><i class="fa fa-medkit"></i> Procedimientos</a></li>
        </ul>
        <span class="navbar-text mr-3"><i class="fa fa-user-circle"></i> <?= htmlspecialchars($nombre_usuario) ?></span>
        <a class="btn btn-sm btn-outline-light" href="php/logout.php"><i class="fa fa-sign-out"></i> Salir</a>
    </div>
</nav>
<div class="container mt-4 mb-5">
    <!-- Aquí se muestran los mensajes de éxito o error -->
    <div id="alertas"></div>

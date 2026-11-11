<?php
/** Inicio de sesión: valida correo y contraseña contra la tabla usuarios. */
include_once(__DIR__ . '/auth.php');
header('Content-Type: application/json; charset=utf-8');
include_once(__DIR__ . '/conexion.php');
solo_post();

$correo = texto('correo');
$clave  = (isset($_POST['clave']) && is_string($_POST['clave'])) ? $_POST['clave'] : '';

if ($correo === '' || $clave === '') {
    responder('error', 'Ingrese su correo y su contraseña.');
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responder('error', 'El formato del correo no es válido.');
}

$stmt = mysqli_prepare($conexion, "SELECT id, nombre, clave FROM usuarios WHERE correo = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
$u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// password_verify compara contra el hash guardado
if ($u && password_verify($clave, $u['clave'])) {
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int)$u['id'];
    $_SESSION['usuario_nombre'] = $u['nombre'];
    responder('exito', 'Bienvenido.');
}
responder('error', 'Correo o contraseña incorrectos.');

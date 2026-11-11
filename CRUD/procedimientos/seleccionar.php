<?php
/** SELECCIONAR un procedimiento por id. Método GET. Parámetro: id. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    responder('error', 'El ID proporcionado no es válido.');
}

$stmt = mysqli_prepare($conexion, "SELECT id, nombre, descripcion, precio, activo FROM procedimientos WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = filas($stmt);
if (count($resultado) === 0) {
    responder('error', 'No se encontró el procedimiento.');
}
responder('exito', 'OK', array('datos' => $resultado[0]));

<?php
/** SELECCIONAR un paciente por id (para llenar el formulario de edición). Método GET. Parámetro: id. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    responder('error', 'El ID proporcionado no es válido.');
}

$stmt = mysqli_prepare($conexion, "SELECT id, cedula, nombre, apellido, fecha_nacimiento, sexo, telefono, correo, direccion, antecedentes FROM pacientes WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = filas($stmt);
if (count($resultado) === 0) {
    responder('error', 'No se encontró el paciente.');
}
responder('exito', 'OK', array('datos' => $resultado[0]));

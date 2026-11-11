<?php
/** EDITAR un procedimiento. Método POST. Campos: id + los de validar.php. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');
include_once(__DIR__ . '/validar.php');
solo_post();

$id = (int)texto('id');
if ($id <= 0) {
    responder('error', 'El ID proporcionado no es válido.');
}
if (!existe($conexion, 'procedimientos', $id)) {
    responder('error', 'No se encontró el procedimiento.');
}

$d = datos_procedimiento();

$stmt = mysqli_prepare($conexion, "UPDATE procedimientos SET nombre = ?, descripcion = ?, precio = ?, activo = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ssdii", $d['nombre'], $d['descripcion'], $d['precio'], $d['activo'], $id);
if (!mysqli_stmt_execute($stmt)) {
    if (mysqli_errno($conexion) == 1062) {
        responder('error', 'Ya existe otro procedimiento con ese nombre.');
    }
    responder('error', 'No se pudo actualizar el procedimiento.');
}
responder('exito', 'Procedimiento actualizado correctamente.');

<?php
/** ELIMINAR un registro de la historia clínica. Método POST. Campo: id. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');
solo_post();

$id = (int)texto('id');
if ($id <= 0) {
    responder('error', 'El ID proporcionado no es válido.');
}

$stmt = mysqli_prepare($conexion, "DELETE FROM atenciones WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (!mysqli_stmt_execute($stmt)) {
    responder('error', 'No se pudo eliminar el registro.');
}
if (mysqli_stmt_affected_rows($stmt) > 0) {
    responder('exito', 'Registro eliminado de la historia clínica.');
}
responder('error', 'No se encontró ningún registro con ese ID.');

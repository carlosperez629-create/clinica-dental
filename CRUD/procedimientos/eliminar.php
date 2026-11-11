<?php
/** ELIMINAR un procedimiento. Método POST. Campo: id. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');
solo_post();

$id = (int)texto('id');
if ($id <= 0) {
    responder('error', 'El ID proporcionado no es válido.');
}

$stmt = mysqli_prepare($conexion, "DELETE FROM procedimientos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (!mysqli_stmt_execute($stmt)) {
    // 1451 = tiene registros relacionados en atenciones (clave foránea)
    if (mysqli_errno($conexion) == 1451) {
        responder('error', 'Este procedimiento ya se usó en historias clínicas y no se puede eliminar. Márquelo como Inactivo.');
    }
    responder('error', 'No se pudo eliminar el procedimiento.');
}
if (mysqli_stmt_affected_rows($stmt) > 0) {
    responder('exito', 'Procedimiento eliminado correctamente.');
}
responder('error', 'No se encontró ningún procedimiento con ese ID.');

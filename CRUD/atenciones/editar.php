<?php
/** EDITAR un registro de la historia clínica. Método POST. Campos: id + los de validar.php. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');
include_once(__DIR__ . '/validar.php');
solo_post();

$id = (int)texto('id');
if ($id <= 0) {
    responder('error', 'El ID proporcionado no es válido.');
}
if (!existe($conexion, 'atenciones', $id)) {
    responder('error', 'No se encontró el registro.');
}

$d = datos_atencion($conexion);

$stmt = mysqli_prepare($conexion, "UPDATE atenciones SET procedimiento_id = ?, fecha = ?, pieza_dental = ?, diagnostico = ?, observaciones = ?, costo = ? WHERE id = ? AND paciente_id = ?");
mysqli_stmt_bind_param($stmt, "issssdii", $d['procedimiento_id'], $d['fecha'], $d['pieza_dental'], $d['diagnostico'], $d['observaciones'], $d['costo'], $id, $d['paciente_id']);
if (!mysqli_stmt_execute($stmt)) {
    responder('error', 'No se pudo actualizar el registro.');
}
responder('exito', 'Registro actualizado correctamente.');

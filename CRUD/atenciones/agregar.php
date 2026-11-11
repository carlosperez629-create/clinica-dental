<?php
/** AGREGAR un procedimiento a la historia clínica de un paciente. Método POST. Campos: ver validar.php. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');
include_once(__DIR__ . '/validar.php');
solo_post();

$d = datos_atencion($conexion);

$stmt = mysqli_prepare($conexion, "INSERT INTO atenciones (paciente_id, procedimiento_id, fecha, pieza_dental, diagnostico, observaciones, costo) VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iissssd", $d['paciente_id'], $d['procedimiento_id'], $d['fecha'], $d['pieza_dental'], $d['diagnostico'], $d['observaciones'], $d['costo']);
if (!mysqli_stmt_execute($stmt)) {
    responder('error', 'No se pudo guardar el registro en la historia clínica.');
}
responder('exito', 'Procedimiento agregado a la historia clínica.', array('id' => mysqli_insert_id($conexion)));

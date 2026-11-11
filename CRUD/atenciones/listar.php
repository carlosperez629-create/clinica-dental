<?php
/** LISTAR los procedimientos de la historia clínica de un paciente. Método GET. Parámetro: paciente_id. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');

$paciente_id = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : 0;
if ($paciente_id <= 0) {
    responder('error', 'El paciente no es válido.');
}

$stmt = mysqli_prepare($conexion, "SELECT a.id, a.fecha, a.pieza_dental, a.diagnostico, a.observaciones, a.costo, a.procedimiento_id, p.nombre AS procedimiento
    FROM atenciones a
    INNER JOIN procedimientos p ON p.id = a.procedimiento_id
    WHERE a.paciente_id = ?
    ORDER BY a.fecha DESC, a.id DESC");
mysqli_stmt_bind_param($stmt, "i", $paciente_id);
mysqli_stmt_execute($stmt);
responder('exito', 'OK', array('datos' => filas($stmt)));

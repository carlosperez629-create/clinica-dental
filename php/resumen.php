<?php
/** Datos del panel principal: totales y últimos procedimientos registrados. */
include_once(__DIR__ . '/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/conexion.php');

function contar($conexion, $sql) {
    $r = mysqli_query($conexion, $sql);
    $f = mysqli_fetch_row($r);
    return (int)$f[0];
}

$ultimas = array();
$r = mysqli_query($conexion, "SELECT a.fecha, pa.id AS paciente_id, pa.nombre, pa.apellido, p.nombre AS procedimiento
    FROM atenciones a
    INNER JOIN pacientes pa ON pa.id = a.paciente_id
    INNER JOIN procedimientos p ON p.id = a.procedimiento_id
    ORDER BY a.fecha DESC, a.id DESC LIMIT 5");
while ($f = mysqli_fetch_assoc($r)) {
    $ultimas[] = $f;
}

responder('exito', 'OK', array(
    'pacientes'      => contar($conexion, "SELECT COUNT(*) FROM pacientes"),
    'procedimientos' => contar($conexion, "SELECT COUNT(*) FROM procedimientos WHERE activo = 1"),
    'atenciones'     => contar($conexion, "SELECT COUNT(*) FROM atenciones"),
    'atenciones_mes' => contar($conexion, "SELECT COUNT(*) FROM atenciones WHERE YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())"),
    'ultimas'        => $ultimas
));

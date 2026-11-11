<?php
/**
 * Validaciones de un registro de la historia clínica. Lo usan agregar.php y editar.php.
 * Devuelve los datos limpios; si algo está mal, responde el error en JSON y termina.
 */
function datos_atencion($conexion) {
    $d = array(
        'paciente_id'      => (int)texto('paciente_id'),
        'procedimiento_id' => (int)texto('procedimiento_id'),
        'fecha'            => texto('fecha'),
        'pieza_dental'     => texto('pieza_dental'),
        'diagnostico'      => texto('diagnostico'),
        'observaciones'    => texto('observaciones'),
        'costo'            => texto('costo'),
    );
    if ($d['paciente_id'] <= 0 || $d['procedimiento_id'] <= 0 || $d['fecha'] === '') {
        responder('error', 'Complete los campos obligatorios: procedimiento y fecha.');
    }
    if (!existe($conexion, 'pacientes', $d['paciente_id'])) {
        responder('error', 'El paciente no existe.');
    }
    if (!existe($conexion, 'procedimientos', $d['procedimiento_id'])) {
        responder('error', 'El procedimiento seleccionado no existe.');
    }
    if (!fecha_valida($d['fecha'])) {
        responder('error', 'La fecha no es válida o es futura.');
    }
    if (mb_strlen($d['pieza_dental']) > 20) {
        responder('error', 'La pieza dental no puede superar los 20 caracteres.');
    }
    if (mb_strlen($d['diagnostico']) > 500) {
        responder('error', 'El diagnóstico no puede superar los 500 caracteres.');
    }
    if (mb_strlen($d['observaciones']) > 1000) {
        responder('error', 'Las observaciones no pueden superar los 1000 caracteres.');
    }
    // Si no se indica costo, se usa el precio del catálogo
    if ($d['costo'] === '') {
        $stmt = mysqli_prepare($conexion, "SELECT precio FROM procedimientos WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $d['procedimiento_id']);
        mysqli_stmt_execute($stmt);
        $f = filas($stmt);
        $d['costo'] = $f[0]['precio'];
    }
    $costo = monto_valido($d['costo']);
    if ($costo === false) {
        responder('error', 'El costo debe ser un número mayor o igual a 0.');
    }
    $d['costo'] = $costo;
    return $d;
}

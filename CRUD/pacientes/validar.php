<?php
/**
 * Validaciones del paciente. Lo usan agregar.php y editar.php.
 * Devuelve los datos limpios; si algo está mal, responde el error en JSON y termina.
 */
function datos_paciente() {
    $d = array(
        'cedula'           => texto('cedula'),
        'nombre'           => texto('nombre'),
        'apellido'         => texto('apellido'),
        'fecha_nacimiento' => texto('fecha_nacimiento'),
        'sexo'             => texto('sexo'),
        'telefono'         => texto('telefono'),
        'correo'           => texto('correo'),
        'direccion'        => texto('direccion'),
        'antecedentes'     => texto('antecedentes'),
    );
    if ($d['cedula'] === '' || $d['nombre'] === '' || $d['apellido'] === '' || $d['fecha_nacimiento'] === '' || $d['sexo'] === '') {
        responder('error', 'Complete los campos obligatorios: cédula, nombre, apellido, fecha de nacimiento y sexo.');
    }
    if (!preg_match('/^[0-9]{5,12}$/', $d['cedula'])) {
        responder('error', 'La cédula debe tener entre 5 y 12 números.');
    }
    if (!preg_match('/^[\p{L} \'-]{2,50}$/u', $d['nombre']) || !preg_match('/^[\p{L} \'-]{2,50}$/u', $d['apellido'])) {
        responder('error', 'Nombre y apellido solo pueden tener letras (entre 2 y 50 caracteres).');
    }
    if (!fecha_valida($d['fecha_nacimiento'])) {
        responder('error', 'La fecha de nacimiento no es válida o es futura.');
    }
    if (!in_array($d['sexo'], array('M', 'F'), true)) {
        responder('error', 'Seleccione un sexo válido.');
    }
    if ($d['telefono'] !== '' && !preg_match('/^[0-9+\- ]{7,20}$/', $d['telefono'])) {
        responder('error', 'El teléfono solo puede tener números, +, - y espacios (7 a 20 caracteres).');
    }
    if ($d['correo'] !== '' && (!filter_var($d['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['correo']) > 100)) {
        responder('error', 'El formato del correo no es válido.');
    }
    if (mb_strlen($d['direccion']) > 200) {
        responder('error', 'La dirección no puede superar los 200 caracteres.');
    }
    if (mb_strlen($d['antecedentes']) > 1000) {
        responder('error', 'Los antecedentes no pueden superar los 1000 caracteres.');
    }
    return $d;
}

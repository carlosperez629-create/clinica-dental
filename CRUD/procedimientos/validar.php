<?php
/**
 * Validaciones del procedimiento. Lo usan agregar.php y editar.php.
 * Devuelve los datos limpios; si algo está mal, responde el error en JSON y termina.
 */
function datos_procedimiento() {
    $d = array(
        'nombre'      => texto('nombre'),
        'descripcion' => texto('descripcion'),
        'precio'      => texto('precio'),
        'activo'      => texto('activo'),
    );
    if ($d['nombre'] === '' || $d['precio'] === '') {
        responder('error', 'Complete los campos obligatorios: nombre y precio.');
    }
    if (mb_strlen($d['nombre']) > 80) {
        responder('error', 'El nombre no puede superar los 80 caracteres.');
    }
    if (mb_strlen($d['descripcion']) > 255) {
        responder('error', 'La descripción no puede superar los 255 caracteres.');
    }
    $precio = monto_valido($d['precio']);
    if ($precio === false) {
        responder('error', 'El precio debe ser un número mayor o igual a 0.');
    }
    $d['precio'] = $precio;
    $d['activo'] = ($d['activo'] === '0') ? 0 : 1;
    return $d;
}

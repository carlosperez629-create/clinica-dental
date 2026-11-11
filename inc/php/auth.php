<?php
/**
 * Funciones comunes: sesión, respuestas JSON y validaciones reutilizables.
 */
session_set_cookie_params(array('httponly' => true, 'samesite' => 'Lax'));
session_start();

// Para páginas HTML: si no hay sesión, vuelve al login
function requerir_sesion_pagina() {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit;
    }
}

// Para los scripts AJAX: responde JSON 401 si no hay sesión
function requerir_sesion_api() {
    header('Content-Type: application/json; charset=utf-8');
    if (empty($_SESSION['usuario_id'])) {
        http_response_code(401);
        responder('error', 'Su sesión expiró. Inicie sesión nuevamente.');
    }
}

// Envía la respuesta JSON estándar {estado, mensaje, ...} y termina
function responder($estado, $mensaje, $extra = array()) {
    echo json_encode(array_merge(array('estado' => $estado, 'mensaje' => $mensaje), $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

// Las acciones que modifican datos solo se aceptan por POST
function solo_post() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        responder('error', 'Método no permitido.');
    }
}

// Lee un campo POST como texto sin espacios sobrantes ('' si no existe)
function texto($clave) {
    return (isset($_POST[$clave]) && is_string($_POST[$clave])) ? trim($_POST[$clave]) : '';
}

// Fecha en formato AAAA-MM-DD, real y que no sea futura
function fecha_valida($f) {
    $d = DateTime::createFromFormat('Y-m-d', $f);
    return $d && $d->format('Y-m-d') === $f && $f <= date('Y-m-d');
}

// Comprueba si existe un registro por id (solo tablas permitidas)
function existe($conexion, $tabla, $id) {
    if (!in_array($tabla, array('pacientes', 'procedimientos', 'atenciones'), true)) {
        return false;
    }
    $stmt = mysqli_prepare($conexion, "SELECT id FROM $tabla WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

// Convierte el resultado de una consulta preparada en un arreglo de filas
function filas($stmt) {
    $res = mysqli_stmt_get_result($stmt);
    $f = array();
    while ($r = mysqli_fetch_assoc($res)) {
        $f[] = $r;
    }
    return $f;
}

// Valida un monto (>= 0, máximo 99999999.99) y lo devuelve redondeado
function monto_valido($v) {
    if (!is_numeric($v) || $v < 0 || $v > 99999999.99) {
        return false;
    }
    return round((float)$v, 2);
}

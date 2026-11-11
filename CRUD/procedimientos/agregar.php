<?php
/** AGREGAR un procedimiento. Método POST. Campos: ver validar.php. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');
include_once(__DIR__ . '/validar.php');
solo_post();

$d = datos_procedimiento();

$stmt = mysqli_prepare($conexion, "INSERT INTO procedimientos (nombre, descripcion, precio, activo) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssdi", $d['nombre'], $d['descripcion'], $d['precio'], $d['activo']);
if (!mysqli_stmt_execute($stmt)) {
    // 1062 = valor duplicado (el nombre es UNIQUE)
    if (mysqli_errno($conexion) == 1062) {
        responder('error', 'Ya existe un procedimiento con ese nombre.');
    }
    responder('error', 'No se pudo registrar el procedimiento.');
}
responder('exito', 'Procedimiento registrado correctamente.', array('id' => mysqli_insert_id($conexion)));

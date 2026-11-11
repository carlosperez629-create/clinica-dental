<?php
/** AGREGAR un paciente. Método POST. Campos: ver validar.php. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');
include_once(__DIR__ . '/validar.php');
solo_post();

$d = datos_paciente();

$stmt = mysqli_prepare($conexion, "INSERT INTO pacientes (cedula, nombre, apellido, fecha_nacimiento, sexo, telefono, correo, direccion, antecedentes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssssssss", $d['cedula'], $d['nombre'], $d['apellido'], $d['fecha_nacimiento'], $d['sexo'], $d['telefono'], $d['correo'], $d['direccion'], $d['antecedentes']);
if (!mysqli_stmt_execute($stmt)) {
    // 1062 = valor duplicado (la cédula es UNIQUE)
    if (mysqli_errno($conexion) == 1062) {
        responder('error', 'Ya existe un paciente registrado con esa cédula.');
    }
    responder('error', 'No se pudo registrar el paciente.');
}
responder('exito', 'Paciente registrado correctamente.', array('id' => mysqli_insert_id($conexion)));

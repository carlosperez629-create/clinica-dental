<?php
/** EDITAR un paciente. Método POST. Campos: id + los de validar.php. */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');
include_once(__DIR__ . '/validar.php');
solo_post();

$id = (int)texto('id');
if ($id <= 0) {
    responder('error', 'El ID proporcionado no es válido.');
}
if (!existe($conexion, 'pacientes', $id)) {
    responder('error', 'No se encontró el paciente.');
}

$d = datos_paciente();

$stmt = mysqli_prepare($conexion, "UPDATE pacientes SET cedula = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, sexo = ?, telefono = ?, correo = ?, direccion = ?, antecedentes = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "sssssssssi", $d['cedula'], $d['nombre'], $d['apellido'], $d['fecha_nacimiento'], $d['sexo'], $d['telefono'], $d['correo'], $d['direccion'], $d['antecedentes'], $id);
if (!mysqli_stmt_execute($stmt)) {
    if (mysqli_errno($conexion) == 1062) {
        responder('error', 'Ya existe otro paciente con esa cédula.');
    }
    responder('error', 'No se pudo actualizar el paciente.');
}
responder('exito', 'Paciente actualizado correctamente.');

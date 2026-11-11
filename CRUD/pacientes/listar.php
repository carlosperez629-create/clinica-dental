<?php
/** LISTAR pacientes. Método GET. Parámetro opcional: q (busca por cédula, nombre o apellido). */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');

$q = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';
$like = '%' . $q . '%';

$stmt = mysqli_prepare($conexion, "SELECT id, cedula, nombre, apellido, fecha_nacimiento, sexo, telefono, correo
    FROM pacientes
    WHERE cedula LIKE ? OR nombre LIKE ? OR apellido LIKE ? OR CONCAT(nombre, ' ', apellido) LIKE ?
    ORDER BY apellido, nombre");
mysqli_stmt_bind_param($stmt, "ssss", $like, $like, $like, $like);
mysqli_stmt_execute($stmt);
responder('exito', 'OK', array('datos' => filas($stmt)));

<?php
/** LISTAR procedimientos. Método GET. Parámetro opcional: activos=1 (solo los activos). */
include_once(__DIR__ . '/../../php/auth.php');
requerir_sesion_api();
include_once(__DIR__ . '/../../php/conexion.php');

$sql = "SELECT id, nombre, descripcion, precio, activo FROM procedimientos";
if (isset($_GET['activos']) && $_GET['activos'] === '1') {
    $sql .= " WHERE activo = 1";
}
$sql .= " ORDER BY nombre";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_execute($stmt);
responder('exito', 'OK', array('datos' => filas($stmt)));

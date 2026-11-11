<?php
/**
 * Conexión a MySQL (MySQLi).
 * IMPORTANTE: al subir al hosting, cambie estos 4 valores por los datos
 * que muestra el panel (sección "MySQL Databases").
 */
$servidor   = "sql112.infinityfree.com";   // MySQL HOSTNAME
$usuario    = "if0_43409026";              // MySQL USERNAME
$password   = "4H7GVeQL3xWqwf";             // MySQL PASSWORD
$base_datos = "if0_43409026_clinica"; 

mysqli_report(MYSQLI_REPORT_OFF); // Los errores se manejan manualmente
$conexion = @mysqli_connect($servidor, $usuario, $password, $base_datos);

// Primero se comprueba la conexión y después se configura el charset
if (!$conexion) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(array('estado' => 'error', 'mensaje' => 'No se pudo conectar a la base de datos.')));
}
mysqli_set_charset($conexion, "utf8mb4");

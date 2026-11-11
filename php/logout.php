<?php
/** Cierra la sesión y vuelve al login. */
include_once(__DIR__ . '/auth.php');
$_SESSION = array();
session_destroy();
header('Location: ../index.php');
exit;

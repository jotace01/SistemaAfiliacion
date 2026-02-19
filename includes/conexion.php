<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);


$host = "mysql.railway.internal";
$user = "root";
$pass = "imbrADBmcQyDmipbdgzLrcJKhGuvWSAK";
$db   = "railway";
$port = 3306;

$conexion = new mysqli($host, $user, $pass, $db, $port);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

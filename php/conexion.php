<?php
const DB_SERVIDOR = 'localhost';
const DB_USUARIO  = 'root';
const DB_CLAVE    = '';
const DB_NOMBRE   = 'mundo_futbol';
const DB_PUERTO   = 3306;

function conectarBD(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conexion = new mysqli(DB_SERVIDOR, DB_USUARIO, DB_CLAVE, DB_NOMBRE, DB_PUERTO);

    $conexion->set_charset('utf8mb4');

    return $conexion;
}

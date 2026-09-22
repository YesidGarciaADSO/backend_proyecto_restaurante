<?php

$servidor = "localhost";
$usuario = "root";
$clave = "";
$bd = "restaurante";

$conexion = mysqli_connect($servidor, $usuario, $clave) or die('no se conecto a mysql');
mysqli_select_db($conexion, $bd) or die('no se conecto a la base de datos restaurante');
mysqli_set_charset($conexion, "utf8");

// Guardamos la conexion en una variable global para que todos los archivos la puedan usar
$GLOBALS['conexion'] = $conexion;

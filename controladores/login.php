<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/login.php';

$login = new Login($conexion);

$datos = json_decode(file_get_contents('php://input'));

// Si es POST y trae nombre es que se esta registrando
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $datos && isset($datos->nombre)) {
    $vec = $login->insertar($datos->nombre, $datos->apellido ?? '', $datos->correo, $datos->clave);
    echo json_encode($vec);
    exit;
}

// Si es POST y trae correo es que se esta logueando
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $datos && isset($datos->correo)) {
    $correo = $datos->correo;
    $clave = $datos->clave;
    $vec = $login->consulta($correo, $clave);
    echo json_encode($vec);
    exit;
}

echo json_encode(['resultado' => 'ERROR', 'mensaje' => 'Parametros invalidos']);

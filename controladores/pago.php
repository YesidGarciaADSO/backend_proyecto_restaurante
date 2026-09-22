<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/pago.php';

$control = $_GET['control'] ?? 'consulta';
$pago = new Pago($conexion);
$vec = [];

switch ($control) {
    case 'consulta':
        $vec = $pago->consulta();
        break;

    case 'insertar':
        $json = file_get_contents('php://input');
        $params = json_decode($json);
        $vec = $pago->insertar($params);
        break;

    case 'editarEstado':
        $id = $_GET['id'] ?? 0;
        $json = file_get_contents('php://input');
        $params = json_decode($json);
        $vec = $pago->editarEstado($id, $params);
        break;

    case 'eliminar':
        $id = $_GET['id'] ?? 0;
        $vec = $pago->eliminar($id);
        break;
}

echo json_encode($vec);

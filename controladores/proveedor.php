<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/proveedor.php';

$control = $_GET['control'] ?? 'consulta';
$proveedor = new Proveedor($conexion);
$vec = [];

switch ($control) {
    case 'consulta':
        $vec = $proveedor->consulta();
        break;

    case 'insertar':
        $json = file_get_contents('php://input');
        $params = json_decode($json);
        $vec = $proveedor->insertar($params);
        break;

    case 'editar':
        $json = file_get_contents('php://input');
        $id = $_GET['id'];
        $params = json_decode($json);
        $vec = $proveedor->editar($id, $params);
        break;

    case 'eliminar':
        $id = $_GET['id'];
        $vec = $proveedor->eliminar($id);
        break;
}

echo json_encode($vec);

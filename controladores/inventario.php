<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/inventario.php';

$control = $_GET['control'] ?? 'consulta';
$inventario = new Inventario($conexion);
$vec = [];

switch ($control) {
    case 'consulta':
        $vec = $inventario->consulta();
        break;

    case 'editar':
        $json = file_get_contents('php://input');
        $id = $_GET['id'];
        $params = json_decode($json);
        $vec = $inventario->editar($id, $params);
        break;
}

echo json_encode($vec);

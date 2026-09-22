<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/categoria.php';

$control = $_GET['control'] ?? 'consulta';
$categoria = new Categoria($conexion);
$vec = [];

switch ($control) {
    case 'consulta':
        $vec = $categoria->consulta();
        break;

    case 'insertar':
        $json = file_get_contents('php://input');
        $params = json_decode($json);
        $vec = $categoria->insertar($params);
        break;

    case 'editar':
        $json = file_get_contents('php://input');
        $id = $_GET['id'] ?? 0;
        $params = json_decode($json);
        $vec = $categoria->editar($id, $params);
        break;

    case 'eliminar':
        $id = $_GET['id'] ?? 0;
        $vec = $categoria->eliminar($id);
        break;
}

echo json_encode($vec);

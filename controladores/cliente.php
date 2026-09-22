<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/cliente.php';

$control = $_GET['control'] ?? 'consulta';
$clienteModel = new Cliente($conexion);
$vec = [];

$json = file_get_contents('php://input');
$params = json_decode($json);

switch ($control) {
    case 'consulta':
        $vec = $clienteModel->consulta();
        break;

    case 'insertar':
        if ($params && isset($params->nombre)) {
            $vec = $clienteModel->insertar($params);
        } else {
            $vec = ["resultado" => "ERROR", "mensaje" => "Datos incompletos"];
        }
        break;

    case 'editar':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0 && $params && isset($params->nombre)) {
            $vec = $clienteModel->editar($id, $params);
        } else {
            $vec = ["resultado" => "ERROR", "mensaje" => "Datos invalidos"];
        }
        break;

    case 'eliminar':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $vec = $clienteModel->eliminar($id);
        } else {
            $vec = ["resultado" => "ERROR", "mensaje" => "ID invalido"];
        }
        break;
}

echo json_encode($vec);

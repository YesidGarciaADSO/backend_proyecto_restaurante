<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/pedido.php';

$control = $_GET['control'] ?? 'consulta';
$pedido = new Pedido($conexion);
$vec = [];

switch ($control) {
    case 'consulta':
        $vec = $pedido->consulta();
        break;

    case 'consultaPorDomiciliario':
        $id_rep = $_GET['id_domiciliario'] ?? 0;
        $vec = $pedido->consultaPorDomiciliario($id_rep);
        break;

    case 'insertar':
        $json = file_get_contents('php://input');
        $params = json_decode($json);
        $vec = $pedido->insertar($params);
        break;

    case 'editar':
        $json = file_get_contents('php://input');
        $id = $_GET['id'] ?? 0;
        $params = json_decode($json);
        $vec = $pedido->editar($id, $params);
        break;

    case 'eliminar':
        $id = $_GET['id'] ?? 0;
        $vec = $pedido->eliminar($id);
        break;

    case 'cambiarEstado':
        $json = file_get_contents('php://input');
        $id = $_GET['id'] ?? 0;
        $params = json_decode($json);
        $vec = $pedido->cambiarEstado($id, $params);
        break;

    case 'asignarRepartidor':
        $json = file_get_contents('php://input');
        $id = $_GET['id'] ?? 0;
        $params = json_decode($json);
        $id_domiciliario = intval($params->id_domiciliario ?? 0);
        $vec = $pedido->asignarRepartidor($id, $id_domiciliario);
        break;

    case 'consultarRepartidores':
        $sqlRep = "SELECT * FROM domiciliario WHERE disponible = 1 ORDER BY nombre";
        $resRep = mysqli_query($conexion, $sqlRep);
        $vec = [];
        if ($resRep) {
            while ($row = mysqli_fetch_assoc($resRep)) {
                $vec[] = $row;
            }
        }
        break;
}

echo json_encode($vec);

<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/producto.php';

$control = $_GET['control'] ?? $_GET['accion'] ?? 'consulta';
$producto = new Producto($conexion);
$vec = [];

switch ($control) {
    case 'consulta':
        $vec = $producto->consulta();
        break;

    case 'insertar':
        $json = file_get_contents('php://input');
        $params = json_decode($json);
        $vec = $producto->insertar($params);
        break;

    case 'editar':
        $json = file_get_contents('php://input');
        $id = $_GET['id'];
        $params = json_decode($json);
        $vec = $producto->editar($id, $params);
        break;

    case 'eliminar':
        $id = $_GET['id'];
        $vec = $producto->eliminar($id);
        break;

    case 'consultaVentas':
        $sql = "SELECT p.id_pedido AS id, p.fecha, c.nombre AS cliente, 
                       p.estado, p.tipo, p.metodo_pago, p.total 
                FROM pedido p
                LEFT JOIN cliente c ON p.fo_cliente = c.id_cliente
                ORDER BY p.fecha DESC";

        global $conexion;
        $resultado = mysqli_query($conexion, $sql);
        $vec = [];

        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $vec[] = $row;
            }
        }
        break;
}

echo json_encode($vec);

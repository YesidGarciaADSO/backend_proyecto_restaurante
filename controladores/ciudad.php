<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/ciudad.php';

$control = $_GET['control'] ?? 'consulta';
$ciudadModel = new Ciudad($conexion);
$vec = [];

switch ($control) {
    // Traer las ciudades, si trae departamento filtra por el
    case 'consulta':
        $id_dep = isset($_GET['fo_departamento']) ? $_GET['fo_departamento'] : null;
        $vec = $ciudadModel->consulta($id_dep);
        break;

    default:
        $vec = ["resultado" => "ERROR", "mensaje" => "Acción no válida"];
        break;
}

echo json_encode($vec);
exit();

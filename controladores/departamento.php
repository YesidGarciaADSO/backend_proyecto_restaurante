<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';

// Consulta simple que trae todos los departamentos ordenados por nombre
$sql = "SELECT id_departamento, nombre FROM departamento ORDER BY nombre ASC";
$res = mysqli_query($conexion, $sql);

// Metemos los resultados en un array
$vec = [];
if ($res) {
    while ($row = mysqli_fetch_array($res)) {
        $vec[] = [
            "id_departamento" => $row['id_departamento'],
            "nombre" => $row['nombre']
        ];
    }
}

echo json_encode($vec);
exit();

<?php
require_once __DIR__ . '/../helper_cors.php';
require_once __DIR__ . '/../modelos/conexion.php';

$control = $_GET['control'] ?? 'consulta';
$vec = [];

switch ($control) {
    case 'consulta':
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.telefono, u.activo, 
                       r.nombre AS nombre_rol, u.fo_rol
                FROM usuario u
                INNER JOIN rol r ON u.fo_rol = r.id_rol
                ORDER BY u.id_usuario DESC";
        $resultado = mysqli_query($conexion, $sql);
        while ($row = mysqli_fetch_assoc($resultado)) {
            $vec[] = $row;
        }
        break;

    case 'roles':
        $sql = "SELECT id_rol, nombre FROM rol ORDER BY nombre ASC";
        $resultado = mysqli_query($conexion, $sql);
        while ($row = mysqli_fetch_assoc($resultado)) {
            $vec[] = $row;
        }
        break;

    case 'insertar':
        $json = file_get_contents('php://input');
        $p = json_decode($json);
        $sql = "INSERT INTO usuario (nombre, apellido, correo, clave, telefono, fo_rol) 
                VALUES ('$p->nombre', '$p->apellido', '$p->correo', '$p->password', '$p->telefono', $p->fo_rol)";
        if (mysqli_query($conexion, $sql)) {
            $vec = ["resultado" => "OK", "mensaje" => "Usuario creado con exito"];
        } else {
            $vec = ["resultado" => "ERROR", "mensaje" => mysqli_error($conexion)];
        }
        break;

    case 'editar':
        $id = $_GET['id'];
        $json = file_get_contents('php://input');
        $p = json_decode($json);
        $sqlClave = "";
        if (!empty($p->password)) {
            $sqlClave = ", clave = '$p->password'";
        }
        $sql = "UPDATE usuario SET nombre = '$p->nombre', apellido = '$p->apellido', correo = '$p->correo', 
                    telefono = '$p->telefono', fo_rol = $p->fo_rol $sqlClave
                WHERE id_usuario = $id";
        if (mysqli_query($conexion, $sql)) {
            $vec = ["resultado" => "OK", "mensaje" => "Usuario actualizado con exito"];
        } else {
            $vec = ["resultado" => "ERROR", "mensaje" => mysqli_error($conexion)];
        }
        break;

    case 'cambiarEstado':
        $id = $_GET['id'];
        $json = file_get_contents('php://input');
        $p = json_decode($json);
        $sql = "UPDATE usuario SET activo = $p->activo WHERE id_usuario = $id";
        if (mysqli_query($conexion, $sql)) {
            $vec = ["resultado" => "OK"];
        }
        break;
}

echo json_encode($vec);

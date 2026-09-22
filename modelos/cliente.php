<?php

class Cliente
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Esta funcion trae todos los clientes que estan activos
    // Tambien trae el nombre de la ciudad haciendo JOIN con la tabla ciudad
    public function consulta()
    {
        $sql = "SELECT c.*, ci.nombre AS nombre_ciudad 
            FROM cliente c 
            LEFT JOIN ciudad ci ON c.fo_ciudad = ci.id_ciudad 
            WHERE c.estado = 'Activo'
            ORDER BY c.nombre ASC";

        $res = mysqli_query($this->conexion, $sql);
        $vec = [];
        if ($res) {
            while ($row = mysqli_fetch_array($res)) {
                $vec[] = $row;
            }
        }
        return $vec;
    }

    // Esta funcion crea un cliente nuevo
    // Le pasan un objeto con nombre, telefono, direccion y ciudad
    public function insertar($params)
    {
        // Limpiamos los textos para que no haya inyeccion SQL
        $nombre    = mysqli_real_escape_string($this->conexion, $params->nombre);
        $telefono  = isset($params->telefono) ? mysqli_real_escape_string($this->conexion, $params->telefono) : '';
        $direccion = isset($params->direccion) ? mysqli_real_escape_string($this->conexion, $params->direccion) : '';
        $fo_ciudad = isset($params->fo_ciudad) ? (int)$params->fo_ciudad : 0;

        // Si no trae ciudad mandamos error porque es obligatoria
        if ($fo_ciudad === 0) {
            return ["resultado" => "ERROR", "mensaje" => "Ciudad requerida"];
        }

        $sql = "INSERT INTO cliente (nombre, telefono, direccion, fo_ciudad) 
                VALUES ('$nombre', '$telefono', '$direccion', $fo_ciudad)";
        $res = mysqli_query($this->conexion, $sql);

        return $res ? ["resultado" => "OK"] : ["resultado" => "ERROR", "mensaje" => "No se pudo registrar"];
    }

    // Esta funcion edita los datos de un cliente existente
    public function editar($id, $params)
    {
        $id        = (int)$id;
        $nombre    = mysqli_real_escape_string($this->conexion, $params->nombre);
        $telefono  = isset($params->telefono) ? mysqli_real_escape_string($this->conexion, $params->telefono) : '';
        $direccion = isset($params->direccion) ? mysqli_real_escape_string($this->conexion, $params->direccion) : '';
        $fo_ciudad = isset($params->fo_ciudad) ? (int)$params->fo_ciudad : 0;

        // Validamos que el ID y la ciudad sean validos
        if ($id === 0 || $fo_ciudad === 0) {
            return ["resultado" => "ERROR", "mensaje" => "Datos de actualización inválidos"];
        }

        $sql = "UPDATE cliente 
                SET nombre = '$nombre', telefono = '$telefono', direccion = '$direccion', fo_ciudad = $fo_ciudad 
                WHERE id_cliente = $id";
        $res = mysqli_query($this->conexion, $sql);

        return $res ? ["resultado" => "OK"] : ["resultado" => "ERROR", "mensaje" => "No se pudo actualizar"];
    }

    // Esta funcion "elimina" un cliente pero solo lo pone en Inactivo
    // No lo borra de la base de datos para no perder los pedidos que tenga
    public function eliminar($id)
    {
        $id = (int)$id;
        if ($id === 0) {
            return ["resultado" => "ERROR", "mensaje" => "ID no válido"];
        }

        // Solo cambiamos el estado a Inactivo, no borramos el registro
        $sql = "UPDATE cliente SET estado = 'Inactivo' WHERE id_cliente = $id";
        $res = mysqli_query($this->conexion, $sql);

        return $res ? ["resultado" => "OK"] : ["resultado" => "ERROR", "mensaje" => "No se pudo eliminar"];
    }
}

<?php


class Proveedor
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Esta funcion trae todos los proveedores que estan activos
    public function consulta()
    {
        $sql = "SELECT * FROM proveedor WHERE activo = 1 ORDER BY nombre";
        $res = mysqli_query($this->conexion, $sql) or die("No se encontró la tabla proveedor");

        $vec = [];
        while ($row = mysqli_fetch_array($res)) {
            $vec[] = $row;
        }

        return $vec;
    }

    // Esta funcion "elimina" un proveedor pero solo lo pone inactivo
    // No lo borramos porque puede tener productos asociados
    public function eliminar($id)
    {
        $sql = "UPDATE proveedor SET activo = 0 WHERE id_proveedor = $id";
        mysqli_query($this->conexion, $sql) or die("No se pudo desactivar el proveedor");

        $vec = [];
        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Proveedor desactivado con éxito";

        return $vec;
    }

    // Esta funcion crea un proveedor nuevo
    // Recibe nombre, contacto, telefono, correo y ciudad
    public function insertar($params)
    {
        // Limpiamos todos los textos para que no haya inyeccion SQL
        $nombre    = mysqli_real_escape_string($this->conexion, $params->nombre);
        $contacto  = mysqli_real_escape_string($this->conexion, $params->contacto ?? '');
        $telefono  = mysqli_real_escape_string($this->conexion, $params->telefono ?? '');
        $correo    = mysqli_real_escape_string($this->conexion, $params->correo ?? '');
        $ciudad    = mysqli_real_escape_string($this->conexion, $params->ciudad ?? '');

        $sql = "INSERT INTO proveedor (nombre, contacto, telefono, correo, ciudad) 
                VALUES ('$nombre', '$contacto', '$telefono', '$correo', '$ciudad')";
        mysqli_query($this->conexion, $sql) or die("No se insertó el proveedor");

        $vec = [];
        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Proveedor guardado con éxito";

        return $vec;
    }

    // Esta funcion edita los datos de un proveedor existente
    public function editar($id, $params)
    {
        $nombre    = mysqli_real_escape_string($this->conexion, $params->nombre);
        $contacto  = mysqli_real_escape_string($this->conexion, $params->contacto ?? '');
        $telefono  = mysqli_real_escape_string($this->conexion, $params->telefono ?? '');
        $correo    = mysqli_real_escape_string($this->conexion, $params->correo ?? '');
        $ciudad    = mysqli_real_escape_string($this->conexion, $params->ciudad ?? '');

        $sql = "UPDATE proveedor SET 
                    nombre = '$nombre', 
                    contacto = '$contacto', 
                    telefono = '$telefono', 
                    correo = '$correo',
                    ciudad = '$ciudad'
                    WHERE id_proveedor = $id";
        mysqli_query($this->conexion, $sql) or die("No se editó el proveedor");

        $vec = [];
        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Proveedor actualizado con éxito";

        return $vec;
    }
}

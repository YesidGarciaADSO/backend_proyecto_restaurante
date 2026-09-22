<?php

class Producto
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Esta funcion trae todos los productos con su categoria
    public function consulta()
    {
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, p.activo, p.proveedor,
                       c.nombre AS nombre_categoria
                FROM producto p
                JOIN categoria c ON c.id_categoria = p.fo_categoria
                ORDER BY p.id_producto DESC";

        $res = mysqli_query($this->conexion, $sql) or die("Error en la consulta: " . mysqli_error($this->conexion));

        $vec = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $vec[] = $row;
        }

        return $vec;
    }

    // Esta funcion elimina un producto por ID
    public function eliminar($id)
    {
        $sql = "DELETE FROM producto WHERE id_producto = $id";
        mysqli_query($this->conexion, $sql) or die("no elimino el registro");

        $vec = [];
        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Se elimino el registro";

        return $vec;
    }

    // Esta funcion crea un producto nuevo
    public function insertar($params)
    {
        $precio = (isset($params->precio) && is_numeric($params->precio)) ? $params->precio : 0;
        $fo_categoria = (isset($params->fo_categoria) && is_numeric($params->fo_categoria)) ? $params->fo_categoria : 0;
        $activo = isset($params->activo) ? $params->activo : 1;
        $stock = (isset($params->stock) && is_numeric($params->stock)) ? intval($params->stock) : 0;
        $proveedor = mysqli_real_escape_string($this->conexion, $params->proveedor ?? '');

        $sql = "INSERT INTO producto(nombre, descripcion, precio, stock, fo_categoria, activo, proveedor)
            VALUES('$params->nombre', '$params->descripcion', $precio, $stock, $fo_categoria, $activo, '$proveedor')";
        mysqli_query($this->conexion, $sql) or die("Error SQL: " . mysqli_error($this->conexion));

        $vec = [];
        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Se inserto el registro";

        return $vec;
    }

    // Esta funcion edita los datos de un producto existente
    public function editar($id, $params)
    {
        $stock = (isset($params->stock) && is_numeric($params->stock)) ? intval($params->stock) : 0;
        $proveedor = mysqli_real_escape_string($this->conexion, $params->proveedor ?? '');

        $sql = "UPDATE producto SET nombre = '$params->nombre', descripcion = '$params->descripcion', precio = $params->precio, stock = $stock, fo_categoria = $params->fo_categoria, activo = $params->activo, proveedor = '$proveedor' WHERE id_producto = $id";
        mysqli_query($this->conexion, $sql) or die("no edito el registro");

        $vec = [];
        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Se edito el registro";

        return $vec;
    }
}

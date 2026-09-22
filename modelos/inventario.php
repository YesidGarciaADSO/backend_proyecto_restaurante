<?php

class Inventario
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Esta funcion trae todos los productos con su stock
    public function consulta()
    {
        $sql = "SELECT p.id_producto, p.nombre, p.stock, c.nombre AS nombre_categoria
                FROM producto p
                JOIN categoria c ON c.id_categoria = p.fo_categoria
                ORDER BY p.nombre ASC";
        $res = mysqli_query($this->conexion, $sql) or die("No se pudo consultar el inventario");

        $vec = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $row['stock'] = (int)$row['stock'];
            $vec[] = $row;
        }

        return $vec;
    }

    // Esta funcion actualiza el stock de un producto
    public function editar($id, $params)
    {
        $vec = [];
        $stock = intval($params->stock ?? 0);

        $sql = "UPDATE producto SET stock = $stock WHERE id_producto = $id";
        mysqli_query($this->conexion, $sql) or die("No se pudo actualizar el stock");

        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Stock actualizado exitosamente";

        return $vec;
    }
}

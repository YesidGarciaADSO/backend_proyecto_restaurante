<?php

class Categoria
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Esta funcion trae todas las categorias ordenadas por nombre
    public function consulta()
    {
        $sql = "SELECT * FROM categoria ORDER BY nombre ASC";
        $res = mysqli_query($this->conexion, $sql) or die("No se encontró la tabla categoria");

        $vec = [];
        while ($row = mysqli_fetch_array($res)) {
            $vec[] = $row;
        }

        return $vec;
    }

    // Esta funcion crea una categoria nueva
    public function insertar($params)
    {
        $vec = [];
        
        // Solo necesitamos el nombre de la categoria
        $sql = "INSERT INTO categoria (nombre) VALUES ('$params->nombre')";
        mysqli_query($this->conexion, $sql) or die("No se pudo insertar la categoría");

        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Categoría registrada con éxito";

        return $vec;
    }

    // Esta funcion edita el nombre de una categoria
    public function editar($id, $params)
    {
        $vec = [];

        $sql = "UPDATE categoria SET nombre = '$params->nombre' WHERE id_categoria = $id";
        mysqli_query($this->conexion, $sql) or die("No se pudo editar la categoría");

        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Categoría actualizada con éxito";

        return $vec;
    }

    // Esta funcion elimina una categoria por ID
    public function eliminar($id)
    {
        $vec = [];

        $sql = "DELETE FROM categoria WHERE id_categoria = $id";
        mysqli_query($this->conexion, $sql) or die("No se pudo eliminar la categoría");

        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Categoría eliminada con éxito";

        return $vec;
    }
}

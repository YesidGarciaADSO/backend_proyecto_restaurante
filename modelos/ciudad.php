<?php

class Ciudad {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Esta funcion trae todas las ciudades
    public function consulta($id_dep = null) {
        $sql = "SELECT * FROM ciudad";
        
        if ($id_dep !== null) {
            $id_dep = (int)$id_dep;
            $sql .= " WHERE fo_departamento = $id_dep";
        }
        
        // Ordenamos por nombre para que quede bonito
        $sql .= " ORDER BY nombre ASC";
        $res = mysqli_query($this->conexion, $sql);

        $vec = [];
        if ($res) {
            while ($row = mysqli_fetch_array($res)) {
                $vec[] = $row;
            }
        }
        return $vec;
    }
}

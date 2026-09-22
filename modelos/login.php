<?php

class Login {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Esta funcion verifica si el usuario puede entrar al sistema
    public function consulta($correo, $clave) {
        $correo = mysqli_real_escape_string($this->conexion, $correo);
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.telefono, u.clave, u.activo,
                        r.nombre AS nombre_rol
                FROM usuario u
                LEFT JOIN rol r ON u.fo_rol = r.id_rol
                WHERE u.correo = '$correo' AND u.activo = 1";
        $res = mysqli_query($this->conexion, $sql);
        $vec = [];

        while ($row = mysqli_fetch_array($res)) {
            $vec[] = $row;
        }

        if (empty($vec)) {
            $vec[0] = array("validar" => "no valida");
        } else {
            $claveAlmacenada = $vec[0]['clave'];
            $valida = ($clave === $claveAlmacenada);

            if ($valida) {
                $vec[0]['validar'] = "valida";
                unset($vec[0]['clave']);
            } else {
                $vec[0] = array("validar" => "no valida");
            }
        }

        return $vec;
    }

    // Esta funcion crea un usuario nuevo
    public function insertar($nombre, $apellido, $correo, $clave) {
        $nombre = mysqli_real_escape_string($this->conexion, $nombre);
        $apellido = mysqli_real_escape_string($this->conexion, $apellido);
        $correo = mysqli_real_escape_string($this->conexion, $correo);
        $claveLimpia = mysqli_real_escape_string($this->conexion, $clave);
        $sql = "INSERT INTO usuario (nombre, apellido, correo, clave, fo_rol, activo) 
                VALUES ('$nombre', '$apellido', '$correo', '$claveLimpia', 1, 1)";
        if (mysqli_query($this->conexion, $sql)) {
            return ['resultado' => 'OK', 'mensaje' => 'Usuario registrado exitosamente'];
        }
        return ['resultado' => 'ERROR', 'mensaje' => 'No se pudo registrar el usuario'];
    }
}

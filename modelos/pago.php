<?php

class Pago
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Esta funcion trae todos los pagos que se han hecho
    // Tambien trae el total del pedido para saber cuanto se pago
    public function consulta()
    {
        // Hacemos JOIN con pedido para traer el total del pedido
        $sql = "SELECT p.*, pe.total AS pedido_total 
                FROM pago p
                INNER JOIN pedido pe ON p.fo_pedido = pe.id_pedido
                ORDER BY p.fecha DESC";
        $res = mysqli_query($this->conexion, $sql) or die("No se encontraron registros de pagos");

        $vec = [];
        while ($row = mysqli_fetch_array($res)) {
            // Convertimos el monto a float para que no haya problemas
            $row['monto'] = (float)$row['monto'];
            $vec[] = $row;
        }

        return $vec;
    }

    // Esta funcion crea un pago nuevo
    // Recibe: ID del pedido, monto, metodo de pago y estado
    public function insertar($params)
    {
        $vec = [];
        
        $sql = "INSERT INTO pago (fo_pedido, monto, metodo, estado) 
                VALUES ($params->fo_pedido, $params->monto, '$params->metodo', '$params->estado')";
        mysqli_query($this->conexion, $sql) or die("No se pudo registrar el pago");

        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Pago registrado exitosamente";

        return $vec;
    }

    // Esta funcion solo cambia el estado de un pago (pendiente, aprobado, rechazado)
    public function editarEstado($id, $params)
    {
        $vec = [];

        $sql = "UPDATE pago SET estado = '$params->estado' WHERE id_pago = $id";
        mysqli_query($this->conexion, $sql) or die("No se pudo actualizar el estado del pago");

        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Estado del pago actualizado con éxito";

        return $vec;
    }

    // Esta funcion busca un pago por su ID
    public function buscar($id)
    {
        $sql = "SELECT * FROM pago WHERE id_pago = $id";
        $res = mysqli_query($this->conexion, $sql) or die("No se pudo buscar el pago");

        $vec = [];
        if ($row = mysqli_fetch_array($res)) {
            $row['monto'] = (float)$row['monto'];
            $vec[] = $row;
        }

        return $vec;
    }

    // Esta funcion trae todos los pagos de un pedido especifico
    // Sirve para ver cuantos pagos se hicieron para un pedido
    public function porPedido($id_pedido)
    {
        $sql = "SELECT * FROM pago WHERE fo_pedido = $id_pedido ORDER BY fecha DESC";
        $res = mysqli_query($this->conexion, $sql) or die("No se pudieron obtener los pagos del pedido");

        $vec = [];
        while ($row = mysqli_fetch_array($res)) {
            $row['monto'] = (float)$row['monto'];
            $vec[] = $row;
        }

        return $vec;
    }

    // Esta funcion edita todos los datos de un pago
    public function editar($id, $params)
    {
        $vec = [];

        $sql = "UPDATE pago SET 
                    monto = '$params->monto', 
                    metodo = '$params->metodo', 
                    estado = '$params->estado' 
                WHERE id_pago = $id";
                
        mysqli_query($this->conexion, $sql) or die("No se pudo actualizar el pago");

        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Pago actualizado exitosamente";

        return $vec;
    }

    // Esta funcion "elimina" un pago pero solo lo pone en rechazado
    // No lo borra porque necesitamos el historial
    public function eliminar($id)
    {
        $vec = [];

        $sql = "UPDATE pago SET estado = 'rechazado' WHERE id_pago = $id";
        mysqli_query($this->conexion, $sql) or die("No se pudo anular el pago");

        $vec["resultado"] = "OK";
        $vec["mensaje"] = "Pago anulado/rechazado exitosamente";

        return $vec;
    }
}

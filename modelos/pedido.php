<?php

class Pedido
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Esta funcion trae todos los pedidos con el nombre del cliente
    public function consulta()
    {
        $sql = "SELECT p.*, c.nombre AS nombre_cliente,
                d.nombre AS nombre_repartidor
                FROM pedido p 
                INNER JOIN cliente c ON p.fo_cliente = c.id_cliente
                LEFT JOIN domiciliario d ON p.fo_domiciliario = d.id_domiciliario
                ORDER BY p.id_pedido DESC";

        $res = mysqli_query($this->conexion, $sql);
        $vec = [];

        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $vec[] = $row;
            }
        }
        return $vec;
    }

    // Esta funcion trae los pedidos de un domiciliario especifico
    public function consultaPorDomiciliario($id_domiciliario)
    {
        $id_domiciliario = intval($id_domiciliario);
        $sql = "SELECT p.*, c.nombre AS nombre_cliente,
                d.nombre AS nombre_repartidor
                FROM pedido p 
                INNER JOIN cliente c ON p.fo_cliente = c.id_cliente
                LEFT JOIN domiciliario d ON p.fo_domiciliario = d.id_domiciliario
                WHERE p.fo_domiciliario = $id_domiciliario
                ORDER BY p.id_pedido DESC";

        $res = mysqli_query($this->conexion, $sql);
        $vec = [];

        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $vec[] = $row;
            }
        }
        return $vec;
    }

    // Esta funcion crea un pedido nuevo
    public function insertar($params)
    {
        $vec = [];
        $telefono = mysqli_real_escape_string($this->conexion, $params->telefono ?? '');
        $nombreCliente = mysqli_real_escape_string($this->conexion, $params->cliente ?? 'Cliente General');
        $direccion = mysqli_real_escape_string($this->conexion, $params->direccion ?? '');
        $tipo = mysqli_real_escape_string($this->conexion, $params->tipo ?? 'local');
        $metodoPago = mysqli_real_escape_string($this->conexion, $params->metodo_pago ?? 'Efectivo');
        $productos = $params->productos ?? [];
        $total = floatval($params->total ?? 0);

        // Buscar o crear cliente por telefono
        $fo_cliente = 0;
        if ($telefono) {
            $resCli = mysqli_query($this->conexion, "SELECT id_cliente FROM cliente WHERE telefono = '$telefono' LIMIT 1");
            if ($resCli && mysqli_num_rows($resCli) > 0) {
                $rowCli = mysqli_fetch_assoc($resCli);
                $fo_cliente = $rowCli['id_cliente'];
            } else {
                $dirEsc = $direccion ?: 'Sin direccion';
                mysqli_query($this->conexion, "INSERT INTO cliente (nombre, telefono, direccion, estado) VALUES ('$nombreCliente', '$telefono', '$dirEsc', 'Activo')");
                $fo_cliente = mysqli_insert_id($this->conexion);
            }
        } else {
            mysqli_query($this->conexion, "INSERT INTO cliente (nombre, telefono, direccion, estado) VALUES ('$nombreCliente', 'N/A', 'Sin direccion', 'Activo')");
            $fo_cliente = mysqli_insert_id($this->conexion);
        }

        // Insertar pedido
        if ($tipo === 'domicilio' && $direccion) {
            $sql = "INSERT INTO pedido (fo_cliente, fecha, estado, tipo, metodo_pago, total, direccion_entrega) 
                    VALUES ('$fo_cliente', NOW(), 'pendiente', '$tipo', '$metodoPago', '$total', '$direccion')";
        } else {
            $sql = "INSERT INTO pedido (fo_cliente, fecha, estado, tipo, metodo_pago, total) 
                    VALUES ('$fo_cliente', NOW(), 'pendiente', '$tipo', '$metodoPago', '$total')";
        }

        if (mysqli_query($this->conexion, $sql)) {
            $id_pedido = mysqli_insert_id($this->conexion);
            $vec["resultado"] = "OK";
            $vec["mensaje"] = "Pedido registrado exitosamente";
            $vec["id_pedido"] = $id_pedido;

            // Insertar detalles del pedido
            if (is_array($productos) && count($productos) > 0) {
                foreach ($productos as $prod) {
                    $fo_producto = intval($prod->fo_producto ?? $prod->id_producto ?? 0);
                    $cantidad = intval($prod->cantidad ?? 1);
                    $precio_unitario = floatval($prod->precio_unitario ?? $prod->precio ?? 0);
                    if ($fo_producto > 0 && $cantidad > 0) {
                        mysqli_query($this->conexion, 
                            "INSERT INTO detalle_pedido (fo_pedido, fo_producto, cantidad, precio_unitario) 
                            VALUES ('$id_pedido', '$fo_producto', '$cantidad', '$precio_unitario')");
                    }
                }
            }
        } else {
            $vec["resultado"] = "ERROR";
            $vec["mensaje"] = "No se pudo registrar el pedido: " . mysqli_error($this->conexion);
        }

        return $vec;
    }

    // Esta funcion edita un pedido
    public function editar($id, $params)
    {
        $vec = [];
        $id = intval($id);
        $fo_cliente = intval($params->fo_cliente);
        $total = floatval($params->total);
        $tipo = mysqli_real_escape_string($this->conexion, $params->tipo ?? 'local');

        $sql = "UPDATE pedido SET fo_cliente = '$fo_cliente', tipo = '$tipo', total = '$total' WHERE id_pedido = '$id'";

        if (mysqli_query($this->conexion, $sql)) {
            $vec["resultado"] = "OK";
            $vec["mensaje"] = "Pedido actualizado exitosamente";
        } else {
            $vec["resultado"] = "ERROR";
            $vec["mensaje"] = "No se pudo actualizar el pedido: " . mysqli_error($this->conexion);
        }

        return $vec;
    }

    // Esta funcion cambia el estado de un pedido
    public function cambiarEstado($id, $params)
    {
        $vec = [];
        $id = intval($id);
        $estado = mysqli_real_escape_string($this->conexion, $params->estado);

        $sql = "UPDATE pedido SET estado = '$estado' WHERE id_pedido = '$id'";

        if (mysqli_query($this->conexion, $sql)) {
            $vec["resultado"] = "OK";
            $vec["mensaje"] = "Estado actualizado a: " . $estado;
        } else {
            $vec["resultado"] = "ERROR";
            $vec["mensaje"] = "No se pudo cambiar el estado: " . mysqli_error($this->conexion);
        }

        return $vec;
    }

    // Esta funcion asigna un domiciliario a un pedido
    public function asignarRepartidor($id_pedido, $id_domiciliario)
    {
        $vec = [];
        $id_pedido = intval($id_pedido);
        $id_domiciliario = intval($id_domiciliario);

        $sql = "UPDATE pedido SET fo_domiciliario = '$id_domiciliario', estado = 'en_preparacion' 
                WHERE id_pedido = '$id_pedido' AND tipo = 'domicilio'";

        if (mysqli_query($this->conexion, $sql)) {
            $vec["resultado"] = "OK";
            $vec["mensaje"] = "Repartidor asignado exitosamente";
        } else {
            $vec["resultado"] = "ERROR";
            $vec["mensaje"] = "No se pudo asignar repartidor: " . mysqli_error($this->conexion);
        }

        return $vec;
    }

    // Esta funcion cancela un pedido
    public function eliminar($id)
    {
        $vec = [];
        $id = intval($id);

        $sql = "UPDATE pedido SET estado = 'cancelado' WHERE id_pedido = '$id'";

        if (mysqli_query($this->conexion, $sql)) {
            $vec["resultado"] = "OK";
            $vec["mensaje"] = "Pedido cancelado exitosamente";
        } else {
            $vec["resultado"] = "ERROR";
            $vec["mensaje"] = "No se pudo cancelar el pedido: " . mysqli_error($this->conexion);
        }

        return $vec;
    }
}

<?php
session_start();

include("../modelo/conexion.php");
include "controlador_crud.php";



$clientes_resultado = $conexion->query("SELECT nombre FROM usuarios WHERE tipo = 'cliente'");
$empleados_resultado = $conexion->query("SELECT nombre FROM usuarios WHERE tipo = 'empleado'");
$horarios_manicure = $conexion->query("SELECT DISTINCT dia, hora FROM horarios WHERE estado = 'disponible'");
$horarios_corte = $conexion->query("SELECT DISTINCT dia, hora FROM horarios_corte WHERE estado = 'disponible'");

// Crear cita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["form_type"]) && $_POST["form_type"] === "agregar_cita") {
    $hora = $_POST["hora"];
    $dia = $_POST["dia"];
    $nombre_cliente = $_POST["nombre_cliente"];
    $nombre_empleado = $_POST["nombre_empleado"];
    $descripcion = $_POST["descripcion"] ?? null;
    $tabla = $_POST["tabla"]; // horarios_manicure o horarios_corte
    $usuarioAdmin = $_SESSION['usuario'] ?? null;


    // Tipo de servicio según tabla
    $tipo_servicio = ($tabla === 'horarios_manicure') ? 'Manicure y Pedicure' : 'Corte de pelo';

    // Verificar duplicado para mismo empleado, día y hora
    $verificar_sql = "SELECT COUNT(*) FROM citas WHERE hora = ? AND dia = ? AND nombre_empleado = ?";
    $stmt = $conexion->prepare($verificar_sql);
    $stmt->bind_param("sss", $hora, $dia, $nombre_empleado);
    $stmt->execute();
    $stmt->bind_result($existe);
    $stmt->fetch();
    $stmt->close();

    if ($existe > 0) {
        header("Location: ../vistas/administrador.php?mensaje=cita_agregada");
        exit();
    } else {

// Insertar cita
        $stmt = $conexion->prepare("INSERT INTO citas (dia, hora, nombre_cliente, nombre_empleado, tipo_servicio, descripcion, usuarioAdmin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $dia, $hora, $nombre_cliente, $nombre_empleado, $tipo_servicio, $descripcion, $usuarioAdmin);
        $stmt->execute();
        $stmt->close();

        // Marcar horario como ocupado
        $estado = 'ocupado';
        if ($tabla === "horarios_manicure") {
            $sql = "UPDATE horarios SET estado = ? WHERE dia = ? AND hora = ?";
        } else {
            $sql = "UPDATE horarios_corte SET estado = ? WHERE dia = ? AND hora = ?";
        }
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sss", $estado, $dia, $hora);
        if ($stmt->execute()) {
            echo "✅ Cita creada correctamente.";
        } else {
            echo "⚠️ Error al actualizar estado del horario.";
        }
        header("Location: ../vistas/administrador.php?mensaje=cita_agregada");
        exit();
    }
}

// Cancelar cita

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cancelar_cita"])) {
    $id_cita = $_POST["id_cita"];

    $stmt = $conexion->prepare("SELECT dia, hora, tipo_servicio FROM citas WHERE id = ?");
    $stmt->bind_param("i", $id_cita);
    $stmt->execute();
    $stmt->bind_result($dia, $hora, $tipo_servicio);
    $stmt->fetch();
    $stmt->close();

    // Eliminar cita
    $stmt = $conexion->prepare("DELETE FROM citas WHERE id = ?");
    $stmt->bind_param("i", $id_cita);
    if ($stmt->execute()) {
        $estado = 'disponible';
        $sql = ($tipo_servicio === "Manicure y Pedicure")
            ? "UPDATE horarios SET estado = ? WHERE dia = ? AND hora = ?"
            : "UPDATE horarios_corte SET estado = ? WHERE dia = ? AND hora = ?";
        $stmt2 = $conexion->prepare($sql);
        $stmt2->bind_param("sss", $estado, $dia, $hora);
        $stmt2->execute();
        $stmt2->close();

        header("Location: ../vistas/administrador.php?mensaje=cita_cancelada");
        exit();
    } else {
        header("Location: ../vistas/administrador.php?mensaje=error_cancelar");
        exit();
    }
}
?>
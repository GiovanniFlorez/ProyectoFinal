<?php
include ("../modelo/conexion.php");

ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_type = $_POST['form_type'] ?? '';
    $tipo = $_POST['tipo_horario'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $dias = $_POST['dias'] ?? [];

    //------------------ ELIMINAR HORARIO MANICURE ------------------
    if ($form_type === 'eliminar_horario') {
        $id_horario = $_POST['id_horario'];

        $stmt = $conexion->prepare("SELECT hora FROM horarios WHERE id_horario = ?");
        $stmt->bind_param("i", $id_horario);
        $stmt->execute();
        $stmt->bind_result($hora);
        $stmt->fetch();
        $stmt->close();

        if ($hora) {
            $stmt = $conexion->prepare("DELETE FROM horarios WHERE hora = ?");
            $stmt->bind_param("s", $hora);
            $stmt->execute();
            $stmt->close();

            header("Location: ../vistas/administrador.php?mensaje=Horario eliminado.");
            exit;
        } else {
            header("Location: ../vistas/administrador.php?error=Horario no encontrado.");
            exit;
        }
    }

    //------------------ ELIMINAR HORARIO CORTE ------------------
    if ($form_type === 'eliminar_horario_corte') {
        $id_horario = $_POST['id_horario'];

        $stmt = $conexion->prepare("SELECT hora FROM horarios_corte WHERE id_horario = ?");
        $stmt->bind_param("i", $id_horario);
        $stmt->execute();
        $stmt->bind_result($hora);
        $stmt->fetch();
        $stmt->close();

        if ($hora) {
            $stmt = $conexion->prepare("SELECT COUNT(*) FROM horarios_corte WHERE hora = ? AND LOWER(estado) = 'ocupado'");
            $stmt->bind_param("s", $hora);
            $stmt->execute();
            $stmt->bind_result($ocupados);
            $stmt->fetch();
            $stmt->close();

            if ($ocupados > 0) {
                header("Location: ../vistas/administrador.php?mensaje_error=No se puede eliminar un horario de corte con citas agendadas.");
                exit;
            }

            $stmt = $conexion->prepare("DELETE FROM horarios_corte WHERE hora = ?");
            $stmt->bind_param("s", $hora);
            $stmt->execute();
            $stmt->close();

            header("Location: ../vistas/administrador.php?mensaje=Horario de corte eliminado correctamente.");
            exit;
        } else {
            header("Location: ../vistas/administrador.php?mensaje_error=Horario de corte no encontrado.");
            exit;
        }
    }

    //------------------ CREAR HORARIOS ------------------
    if ($form_type === 'crear_horario') {
        if ($tipo === 'corte') {
            foreach ($dias as $dia) {
                try {
                    $stmt = $conexion->prepare("INSERT INTO horarios_corte (hora, dia, estado) VALUES (?, ?, 'disponible')");
                    $stmt->bind_param("ss", $hora, $dia);
                    $stmt->execute();
                    $stmt->close();
                } catch (mysqli_sql_exception $e) {
                    if ($e->getCode() == 1062) {
                        header("Location: ../vistas/administrador.php?mensaje_error=duplicado");
                    } else {
                        header("Location: ../vistas/administrador.php?mensaje_error=otro");
                    }
                    exit;
                }
            }
            header("Location: ../vistas/administrador.php");
            exit;
        } else {
            foreach ($dias as $dia) {
                try {
                    $stmt = $conexion->prepare("INSERT INTO horarios (hora, dia, estado) VALUES (?, ?, 'disponible')");
                    $stmt->bind_param("ss", $hora, $dia);
                    $stmt->execute();
                    $stmt->close();
                } catch (mysqli_sql_exception $e) {
                    if ($e->getCode() == 1062) {
                        header("Location: ../vistas/administrador.php?mensaje_error=duplicado");
                    } else {
                        header("Location: ../vistas/administrador.php?mensaje_error=otro");
                    }
                    exit;
                }
            }
            header("Location: ../vistas/administrador.php");
            exit;
        }
    }
}

//
if ($form_type === 'modificar_horario') {
    $id_horario = $_POST['id_horario'];
    $hora = $_POST['hora'];
    $dias = $_POST['dias'] ?? [];
    $tipo = $_POST['tipo_horario'];

    $tabla = ($tipo === 'corte') ? 'horarios_corte' : 'horarios';

    // Obtener la hora actual (por si se modifica)
    $stmt = $conexion->prepare("SELECT hora FROM $tabla WHERE id_horario = ?");
    $stmt->bind_param("i", $id_horario);
    $stmt->execute();
    $stmt->bind_result($hora_original);
    $stmt->fetch();
    $stmt->close();

    // Si la hora cambia, asegurarse que las citas ocupadas sigan existiendo
    // En este ejemplo, asumo que la hora no cambia, o quieres mantener citas en esa hora

    // 1. Eliminar solo los días disponibles de la hora original
    $stmt = $conexion->prepare("DELETE FROM $tabla WHERE hora = ? AND estado = 'disponible'");
    $stmt->bind_param("s", $hora_original);
    $stmt->execute();
    $stmt->close();

    // 2. Insertar los días disponibles nuevos (los que se seleccionaron)
    foreach ($dias as $dia) {
        $stmt = $conexion->prepare("INSERT INTO $tabla (hora, dia, estado) VALUES (?, ?, 'disponible')");
        $stmt->bind_param("ss", $hora, $dia);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ../vistas/administrador.php?mensaje=Horario modificado correctamente.");
    exit;
}
if ($form_type === 'modificar_horario_corte') {
    $id_horario = $_POST['id_horario'];
    $hora = $_POST['hora'];
    $dias = $_POST['dias'] ?? [];

    // Obtener la hora original
    $stmt = $conexion->prepare("SELECT hora FROM horarios_corte WHERE id_horario = ?");
    $stmt->bind_param("i", $id_horario);
    $stmt->execute();
    $stmt->bind_result($hora_original);
    $stmt->fetch();
    $stmt->close();

    // Eliminar los días no ocupados de esa hora original (no borrar ocupados)
    $stmt = $conexion->prepare("DELETE FROM horarios_corte WHERE hora = ? AND estado != 'ocupado'");
    $stmt->bind_param("s", $hora_original);
    $stmt->execute();
    $stmt->close();

    // Insertar nuevos días seleccionados que no existen
    foreach ($dias as $dia) {
        // Verificar si ya existe registro para esa hora y día
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM horarios_corte WHERE hora = ? AND dia = ?");
        $stmt->bind_param("ss", $hora, $dia);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            $stmt = $conexion->prepare("INSERT INTO horarios_corte (hora, dia, estado) VALUES (?, ?, 'disponible')");
            $stmt->bind_param("ss", $hora, $dia);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: ../vistas/administrador.php?mensaje=Horario de corte modificado correctamente.");
    exit;
}

   // CREAR USUARIO
if ($form_type === 'crear_usuario') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $tipo = $_POST['tipo'];

    

    $insertar = $conexion->prepare("INSERT INTO usuarios (nombre, telefono, tipo) VALUES (?, ?, ?)");
    $insertar->bind_param("sss", $nombre, $telefono, $tipo);

    if ($insertar->execute()) {
        header("Location: ../vistas/administrador.php?mensaje=Usuario creado correctamente.");
        exit;
    } else {
        header("Location: ../vistas/administrador.php?mensaje_error=Error al crear el usuario.");
        exit;
    }
}

// EDITAR USUARIO
if ($form_type === 'editar_usuario') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $tipo = $_POST['tipo'];

    $update = $conexion->prepare("UPDATE usuarios SET nombre = ?, telefono = ?, tipo = ? WHERE id = ?");
    $update->bind_param("sssi", $nombre, $telefono, $tipo, $id);

    if ($update->execute()) {
        header("Location: ../vistas/administrador.php?mensaje=Usuario modificado correctamente.");
        exit;
    } else {
        header("Location: ../vistas/administrador.php?mensaje_error=Error al modificar el usuario.");
        exit;
    }
}

// ELIMINAR USUARIO
if ($form_type === 'eliminar_usuario') {
    $id = $_POST['id'];

    // Paso 1: Obtener el nombre y tipo del usuario
    $stmt = $conexion->prepare("SELECT nombre, tipo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nombre, $tipo);
    $stmt->fetch();
    $stmt->close();

    if (!$nombre || !$tipo) {
        echo "<script>
            alert('Usuario no encontrado.');
            window.location.href = '../vistas/administrador.php';
        </script>";
        exit;
    }

    // Paso 2: Verificar si tiene citas agendadas según el tipo
    if ($tipo === 'cliente') {
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM citas WHERE nombre_cliente = ?");
    } else if ($tipo === 'empleado') {
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM citas WHERE nombre_empleado = ?");
    } else {
        echo "<script>
            alert('Tipo de usuario no válido.');
            window.location.href = '../vistas/administrador.php';
        </script>";
        exit;
    }

    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->bind_result($cantidad_citas);
    $stmt->fetch();
    $stmt->close();

    if ($cantidad_citas > 0) {
        echo "<script>
            alert('No se puede eliminar el usuario porque tiene citas agendadas.');
            window.location.href = '../vistas/administrador.php';
        </script>";
        exit;
    }

    // Paso 3: Eliminar si no tiene citas
    $eliminar = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $eliminar->bind_param("i", $id);

    if ($eliminar->execute()) {
        echo "<script>
            window.location.href = '../vistas/administrador.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al eliminar el usuario.');
            window.location.href = '../vistas/administrador.php';
        </script>";
    }
    exit;
}


?>
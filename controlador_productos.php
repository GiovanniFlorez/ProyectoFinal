<?php

include("../modelo/conexion.php");

$form_type = $_POST['form_type'] ?? '';

// ========== AGREGAR PRODUCTO ==========
if ($form_type === 'agregar_producto') {
    $producto = $_POST['producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $usuarioAdmin= $_POST['usuarioAdmin'];

    $directorio = "../img/";
    $nombreArchivo = basename($_FILES['imagen']['name']);
    $archivoDestino = $directorio . $nombreArchivo;
    $urlImagen = "img/" . $nombreArchivo;

    if (!empty($nombreArchivo) && move_uploaded_file($_FILES['imagen']['tmp_name'], $archivoDestino)) {
        $stmt = $conexion->prepare("INSERT INTO productos (producto, descripcion, precio, imagen, usuarioAdmin) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $producto, $descripcion, $precio, $urlImagen, $usuarioAdmin);
        $stmt->execute();
        $stmt->close();
        header("Location: ../vistas/administrador.php?mensaje=guardado");
        exit();
    } else {
        echo "Error al subir la imagen.";
    }
}

// ========== EDITAR PRODUCTO ==========
if ($form_type === 'editar_producto') {
    $id = $_POST['id'] ?? null;
    $producto = $_POST['producto'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $descripcion = $_POST['descripcion'] ?? '';

    if (!$id) {
        echo "ID no proporcionado";
        exit();
    }

    if (!empty($_FILES['imagen']['name'])) {
        $directorio = "../img/";
        $nombreArchivo = basename($_FILES['imagen']['name']);
        $archivoDestino = $directorio . $nombreArchivo;
        $nuevaImagen = "img/" . $nombreArchivo;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $archivoDestino)) {
            echo "Error al mover la imagen";
            exit();
        }

        $stmt = $conexion->prepare("UPDATE productos SET producto = ?, precio = ?, descripcion = ?, imagen = ? WHERE id = ?");
        $stmt->bind_param("sdssi", $producto, $precio, $descripcion, $nuevaImagen, $id);
    } else {
        $imagenActual = $_POST['imagen_actual'] ?? '';
        $stmt = $conexion->prepare("UPDATE productos SET producto = ?, precio = ?, descripcion = ?, imagen = ? WHERE id = ?");
        $stmt->bind_param("sdssi", $producto, $precio, $descripcion, $imagenActual, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../vistas/administrador.php?mensaje=editado");
        exit();
    } else {
        echo "Error al ejecutar: " . $stmt->error;
        exit();
    }
}

// ========== ELIMINAR PRODUCTO ==========
if ($form_type === 'eliminar_producto') {
    $id = $_POST['id'];

    // Primero obtenemos la imagen para poder eliminarla del servidor
    $stmt = $conexion->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
    $stmt->close();

    if ($producto) {
        $rutaImagen = "../" . $producto['imagen'];

        $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
            header("Location: ../vistas/administrador.php?mensaje=eliminado");
            exit();
        } else {
            echo "Error al eliminar producto: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Producto no encontrado.";
    }
}
?>

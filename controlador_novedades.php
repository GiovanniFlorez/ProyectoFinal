<?php
include("../modelo/conexion.php");

$form_type = $_POST['form_type'] ?? '';

// ========== AGREGAR NOVEDAD ==========
if ($form_type === 'agregar_novedad') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $usuarioAdmin= $_POST['usuarioAdmin'];

    $directorio = "../img/";
    $nombreArchivo = basename($_FILES["imagen"]["name"] ?? '');
    $extensiones_validas = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

    if (!in_array($extension, $extensiones_validas)) {
        die("Formato de imagen no permitido. Usa JPG, PNG o GIF.");
    }

    $nombreArchivoUnico = uniqid() . "_" . $nombreArchivo;
    $archivo_imagen = $directorio . $nombreArchivoUnico;
    $url_guardada = "img/" . $nombreArchivoUnico;

    if (!empty($nombreArchivo) && move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivo_imagen)) {
        $stmt = $conexion->prepare("INSERT INTO novedades (nombre, descripcion, image_url, usuarioAdmin) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $descripcion, $url_guardada, $usuarioAdmin);
        $stmt->execute();
        $stmt->close();
        header("Location: ../vistas/administrador.php?mensaje=novedad_agregada");
        exit();
    } else {
        echo "Error al subir la imagen o imagen no proporcionada.";
    }
}

// ========== EDITAR NOVEDAD ==========
if ($form_type === 'editar_novedad') {
    $id = intval($_POST['id_novedad'] ?? 0);
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (!$id) {
        echo "ID de novedad no proporcionado";
        exit();
    }

    // Si viene nueva imagen
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]['error'] === UPLOAD_ERR_OK) {
        $directorio = "../img/";
        $nombreArchivo = basename($_FILES["imagen"]["name"]);
        $extensiones_validas = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

        if (!in_array($extension, $extensiones_validas)) {
            die("Formato de imagen no permitido. Usa JPG, PNG o GIF.");
        }

        $nombreArchivoUnico = uniqid() . "_" . $nombreArchivo;
        $archivo_imagen = $directorio . $nombreArchivoUnico;
        $nueva_imagen = "img/" . $nombreArchivoUnico;

        // Eliminar imagen anterior
        $stmt = $conexion->prepare("SELECT image_url FROM novedades WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $anterior = $resultado->fetch_assoc();
        $stmt->close();

        if ($anterior) {
            $ruta_anterior = "../" . $anterior['image_url'];
            if (file_exists($ruta_anterior)) {
                unlink($ruta_anterior);
            }
        }

        // Subir nueva imagen
        if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivo_imagen)) {
            die("Error al subir la nueva imagen.");
        }

        $stmt = $conexion->prepare("UPDATE novedades SET nombre = ?, descripcion = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $descripcion, $nueva_imagen, $id);
    } else {
        // Sin imagen nueva
        $stmt = $conexion->prepare("UPDATE novedades SET nombre = ?, descripcion = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../vistas/administrador.php?mensaje=novedad_editada");
        exit();
    } else {
        echo "Error al ejecutar: " . $stmt->error;
        exit();
    }
}

// ========== ELIMINAR NOVEDAD ==========
if ($form_type === 'eliminar_novedad') {
    $id = intval($_POST['id_novedad'] ?? 0);

    $stmt = $conexion->prepare("SELECT image_url FROM novedades WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $novedad = $resultado->fetch_assoc();
    $stmt->close();

    if ($novedad) {
        $rutaImagen = "../" . $novedad['image_url'];

        $stmt = $conexion->prepare("DELETE FROM novedades WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
            header("Location: ../vistas/administrador.php?mensaje=novedad_eliminada");
            exit();
        } else {
            echo "Error al eliminar novedad: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Novedad no encontrada.";
    }
}
?>

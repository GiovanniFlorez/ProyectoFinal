<?php

$mysqli = new mysqli("localhost", "root", "", "proyectofinal", "3306");
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Obtener la imagen para borrarla del sistema de archivos
    $stmt = $mysqli->prepare("SELECT image_url FROM novedades WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $novedad = $result->fetch_assoc();
    $stmt->close();

    if ($novedad) {
        $filepath = "../" . $novedad['image_url'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Eliminar la fila
        $stmt = $mysqli->prepare("DELETE FROM novedades WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

$mysqli->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>

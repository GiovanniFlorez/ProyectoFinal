<?php
$mysqli = new mysqli("localhost", "root", "", "proyectofinal", "3306");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['image'])) {
    $image_url = $_POST['image'];
    $filepath = "../" . $image_url;

    // Borra el archivo físico
    if (file_exists($filepath)) {
        unlink($filepath);
    }

    // Borra el registro en la base de datos
    $stmt = $mysqli->prepare("DELETE FROM carousel_images WHERE image_url = ?");
    $stmt->bind_param("s", $image_url);
    $stmt->execute();
    $stmt->close();
}

$mysqli->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>

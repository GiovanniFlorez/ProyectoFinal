<?php
session_start(); // ¡IMPORTANTE!
$mysqli = new mysqli("localhost", "root", "", "proyectofinal", "3306");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

$usuarioAdmin = $_SESSION['usuario'] ?? null;

if (!$usuarioAdmin) {
    die("Error: no se detectó usuario administrador en la sesión.");
}

$target_dir = "../img/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);
$image_url = "img/" . basename($_FILES["image"]["name"]);

if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    $stmt = $mysqli->prepare("INSERT INTO carousel_images (image_url, usuarioAdmin) VALUES (?, ?)");
    $stmt->bind_param("ss", $image_url, $usuarioAdmin);
    $stmt->execute();
    $stmt->close();
    echo "Imagen subida exitosamente.";
} else {
    echo "Error al subir la imagen.";
}

$mysqli->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>

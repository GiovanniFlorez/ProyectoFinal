<?php 
$mysqli = new mysqli("localhost", "root", "", "proyectofinal", "3306");
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM carousel_images ORDER BY created_at DESC");

$first = true;

while ($row = $result->fetch_assoc()) {
    $class = $first ? 'carousel-img active' : 'carousel-img';
    $imgSrc = 'http://localhost/proyectofinal/' . htmlspecialchars($row['image_url']) . '?t=' . time(); // evita caché
    $filename = htmlspecialchars($row['image_url']);
    
    echo '<img src="' . $imgSrc . '" alt="Imagen" class="' . $class . '" data-filename="' . $filename . '">';
    $first = false;
}

$mysqli->close();
?>


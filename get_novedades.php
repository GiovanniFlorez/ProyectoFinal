<?php
include("../modelo/conexion.php");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta que une productos con administrador
$query = "
    SELECT p.*, a.usuario AS nombre_admin
    FROM productos p
    JOIN administrador a ON p.usuarioAdmin = a.usuario
";

$resultado = $conexion->query($query);
if ($resultado && $resultado->num_rows > 0){
    while ($row = $resultado->fetch_assoc()) {
    echo '<div class="novedad-card" data-id="' . $row['id'] . '">';
    echo '  <img src="../' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['nombre']) . '">';
    echo '  <h3 class="nombre">' . htmlspecialchars($row['nombre']) . '</h3>';
    echo '  <p class="descripcion">' . htmlspecialchars($row['descripcion']) . '</p>';
            echo '<p class="admin">Creado por: ' . htmlspecialchars($row['nombre_admin']) . '</p>';
    echo '</div>';
    }
}else{
    echo "<p>No hay novedades disponibles.</p>";
}


?>

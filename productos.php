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

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        echo '<div class="producto-card" data-id="' . htmlspecialchars($row['id']) . '">';
        echo '<img src="../' . htmlspecialchars($row['imagen']) . '" alt="' . htmlspecialchars($row['producto']) . '">';
        echo '<h3 class="nombre">' . htmlspecialchars($row['producto']) . '</h3>';
        echo '<p class="descripcion">' . htmlspecialchars($row['descripcion']) . '</p>';
        echo '<p class="precio">$' . number_format($row['precio']) . '</p>';
        echo '<p class="admin">Creado por: ' . htmlspecialchars($row['nombre_admin']) . '</p>';
        echo '</div>';
    }
} else {
    echo "<p>No hay productos disponibles.</p>";
}

?>

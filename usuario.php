<?php
session_start();

include "../controlador/controlador_login.php";


?>

<?php
include_once '../modelo/consultas_horarios.php';  // Asegúrate que la ruta es correcta
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Jazkel Peluquería</title>
  <link rel="stylesheet" href="../css/usuario.css">
  
</head>

<?php include 'headerUsuario.php'; ?>

<body>
<main>
<section id="inicio" class="carousel-container section active">
  <div class="carousel">
    <button class="carousel-btn prev">&#10094;</button>
    <div class="carousel-images" id="carousel-images">
      <?php include('get_images.php'); ?>
    </div>
    <button class="carousel-btn next">&#10095;</button>
  </div>
  
  <div class="blablabla">
    

  </div>
</section>




<div class="pantalla-completa">
 <div class="container">
 <section id="productos" class="section"> 
  <h2>Productos</h2>

  <!-- Barra de búsqueda -->
  <input type="text" id="busquedaProductos" placeholder="Buscar productos por nombre..." style="margin-bottom: 10px; padding: 5px; width: 100%; max-width: 400px; height: 40px;">
          <div style=" overflow-y: auto; padding: 10px;">
            <div class="productos-grid">
              <?php
              while ($producto = $productosquery->fetch_object()) {
                echo '<div class="producto-card" 
                   data-id="' . htmlspecialchars($producto->id) . '" 
                   data-producto="' . htmlspecialchars($producto->producto) . '" 
                   data-precio="' . htmlspecialchars($producto->precio) . '" 
                   data-descripcion="' . htmlspecialchars($producto->descripcion) . '"
                   data-imagen="' . htmlspecialchars($producto->imagen) . '">';






                echo '<img src="../' . htmlspecialchars($producto->imagen) . '" alt="' . htmlspecialchars($producto->producto) . '">';
                echo '<p><strong>ID:</strong> ' . htmlspecialchars($producto->id) . '</p>';
                echo '<h3>' . htmlspecialchars($producto->producto) . '</h3>';
                echo '<p><strong></strong> ' . htmlspecialchars($producto->descripcion) . '</p>';
                echo '<p><strong>Precio:</strong> $' . number_format($producto->precio, 1) . '</p>';
                echo '</div>';
              }
              ?>
            </div>
</section>

<!-- Sección Horario Manicure -->
<section id="horarios" class="section">
  <h2>Horario para Manicure y Pedicure</h2>
  <?php include 'tabla_horarios.php'; mostrarTabla($horarios_manicure, 'manicure'); ?>
</section>

<!-- Sección Horario Corte -->
<section id="horarios-corte" class="section">
  <h2>Horario para Corte de Cabello</h2>
  <?php mostrarTabla($horarios_corte, 'corte'); ?>
</section>


<section id="novedades" class="section">
  <h2>Novedades</h2>

 <!-- Barra de búsqueda -->
  <input type="text" id="busquedaNovedades" placeholder="Buscar novedades por nombre..." style="margin-bottom: 10px; padding: 5px; width: 100%; max-width: 400px; height: 40px;">


  <!-- Contenedor con scroll -->
  <div style="overflow-y: auto; padding: 10px;">
    <!-- Grid de novedades -->
    <div class="productos-grid">
      <?php
      include("../modelo/conexion.php");
      $query = "SELECT * FROM novedades";
      $resultado = $conexion->query($query);

      while ($row = $resultado->fetch_assoc()) {
        echo '<div class="novedad-card"
              data-id="' . $row['id'] . '"
              data-nombre="' . htmlspecialchars($row['nombre']) . '"
              data-descripcion="' . htmlspecialchars($row['descripcion']) . '"">';

        echo '<img src="../' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['nombre']) . '">';
        echo '<h3 class="nombre">' . htmlspecialchars($row['nombre']) . '</h3>';
        echo '<p class="descripcion">' . htmlspecialchars($row['descripcion']) . '</p>';
        echo '</div>';
      }
      ?>
    </div>
  </div>
</section>

  </div>
</div>
</main>
  <?php include 'footer.php'; ?>

  <script>
    document.getElementById("busquedaProductos").addEventListener("input", function() {
    var filtro = this.value.toLowerCase().trim();
    var productos = document.querySelectorAll(".producto-card");

    productos.forEach(function(producto) {
        var nombre = producto.getAttribute("data-producto").toLowerCase().trim();
        if (nombre.includes(filtro) || filtro === "") {
            producto.style.display = "";
        } else {
            producto.style.display = "none";
        }
    });
});
document.getElementById("busquedaNovedades").addEventListener("input", function() {
        var filtro = this.value.toLowerCase().trim();
        var novedades = document.querySelectorAll(".novedad-card");

        novedades.forEach(function(novedad) {
          var nombre = novedad.getAttribute("data-nombre").toLowerCase().trim();
          if (nombre.includes(filtro) || filtro === "") {
            novedad.style.display = "";
          } else {
            novedad.style.display = "none";
          }
    });
});
  </script>

</body>

 
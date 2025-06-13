<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/headerUsuario.css">
</head>

<header class="headers">
    <div class="imagen">
      <img src="../img/images.jpg" alt="Ir al inicio" style="cursor: pointer;" onclick="mostrarSeccion('inicio')">

    </div>
        <nav class="nav">
      <a href="#" onclick="mostrarSeccion('productos')">Productos</a>
      <a href="#" class="active" onclick="mostrarSeccion('horarios')">Horario Manicure</a>
      <a href="#" onclick="mostrarSeccion('horarios-corte')">Horarios Cortes</a>
      <a href="#" onclick="mostrarSeccion('novedades')">Novedades</a>
      <a id="Iniciar" href="login.php" class="login-button">Iniciar Sesión</a>
    </nav>
</header>


<script src="../js/app.js" defer></script>
</html>
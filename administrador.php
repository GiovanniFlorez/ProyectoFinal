<?php
session_start();

include "../controlador/controlador_login.php";
include "../controlador/controlador_crud.php";
include "../modelo/conexion.php";
$form_type = $_POST['form_type'] ?? '';
$usuarioAdmin = $_SESSION['usuario'] ?? null;
?>
<?php
include_once '../modelo/consultas_horarios.php';  // Asegúrate que la ruta es correcta
?>

<!-- original -->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>Jazkel Peluquería</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="/proyectofinal/css/admin.css">

</head>
<header>
  <div class="imagen">
    <img src="../img/images.jpg" alt="Ir al inicio" style="cursor: pointer;"   onclick="mostrarSeccion('inicio')">
  </div>
  <div class="logo" alt="Ir al inicio" style="cursor: pointer;" onclick="mostrarSeccion('inicio')">PELUQUERÍA JAZKEL</div>
  <nav class="nav">
    <a href="#" onclick="mostrarSeccion('productos')">Productos</a>
    <a href="#" onclick="mostrarSeccion('horarios')">Horario Manicure</a>
    <a href="#" onclick="mostrarSeccion('horarios-corte')">Horarios Cortes</a>
    <a href="#" onclick="mostrarSeccion('novedades')">novedades</a>
    <a href="#" onclick="mostrarSeccion('citas')">Citas</a>
    <a href="#" onclick="mostrarSeccion('usuarios')">Usuarios</a>
    <button id="cerrarSesion" class="login-button">Cerrar Sesión</button>
  </nav>
</header>

<body>
<?php
if (isset($_GET['mensaje_error'])) {
    $mensaje = $_GET['mensaje_error'];
    if ($mensaje == 'duplicado_corte') {
        echo "<script>alert('Este horario de corte ya existe.');</script>";
    } elseif ($mensaje == 'duplicado_unas') {
        echo "<script>alert('Este horario de uñas ya existe.');</script>";
    } elseif ($mensaje == 'otro') {
        echo "<script>alert('Ocurrió un error inesperado.');</script>";
    }
}
?>


  <div id="contenedor-vista"></div>

  <!----------------------------------------------------------------------------------------------------------------------------------------------->
  <section id="inicio" class="carousel-container section active">
    <div class="encabezado">
      <div class="logo">JK</div>
      <h1>JAZKEL</h1>
      <h2>PELUQUERÍA</h2>
    </div>

    <div class="carousel">
      <button class="carousel-btn prev">&#10094;</button>
      <div class="carousel-images" id="carousel-images">
        <?php include('get_images.php'); ?>
      </div>
      <button class="carousel-btn next">&#10095;</button>
    </div>

    <div class="cargarcarousel">
      <form id="modificacionCarrusel" action="upload_image.php" method="POST"  enctype="multipart/form-data" >
        <input type="file" name="image"  required>
        <button type="submit">Subir imagen</button>
      </form>

      <form id="delete-form" method="POST" action="delete_image.php" style="display: none;">
        <input type="hidden" name="usuarioAdmin" value="<?php echo $_SESSION['usuario']; ?>" require>
        <input type="hidden" name="image" id="delete-image-name">
        <button type="submit">Eliminar esta imagen</button>
      </form>


    </div>
  </section>

  <main>
    <div class="pantalla-completa">
      <div class="container">

        <!-- ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->

        <section id="productos" class="section">
          <h2>Productos</h2>

            <!-- Botones -->
            <div class="controls">
              <button id="btn-crear">Crear</button>
              <button id="boton-modificar">Modificar</button>
              <button id="boton-eliminar">Eliminar</button>
            </div>

          <!-- Barra de búsqueda -->
          <input type="text" id="busquedaProductos" placeholder="Buscar productos por nombre..." style="margin-bottom: 10px; padding: 5px; width: 100%; max-width: 400px; height: 40px;">

          <!-- Contenedor con scroll -->
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
          </div>
        </section>

        <!-- Formulario para guardar productos -->
        <div id="form-crear-producto" style="display: none;" class="form-crear-container">
          <h3>Agregar nuevo producto</h3>
          <form action="../controlador/controlador_productos.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="agregar_producto">

            <label>Producto:</label>
            <input type="text" name="producto" required>

            <label>Precio:</label>
            <input type="number" name="precio" min="0" step="100"   required>

            <label>Descripción:</label>
            <input type="text" name="descripcion" required>

            <input type="hidden" name="usuarioAdmin" value="<?php echo $_SESSION['usuario']; ?>" require>

            <label for="crear-imagen-producto">Imagen:</label>
            <input type="file" id="crear-imagen-producto" name="imagen" accept="image/*" onchange="previsualizarImagenProducto(event)">
            <img id="vista-previa-producto-creacion" src="" alt="Vista previa producto" style="max-width: 200px; max-height: 200px; display: none;">

            <button type="submit" class="btn">Guardar</button>
            <button class="btn_cancelar" type="button" onclick="cerrarFormularioCreacionProducto()">Cancelar</button>
          </form>
        </div>


        <!-- Formulario de edición productos -->
        <div id="tabla-modificacion-producto" style="display: none;">
          <h3>Editar producto</h3>
          <form id="form-editar-producto" action="../controlador/controlador_productos.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="imagen_actual" id="imagen-actual">
            <input type="hidden" name="form_type" value="editar_producto">
            <input type="hidden" name="id" id="edit-id">

            <label>Producto:</label>
            <input type="text" name="producto" id="edit-producto" required>

            <label>Precio:</label>
            <input type="number" step="100" min="0" name="precio" id="edit-precio" required>

            <label>Descripción:</label>
            <input type="text" name="descripcion" id="edit-descripcion" required>

            <label for="edit-imagen-producto">Imagen:</label>
            <input type="file" id="edit-imagen-producto" name="imagen" accept="image/*" onchange="previsualizarImagenProducto(event)">
            <img id="vista-previa-producto-edicion" src="" alt="Vista previa nueva producto" style="max-width: 150px; max-height: 200px; display: none;">
            <img id="edit-imagen-preview" src="" alt="Vista previa producto" style="max-width: 150px; max-height: 200px; display: none;">

            <button type="submit" class="btn">Guardar cambios</button>
            <button type="button" class="btn_cancelar" onclick="cerrarFormularioEdicionProducto()">Cancelar</button>
          </form>
        </div>


        <!-- Eliminar productos -->
        <form action="../controlador/controlador_productos.php" method="POST" id="etiqueta-eliminar" style="display: none;">
          <input type="hidden" name="form_type" value="eliminar_producto">
          <input type="hidden" name="id" id="eliminar-id">
        </form>



        <!-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

        <section id="novedades" class="section">
          <h2>Novedades</h2>

           <div id="contenedor-novedades">

                                    <!-- Botones CRUD -->
            <div class="controls">
              <button id="btn-crear-novedad">Crear</button>
              <button id="boton-modificar-novedad">Modificar</button>
              <button id="boton-eliminar-novedad">Eliminar</button>
            </div>


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
              data-descripcion="' . htmlspecialchars($row['descripcion']) .'">';

                echo '<img src="../' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['nombre']) . '">';
                echo '<h3 class="nombre">' . htmlspecialchars($row['nombre']) . '</h3>';
                echo '<p class="descripcion">' . htmlspecialchars($row['descripcion']) . '</p>';
                echo '</div>';
              }
              ?>
            </div>
          </div>
        </section>

        <!-- FORMULARIO DE CREACIÓN -->
        <div id="form-crear-novedades" class="form-crear-container" style="display: none;">
          <form action="../controlador/controlador_novedades.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="agregar_novedad">

            <label for="crear-nombre">Nombre:</label>
            <input type="text" id="crear-nombre" name="nombre" required>
            <label for="crear-descripcion">Descripción:</label>
            <input type="text" id="crear-descripcion" name="descripcion" required>

            <input type="hidden" name="usuarioAdmin" value="<?php echo $_SESSION['usuario']; ?>" require>

            <label for="crear-imagen">Imagen:</label>
            <input type="file" id="crear-imagen" name="imagen" accept="image/*" onchange="previsualizarImagenNovedad(event)" required>

            <img id="vista-previa-novedades" src="" alt="Vista previa novedad" style="max-width: 150px; max-height: 200px; display: none;">


            <button id="btn-crear-novedad" class="btn">Crear novedad</button>
            <button type="button" class="btn_cancelar" onclick="cerrarFormularioCreacion()">Cancelar</button>
          </form>
        </div>

        <!-- FORMULARIO DE EDICIÓN -->
        <div id="tabla-modificacion-novedad" style="display: none;">
          <h3>Editar novedad</h3>
          <form id="form-editar-novedad" action="../controlador/controlador_novedades.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="editar_novedad">
            <input type="hidden" name="id_novedad" id="edit-id-novedad">

            <label for="edit-nombre">Nombre:</label>
            <input type="text" name="nombre" id="edit-nombre">

            <label for="edit-descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="edit-descripcion-novedad">

            <label for="edit-imagen">Imagen:</label>
            <input type="file" name="imagen" id="edit-imagen-novedad" accept="image/*" onchange="previsualizarImagenNovedad(event)">
            <img id="edit-imagen-preview-novedad" src="" alt="Imagen actual" style="max-width: 150px; max-height: 200px;; display: none;">
            <img id="vista-previa-novedad-edicion" src="" alt="Vista previa de la imagen novedad" style="max-width: 150px; max-height: 200px; display: none;">


            <button type="submit" class="btn">Guardar cambios</button>
            <button type="button" class="btn_cancelar" onclick="cerrarFormularioEdicion()">Cancelar</button>
          </form>
        </div>


        <!-- FORMULARIO DE ELIMINACIÓN -->
        <form action="../controlador/controlador_novedades.php" method="POST" id="form-eliminar-novedad" style="display: none;">
          <input type="hidden" name="form_type" value="eliminar_novedad">
          <input type="hidden" name="id_novedad" id="eliminar-id-novedad">
        </form>
      </div>
    </div>

    <!-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

    <!---------------------------------------------------------------------------------------------------Horarios----------------------------------------------------------------------------->

    <!-- Sección Horario Manicure -->
    <section id="horarios" class="section"">
      <h2>Horario para Manicure y Pedicure</h2>
      <div class="controls">
        <button id="btn-nuevo-horario">Nuevo Horario</button>
        <button id="btn-eliminar">Eliminar</button>
      </div>


      <div class="scroll-container">
        <?php include 'tabla_horarios.php';
        mostrarTabla($horarios_manicure, 'manicure'); ?>

      </div>


    </section>

    <!-- Sección Horario Corte -->
    <section id="horarios-corte" class="section">
      <h2>Horario para Corte de Cabello</h2>
      <div class="controls">
        <button id="btn-nuevo-horario-corte">Nuevo Horario</button>
        <button id="btn-eliminar-corte">Eliminar</button>
      </div>

      <!-- Contenedor con scroll -->
      <div style="max-height: 400px; overflow-y: auto; padding: 10px;">
        <?php mostrarTabla($horarios_corte, 'corte'); ?>
      </div>
    </section>
    <!-- Formularios -->
    <?php include 'formularios_horarios.php'; ?>

    <?php include_once '../modelo/consultas_citas.php'; ?>
  </main>
<!-- SECCIÓN DE CITAS -->
<section id="citas" class="section">
  <h1>Horario Semanal de Servicios</h1>
  <button id="btn-crear-cita">Crear Cita</button>

        <!-- Barra de búsqueda -->
  <input type="text" id="busquedaCitas" placeholder="Buscar por cliente, empleado, servicio o día..." style="margin-bottom: 10px; padding: 5px; width: 100%; max-width: 400px; height: 40px;">

  <div id="form-crear-citas" class="form-crear-container" style="display: none;">
    <form action="../controlador/controlador_citas.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="form_type" value="agregar_cita">
      <h3>Crear nueva cita</h3>

      <label>Tipo de servicio:
        <select name="tabla" id="tipo_servicio" onchange="mostrarHorarios()" required>
          <option value="horarios_manicure">Manicure</option>
          <option value="horarios_corte">Corte</option>
        </select>
      </label>

      <div id="horarios_manicure_div">
        <label>Día y Hora:
          <select onchange="extraerHora(this)">
            <?php foreach ($horarios_manicure as $row): ?>
              <option value="<?= $row['dia'] . '|' . $row['hora'] ?>"><?= $row['dia'] ?> - <?= $row['hora'] ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>

      <div id="horarios_corte_div" style="display: none;">
        <label>Día y Hora:
          <select onchange="extraerHora(this)">
            <?php foreach ($horarios_corte as $row): ?>
              <option value="<?= $row['dia'] . '|' . $row['hora'] ?>"><?= $row['dia'] ?> - <?= $row['hora'] ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>

      <input type="hidden" name="hora" id="hora">
      <input type="hidden" name="dia" id="dia">

      <label>Cliente:
        <select name="nombre_cliente" required>
          <?php foreach ($clientes as $cliente): ?>
            <option value="<?= $cliente['nombre'] ?>"><?= $cliente['nombre'] ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Empleado:
        <select name="nombre_empleado" required>
          <?php foreach ($empleados as $empleado): ?>
            <option value="<?= $empleado['nombre'] ?>"><?= $empleado['nombre'] ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Descripción (opcional):
        <textarea name="descripcion" rows="3" cols="30" placeholder="Ej: Cliente prefiere corte estilo fade..."></textarea>
      </label>

      <button type="submit" name="crear_cita">Crear Cita</button>
      <button type="button" class="btn_cancelar" onclick="cerrarFormularioCreacionCitas()">Cancelar</button>
    </form>
  </div>

  <h2>Citas agendadas</h2>
 <table id="tablaCitas">
    <thead>
      <tr>
        <th>Cliente</th>
        <th>Empleado</th>
        <th>Servicio</th>
        <th>Día</th>
        <th>Hora</th>
        <th>Descripción</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $citas = $conexion->query("SELECT * FROM citas ORDER BY dia, hora");
      while ($cita = $citas->fetch_assoc()) {
        echo "<tr>
                <td>{$cita['nombre_cliente']}</td>
                <td>{$cita['nombre_empleado']}</td>
                <td>{$cita['tipo_servicio']}</td>
                <td>{$cita['dia']}</td>
                <td>{$cita['hora']}</td>
                <td>{$cita['descripcion']}</td>
                <td>
                    <form method='POST' action='../controlador/controlador_citas.php' style='display:inline'>
                      <input type='hidden' name='id_cita' value='{$cita['id']}'>
                      <button type='submit' name='cancelar_cita' onclick=\"return confirm('¿Cancelar esta cita?')\">Cancelar</button>
                    </form>
                </td>
              </tr>";
      }
      ?>
    </tbody>
  </table>
</section>

    <!-- SECCIÓN DE USUARIOS -->
    <section id="usuarios" class="section" style="display: none;">
      <h2>Usuarios</h2>

      <div class="controls">
        <button id="btn-crear-usuario">Crear</button>
        <button id="btn-modificar-usuario">Modificar</button>
        <button id="btn-eliminar-usuario">Eliminar</button>
      </div>

      <!-- Barra de búsqueda -->
      <input type="text" id="busquedaUsuarios" placeholder="Buscar por nombre, teléfono o tipo..." style="margin-bottom: 10px; padding: 5px; width: 100%; max-width: 400px; height: 40px;">

      <!-- Tabla con scroll -->
      <div style="max-height: 300px; overflow-y: auto;">
        <table class="tabla-usuarios" style="width: 100%; border-collapse: collapse;" id="tablaUsuarios">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Tipo</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = "SELECT * FROM usuarios";
            $resultado = $conexion->query($query);

            while ($row = $resultado->fetch_assoc()) {
              echo '<tr class="fila-usuario" 
                    data-id="' . $row['id'] . '"
                    data-nombre="' . htmlspecialchars($row['nombre']) . '"
                    data-telefono="' . htmlspecialchars($row['telefono']) . '"
                    data-tipo="' . htmlspecialchars($row['tipo']) . '">';

              echo '<td>' . $row['id'] . '</td>';
              echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
              echo '<td>' . htmlspecialchars($row['telefono']) . '</td>';
              echo '<td>' . htmlspecialchars($row['tipo']) . '</td>';
              echo '</tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </section>
    <!-- Formulario nuevo usuario -->
    <div id="tabla-nuevo-usuario" style="display: none;">
      <h3>Crear nuevo usuario</h3>
      <form action="../controlador/controlador_crud.php" method="POST" id="nuevo-usuario">
        <input type="hidden" name="form_type" value="crear_usuario">

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono"><br>

        <label for="tipo">Tipo:</label>
        <select name="tipo" required>
          <option value="">-- Selecciona --</option>
          <option value="cliente">Cliente</option>
          <option value="empleado">Empleado</option>
        </select><br>

        <button type="submit" class="btn">Crear</button>
        <button type="button" class="btn_cancelar" onclick="formularioCancelarNuevoUsuario()">Cancelar</button>
      </form>
    </div>

    <!-- Formulario editar usuario -->
    <div id="tabla-editar-usuario" style="display: none;">
      <h3>Editar Usuario</h3>
      <form action="../controlador/controlador_crud.php" method="POST" id="editar-usuario-form">
        <input type="hidden" name="form_type" value="editar_usuario">
        <input type="hidden" name="id" id="editar-id">

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="editar-nombre"><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="editar-telefono"><br>

        <label for="tipo">Tipo:</label>
        <select name="tipo" id="editar-tipo">
          <option value="cliente">Cliente</option>
          <option value="empleado">Empleado</option>
        </select><br>

        <button type="submit" class="btn">Guardar cambios</button>
        <button type="button" class="btn_cancelar" onclick="formularioCancelarEditarUsuario()">Cancelar</button>
      </form>
    </div>

    <!-- Formulario eliminar usuario -->
    <form action="../controlador/controlador_crud.php" method="POST" id="form-eliminar-usuario" style="display: none;">
      <input type="hidden" name="form_type" value="eliminar_usuario">
      <input type="hidden" name="id" id="eliminar-id-usuario">
    </form>




    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/proyectofinal/js/app.js"></script>
</body>


</html>
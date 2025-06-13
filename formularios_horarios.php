<div id="formulario-nuevo-manicure" style="display: none;">
  <form action="../controlador/controlador_crud.php" method="POST">

    <input type="hidden" name="form_type" value="crear_horario">
    <input type="hidden" name="tipo_horario" value="manicure">

    <label for="hora">Hora:</label>
    <input type="time" name="hora" required><br>

    <p>Días disponibles:</p>
    <?php
    $dias = ["lunes", "martes", "miércoles", "jueves", "viernes", "sábado"];
    foreach ($dias as $dia) {
      echo "<label><input type='checkbox' name='dias[]' value='$dia'> ".ucfirst($dia)."</label><br>";
    }
    ?>

    <button type="submit" class="btn">Crear horario</button>
    <button type="button" class="btn_cancelar" onclick="formularioCancelarNuevoHorario('manicure')">Cancelar</button>
  </form>
</div>
<div id="formulario-nuevo-corte" style="display: none;">
  <form action="../controlador/controlador_crud.php" method="POST">

    <input type="hidden" name="form_type" value="crear_horario">
    <input type="hidden" name="tipo_horario" value="corte">

    <label for="hora">Hora:</label>
    <input type="time" name="hora" required><br>

    <p>Días disponibles:</p>
    <?php
    foreach ($dias as $dia) {
      echo "<label><input type='checkbox' name='dias[]' value='$dia'> ".ucfirst($dia)."</label><br>";
    }
    ?>

    <button type="submit" class="btn">Crear horario</button>
    <button type="button" class="btn_cancelar" onclick="formularioCancelarNuevoHorario('corte')">Cancelar</button>
  </form>
</div>


<!-- FORMULARIO PARA ELIMINAR HORARIO CORTE -->
<form id="form-eliminar-horario-corte" action="../controlador/controlador_crud.php" method="POST" style="display: none;">
  <input type="hidden" name="form_type" value="eliminar_horario_corte">
  <input type="hidden" name="id_horario" id="input-id-horario-corte">
</form>

<!-- FORMULARIO PARA EDITAR HORARIO CORTE -->
<div id="formulario-editar-horario-corte" style="display: none;">
  <form action="../controlador/controlador_crud.php" method="POST">
    <input type="hidden" name="form_type" value="modificar_horario_corte">
    <input type="hidden" name="id_horario" id="editar-id-horario-corte">

    <label for="editar-hora-corte">Hora:</label>
    <input type="time" name="hora" id="editar-hora-corte" readonly><br>

    <p>Días disponibles:</p>
    <?php
    foreach (["lunes", "martes", "miércoles", "jueves", "viernes", "sábado"] as $dia) {
        echo "<label><input type='checkbox' name='dias[]' value='$dia' id='editar-corte-$dia'> ".ucfirst($dia)."</label><br>";
    }
    ?>

    <button type="submit" class="btn">Guardar Cambios</button>
    <button type="button" class="btn_cancelar" onclick="formularioCancelarEditarHorarioCorte()">Cancelar</button>
  </form>
</div>



<!-- FORMULARIO PARA ELIMINAR HORARIO MANICURE -->
<form id="form-eliminar-horario" action="../controlador/controlador_crud.php" method="POST" style="display: none;">
  <input type="hidden" name="form_type" value="eliminar_horario">
  <input type="hidden" name="id_horario" id="input-id-horario">
</form>

<div id="formulario-editar-horario" style="display: none;">
  <form action="../controlador/controlador_crud.php" method="POST">
    <input type="hidden" name="form_type" value="modificar_horario">
    <input type="hidden" name="id_horario" id="editar-id-horario">

    <label for="editar-hora">Hora:</label>
    <input type="time" name="hora" id="editar-hora" readonly><br>

    <p>Días disponibles:</p>
    <?php
    foreach (["lunes", "martes", "miércoles", "jueves", "viernes", "sábado"] as $dia) {
        echo "<label><input type='checkbox' name='dias[]' value='$dia' id='editar-$dia'> ".ucfirst($dia)."</label><br>";
    }
    ?>

    <button type="submit" class="btn">Guardar Cambios</button>
    <button type="button" class="btn_cancelar" onclick="formularioCancelarEditarHorario()">Cancelar</button>
  </form>
</div>
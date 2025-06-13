<?php
function mostrarTabla($horarios, $tipo) {
  echo '<table>
    <thead>
      <tr>
        <th>Hora</th>
        <th>Lunes</th>
        <th>Martes</th>
        <th>Miércoles</th>
        <th>Jueves</th>
        <th>Viernes</th>
        <th>Sábado</th>
      </tr>
    </thead>
    <tbody>';
  
  foreach ($horarios as $hora => $dias) {
    echo "<tr data-id='{$dias['id']}' data-tipo='{$tipo}'>
      <td>$hora</td>
      <td>{$dias['lunes']}</td>
      <td>{$dias['martes']}</td>
      <td>{$dias['miércoles']}</td>
      <td>{$dias['jueves']}</td>
      <td>{$dias['viernes']}</td>
      <td>{$dias['sábado']}</td>
    </tr>";
  }

  echo '</tbody></table>';
}
?>

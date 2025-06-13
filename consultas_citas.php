<?php
// Asegúrate de tener $conexion ya definido antes de incluir este archivo

$clientes_resultado = $conexion->query("SELECT nombre FROM usuarios WHERE tipo = 'cliente'");
$empleados_resultado = $conexion->query("SELECT nombre FROM usuarios WHERE tipo = 'empleado'");
$horarios_manicure_data = $conexion->query("SELECT DISTINCT dia, hora FROM horarios WHERE estado = 'disponible'");
$horarios_corte_data = $conexion->query("SELECT DISTINCT dia, hora FROM horarios_corte WHERE estado = 'disponible'");

// Convertir resultados a arrays para usarlos más de una vez
$horarios_manicure = [];
while ($row = $horarios_manicure_data->fetch_assoc()) $horarios_manicure[] = $row;

$horarios_corte = [];
while ($row = $horarios_corte_data->fetch_assoc()) $horarios_corte[] = $row;

$clientes = [];
while ($row = $clientes_resultado->fetch_assoc()) $clientes[] = $row;

$empleados = [];
while ($row = $empleados_resultado->fetch_assoc()) $empleados[] = $row;
?>

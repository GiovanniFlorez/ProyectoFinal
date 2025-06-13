<?php
$mysqli = new mysqli("localhost", "root", "", "proyectofinal", "3306");
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

function obtenerHorarios($tabla) {
    global $mysqli;
    $query = $mysqli->query("SELECT id_horario, hora, dia, estado FROM $tabla ORDER BY hora, FIELD(dia, 'lunes','martes','miércoles','jueves','viernes','sábado')");
    $horarios = [];

    while ($h = $query->fetch_object()) {
        $hora = $h->hora;
        $dia = strtolower($h->dia);
        $estado = $h->estado;
        $id = $h->id_horario;

        if (!isset($horarios[$hora])) {
            $horarios[$hora] = [
                'id' => $id,
                'lunes' => '', 'martes' => '', 'miércoles' => '', 'jueves' => '', 'viernes' => '', 'sábado' => ''
            ];
        }
        $horarios[$hora][$dia] = $estado;
    }

    return $horarios;
}

$horarios_manicure = obtenerHorarios("horarios");
$horarios_corte = obtenerHorarios("horarios_corte");
?>

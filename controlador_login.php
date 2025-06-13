<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['cerrado'])): ?>
  <div class="mensaje-cierre-sesion">
    <p>✨ Has cerrado sesión exitosamente. ¡Gracias por visitarnos! ✨</p>
  </div>
<?php endif;

include "../modelo/conexion.php";

if (isset($_POST['botoningresar'])) {
    if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
        $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
        $claveIngresada = $_POST['password'];

        // Buscar usuario por nombre o correo
        $sql = $conexion->prepare("SELECT * FROM administrador WHERE usuario = ?");
        $sql->bind_param("s", $usuario);
        $sql->execute();
        $resultado = $sql->get_result();

        if ($datos = $resultado->fetch_object()) {
            if (password_verify($claveIngresada, $datos->contraseña)) {
                $_SESSION['usuario'] = $datos->usuario;
                header("Location: ../vistas/administrador.php");
                exit();
            } else {
                echo "⚠️ Contraseña incorrecta";
            }
        } else {
            echo "⚠️ Usuario no encontrado";
        }
    } else {
        echo "⚠️ Por favor, complete todos los campos.";
    }
}

//consultas de SQL
$horariosque = $conexion->query("SELECT * FROM horarios_corte;");
$productosquery = $conexion->query("SELECT * FROM productos");
$horariosquery = $conexion->query("SELECT * FROM horarios");
?>

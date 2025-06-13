<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

$conexion = new mysqli("localhost", "root", "", "proyectofinal", "3306");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Enviar código
if (isset($_POST['enviar_codigo'])) {
    $correo = trim($_POST["correo"]);
    $mensaje = "";
    $exito = false;

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo no tiene un formato válido.";
    } else {
        $consulta = $conexion->prepare("SELECT usuario FROM administrador WHERE Correo = ?");
        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $consulta->store_result();

        if ($consulta->num_rows > 0) {
            $codigo = rand(100000, 999999);
            $conexion->query("INSERT INTO codigos (correo, codigo, fecha_envio) VALUES ('$correo', '$codigo', NOW())");
            try {
                enviarCorreo($correo, $codigo, 'tls');
                $_SESSION["correo"] = $correo;
                $mensaje = "Código enviado al correo.";
                $exito = true;
            } catch (Exception $e) {
                $mensaje = "Error al enviar el correo: " . $e->getMessage();
            }
        } else {
            $mensaje = "Este correo no está registrado.";
        }
        $consulta->close();
    }

    echo "<script>
        alert('$mensaje');
        window.location.href = 'login.php?abrirModal=1';
    </script>";
    exit;
}

// Cambiar contraseña
if (isset($_POST['cambiar_contraseña'])) {
    $correo = $_SESSION["correo"] ?? null;
    $codigo = trim($_POST["codigo"]);
    $mensaje = "";
    $nuevaClave = trim($_POST["newPassword"]);

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $nuevaClave)) {
        echo "<script>
            alert('La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.');
            window.location.href = 'login.php?abrirModal=1';
        </script>";
        exit;
    }

    if (!$correo || empty($codigo) || empty($nuevaClave)) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        $verificar = $conexion->prepare("SELECT * FROM codigos WHERE correo = ? AND codigo = ? ORDER BY fecha_envio DESC LIMIT 1");
        $verificar->bind_param("ss", $correo, $codigo);
        $verificar->execute();
        $resultado = $verificar->get_result();

        if ($resultado->num_rows > 0) {
            $claveHash = password_hash($nuevaClave, PASSWORD_DEFAULT);
            $actualizar = $conexion->prepare("UPDATE administrador SET contraseña = ? WHERE Correo = ?");
            $actualizar->bind_param("ss", $claveHash, $correo);
            $actualizar->execute();
            $mensaje = "Contraseña actualizada exitosamente.";
        } else {
            $mensaje = "Código incorrecto.";
        }

        $verificar->close();
    }

    echo "<script>
        alert('$mensaje');
        window.location.href = 'login.php';
    </script>";
    exit;
}

// Iniciar sesión con hash
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["botoningresar"])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $consulta = $conexion->prepare("SELECT contraseña FROM administrador WHERE usuario = ?");
    $consulta->bind_param("s", $usuario);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        if (password_verify($password, $fila['contraseña'])) {
            $_SESSION['usuario'] = $usuario;
            header("Location: ../vistas/administrador.php");
            exit;
        } else {
            echo "<script>alert('Contraseña incorrecta');</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado');</script>";
    }
    $consulta->close();
}

$conexion->close();

function enviarCorreo($correo, $codigo, $modo) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Timeout = 5;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // Configuración de remitente según el correo
    if ($correo === '2129henry@gmail.com') {
        $mail->Username = '2129henry@gmail.com';
        $mail->Password = 'gbjluyylzgqorqgd';
        $mail->setFrom('2129henry@gmail.com', 'jazkel');
    } elseif ($correo === 'giovanniflorez22@gmail.com') {
        $mail->Username = 'giovanniflorez22@gmail.com';
        $mail->Password = 'daucdihsoijfdnqf';
        $mail->setFrom('giovanniflorez22@gmail.com', 'jazkel');
    } else {
        throw new Exception("Correo no autorizado para enviar mensajes.");
    }

    $mail->SMTPSecure = $modo === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $modo === 'tls' ? 587 : 465;

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->addAddress($correo);
    $mail->isHTML(true);
    $mail->Subject = 'Código de verificación';

$mail->Body = '
    <div style="font-family: Arial, sans-serif; margin: 0; padding: 0;">
        <div style="padding: 20px; text-align: center;">
            <img src="https://scontent-bog2-2.xx.fbcdn.net/v/t39.30808-6/490813502_1216079473857288_4361485695741951293_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=cc71e4&_nc_eui2=AeEW8WYJMiY7prqT9_T78m6zi96zR4LhBgyL3rNHguEGDD4wVpoRxSnKbZDh8xgX5MSPTKMNhtfUTLYUbnOUT1YT&_nc_ohc=vYnqg84aImwQ7kNvwGNsjQH&_nc_oc=Adk8E2DApM7nTrMW2yJyjtus8gHcdNYdKN1IhKUjujrj-3Z-G6666WhowUVwwk3W9Tg&_nc_zt=23&_nc_ht=scontent-bog2-2.xx&_nc_gid=pao08T6ojXImRBEI49MfUw&oh=00_AfO0Jc-_ECJPMa6R2LySm9jLHuY0EASzwA8fmOcdkhsCwA&oe=684E0EFD" alt="jazkel Logo" style="width: 100%; max-width: 300px; height: 230px; border-radius: 12px;">
        </div>
        
        <div style="background-color:#ffffff; color:#000000; padding:30px;">
            <p style="font-size:16px; margin-bottom:20px; color:#000000 !important; -webkit-text-fill-color:#000000 !important;">
                Hemos recibido una solicitud para restablecer tu contraseña. Si tú no hiciste esta solicitud, puedes ignorar este correo.
            </p>

            <p style="font-size:16px; color:#000000 !important; -webkit-text-fill-color:#000000 !important;">
                Tu código de verificación es:
            </p>

            <div style="font-size:28px; font-weight:bold; background-color:#f0f0f0; padding:15px; text-align:center; border-radius:8px; margin:20px 0; color:#000000 !important; -webkit-text-fill-color:#000000 !important;">
                ' . $codigo . '
            </div>

            <p style="font-size:16px; color:#000000 !important; -webkit-text-fill-color:#000000 !important;">
                Ingresa este código en el formulario para continuar con el proceso de recuperación de contraseña.
            </p>
        </div>
    </div>
';

    
    if (!$mail->send()) {
        throw new Exception("Error al enviar el correo: " . $mail->ErrorInfo);
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<link rel="icon" href="jazkellogo.png">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - jazkel</title>
    <link href="../css/login.css" rel="stylesheet">
</head>
<body>
    <?php
if (isset($_GET['mensaje'])) {
    echo "<script>alert('" . htmlspecialchars($_GET['mensaje']) . "');</script>";
}
?>

    <div class="login-container">
        <div class="logo">
            <img src="../img/images.jpg" alt="Logo de jazkel">
        </div>
        <h1>Iniciar Sesión</h1>
        <form method="post" action="">
        <?php
            include "../controlador/controlador_login.php";
            ?>
            <div class="input-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="usuario" required>
            </div>
            <div class="input-group">
                <label for="newPassword">Nueva Contraseña</label>
                <input type="password" id="password" minlength="8" name="password" required>

            </div>


            <button type="submit" class="btn" name="botoningresar" value="iniciarsesion">Entrar</button>
        </form>
        <div class="footer">
            <p>¿Olvidaste tu contraseña? <span id="PasswordBtn" style="color: blue; cursor: pointer;">Recuperar Contraseña</span></p>
        </div>
    </div>

    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Cambiar contraseña</h2>
            <form method="post" action="">
                <div class="input-group-container">
                    <div class="input-group">
                        <label for="correo">Ingresa tu correo electrónico:</label>
                        <input type="email" name="correo" id="correo" required>
                    </div>
                    <button type="submit" name="enviar_codigo" class="send-code-btn" style="padding: 15px 10px; font-size: 15px; border-radius: 6px;">Enviar código</button>
                </div>
                <div class="input-group">
                    <label for="codigo">Código</label>
                    <input type="text" id="codigo" name="codigo">
                </div>
                <div class="input-group">
                    <label for="newPassword">Nueva Contraseña</label>
                    <input type="password" id="newPassword" minlength="8" name="newPassword">


                </div>
                <button type="submit" name="cambiar_contraseña" class="btn">Recuperar Contraseña</button>
            </form>
        </div>
    </div>

    <script>
        // Abrir modal al hacer clic en el texto "Recuperar Contraseña"
    document.getElementById("PasswordBtn").addEventListener("click", function () {
        document.getElementById("registerModal").style.display = "block";
    });

    // Cerrar modal al hacer clic en la X
    document.getElementById("closeModal").addEventListener("click", function () {
        document.getElementById("registerModal").style.display = "none";
    });

    // Cerrar modal si se hace clic fuera del modal
    window.addEventListener("click", function (event) {
        const modal = document.getElementById("registerModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
            const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("abrirModal") === "1") {
        document.getElementById('registerModal').style.display = "block";
    }
    // Mostrar/ocultar contraseña en el formulario de login
    document.getElementById('toggleLoginPassword').addEventListener('change', function () {
        const passwordInput = document.getElementById('password');
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    // Mostrar/ocultar contraseña
    document.getElementById('togglePassword').addEventListener('change', function () {
        const passwordInput = document.getElementById('newPassword');
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    </script>
</body>
</html>

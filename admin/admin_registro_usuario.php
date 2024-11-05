<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
    exit();
}

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "u449484077_root";
$password = "Expoindustriatkt1";
$dbname = "u449484077_canacintrabd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $empresa = $_POST['empresa'];
    $puesto = $_POST['puesto'];

    if ($nombre && $apellido && $correo && $empresa && $puesto) {
        $sql = "INSERT INTO usuarios (nombre, apellido, correo_electronico, telefono, empresa, puesto) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nombre, $apellido, $correo, $telefono, $empresa, $puesto);

        if ($stmt->execute()) {
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'canacintra.tecate24@gmail.com';
                $mail->Password = 'vlyb bqyi bhzl lgab';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('canacintra.tecate24@gmail.com', 'CANACINTRA Tecate');
                $mail->addAddress($correo);

                $mail->isHTML(true);
                $mail->Subject = 'Registro en Expo Industria Tecate 2024';
                $mail->Body = "
                    <html>
                    <body>
                        <h2>¡Gracias por registrarte, $nombre!</h2>
                        <p>Hola $nombre $apellido,</p>
                        <p>Gracias por registrarte en la Expo Industria Tecate 2024. Nos vemos pronto.</p>
                        <p>&copy; 2024 CANACINTRA Tecate. Todos los derechos reservados.</p>
                    </body>
                    </html>";

                $mail->send();
                echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente y correo enviado.']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Usuario registrado, pero error al enviar el correo: ' . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    }
}

$conn->close();
?>

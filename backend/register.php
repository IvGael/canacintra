<?php
require 'vendor/autoload.php';  // Asegúrate de que la ruta a autoload.php sea correcta

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "canacintra";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]));
}

// Verificar y obtener datos del formulario
$firstName = isset($_POST['first_name']) ? $_POST['first_name'] : null;
$lastName = isset($_POST['last_name']) ? $_POST['last_name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;
$eventId = isset($_POST['event_id']) ? $_POST['event_id'] : null;

// Verificar si todos los datos requeridos están presentes
if ($firstName && $lastName && $email && $eventId) {
    // Insertar usuario
    $sqlUser = "INSERT INTO usuarios (nombre, apellido, correo_electronico, telefono) VALUES (?, ?, ?, ?)";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("ssss", $firstName, $lastName, $email, $phone);

    if ($stmtUser->execute()) {
        $userId = $stmtUser->insert_id;

        // Insertar registro
        $sqlRegistration = "INSERT INTO registros (id_usuario, id_evento) VALUES (?, ?)";
        $stmtRegistration = $conn->prepare($sqlRegistration);
        $stmtRegistration->bind_param("ii", $userId, $eventId);

        if ($stmtRegistration->execute()) {
            // Enviar correo electrónico
            $mail = new PHPMailer(true);
            try {
                // Configuración del servidor
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  // Cambia esto al servidor SMTP que estás utilizando
                $mail->SMTPAuth = true;
                $mail->Username = 'gael.chavez@uabc.edu.mx';  // Cambia esto a tu correo electrónico
                $mail->Password = 'Happ1Goek';  // Cambia esto a tu contraseña
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Remitente y destinatario
                $mail->setFrom('gael.chavez@uabc.edu.mx', 'CANACINTRA Tecate');
                $mail->addAddress($email);

                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Registro en Expo Empresas';
                $mail->Body    = 'Hola, <br><br> Gracias por registrarte en el evento Expo Empresas. <br><br> Saludos,<br>CANACINTRA Tecate';

                $mail->send();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'El mensaje no pudo ser enviado. Error: ' . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $stmtRegistration->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmtUser->error]);
    }

    $stmtUser->close();
    $stmtRegistration->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario.']);
}

$conn->close();
?>
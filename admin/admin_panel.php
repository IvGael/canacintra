<?php
require 'vendor/autoload.php';
require 'plugins/phpqrcode/qrlib.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// Activa el almacenamiento en búfer de salida para evitar salidas adicionales
ob_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "u449484077_root";
$password = "Expoindustriatkt1";
$dbname = "u449484077_canacintrabd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

// Datos del formulario
$firstName = $_POST['first_name'] ?? null;
$lastName = $_POST['last_name'] ?? null;
$email = $_POST['email'] ?? null;
$phone = $_POST['phone'] ?? null;
$company = $_POST['company'] ?? null;
$position = $_POST['position'] ?? null;

if ($firstName && $lastName && $email && $phone && $company && $position) {
    $sqlUser = "INSERT INTO usuarios (nombre, apellido, correo_electronico, telefono, empresa, puesto) VALUES (?, ?, ?, ?, ?,?)";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("ssssss", $firstName, $lastName, $email, $phone, $company, $position);

    if ($stmtUser->execute()) {
        // Generación del vCard para el código QR
        $vCard = "BEGIN:VCARD\n";
        $vCard .= "VERSION:3.0\n";
        $vCard .= "FN:{$firstName} {$lastName}\n";
        $vCard .= "ORG:{$company}\n";
        $vCard .= "TITLE:{$position}\n";
        $vCard .= "TEL:{$phone}\n";
        $vCard .= "EMAIL:{$email}\n";
        $vCard .= "END:VCARD";

        // Enviar correo de agradecimiento
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
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Gracias por registrarte en Expo Industria Tecate 2024';
            $mail->Body = "
                <html>
                <body>
                    <h1>¡Gracias, $firstName!</h1>
                    <p>Estamos agradecidos de que te hayas registrado en Expo Industria Tecate 2024. ¡Nos vemos pronto!</p>
                </body>
                </html>";

            // Enviar correo una sola vez
            $mail->send();

            // Limpiar el buffer y enviar la respuesta JSON solo una vez
            ob_end_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Usuario registrado correctamente y correo enviado.',
                'qrData' => $vCard
            ]);
        } catch (Exception $e) {
            // Si hay un error, limpiar el buffer y enviar el mensaje de error
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'El correo no pudo ser enviado. Error: ' . $mail->ErrorInfo]);
        }
    } else {
        // En caso de error en la inserción a la base de datos, limpiar el buffer y enviar el mensaje de error
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: ' . $stmtUser->error]);
    }

    $stmtUser->close();
} else {
    // En caso de falta de datos, limpiar el buffer y enviar el mensaje de error
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario.']);
}

$conn->close();
?>

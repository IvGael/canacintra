<?php
require 'vendor/autoload.php';
require 'plugins/phpqrcode/qrlib.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

ob_start(); // Inicia el almacenamiento en búfer de la salida

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "canacintra";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

$firstName = isset($_POST['first_name']) ? $_POST['first_name'] : null;
$lastName = isset($_POST['last_name']) ? $_POST['last_name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;
$company = isset($_POST['company']) ? $_POST['company'] : null;
$position = isset($_POST['position']) ? $_POST['position'] : null;

if ($firstName && $lastName && $email && $company && $position) {
    $sqlUser = "INSERT INTO usuarios (nombre, apellido, correo_electronico, telefono, empresa, puesto) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtUser = $conn->prepare($sqlUser);
    if (!$stmtUser) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta de usuario: ' . $conn->error]);
        exit();
    }
    $stmtUser->bind_param("ssssss", $firstName, $lastName, $email, $phone, $company, $position);

    if ($stmtUser->execute()) {
        $userId = $stmtUser->insert_id;

        // Inserción en la tabla de registros
        $sqlRegistration = "INSERT INTO registros (id_usuario, asistencia) VALUES (?, 0)";
        $stmtRegistration = $conn->prepare($sqlRegistration);
        if (!$stmtRegistration) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta de registro: ' . $conn->error]);
            exit();
        }
        $stmtRegistration->bind_param("i", $userId);

        if ($stmtRegistration->execute()) {
            $registrationId = $stmtRegistration->insert_id;

            // Generación del vCard
            $vCard = "BEGIN:VCARD\n";
            $vCard .= "VERSION:3.0\n";
            $vCard .= "FN:{$firstName} {$lastName}\n"; // Nombre completo del usuario
            $vCard .= "ORG:{$company}\n"; // Nombre de la empresa
            $vCard .= "TITLE:{$position}\n"; // Puesto en la empresa
            $vCard .= "TEL:{$phone}\n"; // Teléfono
            $vCard .= "EMAIL:{$email}\n"; // Correo electrónico
            $vCard .= "END:VCARD";

            // Generación del código QR con el contenido vCard
            $dir = 'plugins/codes/';
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    ob_end_clean();
                    echo json_encode(['success' => false, 'message' => 'No se pudo crear el directorio para los códigos QR.']);
                    exit();
                }
            }

            $filename = $dir . $registrationId . '_vcard.png';
            $tamaño = 10;
            $level = 'L';
            $frameSize = 3;

            try {
                QRcode::png($vCard, $filename, $level, $tamaño, $frameSize);

                if (!file_exists($filename)) {
                    throw new Exception('El archivo QR no se generó correctamente.');
                }

                $cid = md5(uniqid(time()));

                $mail = new PHPMailer(true);

                // Activa el nivel de depuración de SMTP para desarrollo (ajusta a 0 en producción)
                $mail->SMTPDebug = 2;

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'gael.chavez@uabc.edu.mx';  // Asegúrate de usar una contraseña de aplicación si tienes 2FA activado
                    $mail->Password = 'Happ1Goek';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('gael.chavez@uabc.edu.mx', 'CANACINTRA Tecate');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Registro en Expo Empresas';
                    $mail->AddEmbeddedImage($filename, $cid, basename($filename));
                    $mail->Body    = "
                        <html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; line-height: 1.6; }
                                .container { padding: 20px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px; margin: auto; }
                                .header { background-color: #f8f8f8; padding: 10px; text-align: center; border-bottom: 1px solid #ddd; }
                                .header h1 { margin: 0; }
                                .content { padding: 20px; }
                                .content p { margin: 0 0 10px; }
                                .footer { text-align: center; padding: 10px; border-top: 1px solid #ddd; background-color: #f8f8f8; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <div class='header'>
                                    <h1>¡Gracias por registrarte, $firstName!</h1>
                                </div>
                                <div class='content'>
                                    <p>Hola $firstName,</p>
                                    <p>Gracias por registrarte a Expo Industria Tecate 2024. A continuación, encontrarás tu código QR con la información de contacto que necesitarás para el acceso al evento:</p>
                                    <p><img src='cid:$cid' alt='Código QR'></p>
                                    <p>¡Nos vemos pronto!</p>
                                </div>
                                <div class='footer'>
                                    <p>&copy; 2024 CANACINTRA Tecate. Todos los derechos reservados.</p>
                                </div>
                            </div>
                        </body>
                        </html>";

                    $mail->send();
                    ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Correo enviado correctamente.']);
                } catch (Exception $e) {
                    ob_end_clean();
                    echo json_encode(['success' => false, 'message' => 'El mensaje no pudo ser enviado. Error: ' . $mail->ErrorInfo]);
                }
            } catch (Exception $e) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Error al generar el código QR: ' . $e->getMessage()]);
            }
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: ' . $stmtRegistration->error]);
        }
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error al insertar el usuario: ' . $stmtUser->error]);
    }

    $stmtUser->close();
    $stmtRegistration->close();
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Faltan datos en el formulario.']);
}

$conn->close();
?>

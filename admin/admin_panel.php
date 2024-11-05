<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.html");
    exit();
}

require 'vendor/autoload.php'; // Asegúrate de tener PHPMailer correctamente instalado
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "u449484077_root";
$password = "Expoindustriatkt1";
$dbname = "u449484077_canacintrabd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verifica si los datos se reciben
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $empresa = $_POST['empresa'];
    $puesto = $_POST['puesto'];

    // Mensajes de depuración
    if ($nombre && $apellido && $correo && $empresa && $puesto) {
        echo "Datos recibidos correctamente: $nombre, $apellido, $correo, $telefono, $empresa, $puesto<br>";

        $sql = "INSERT INTO usuarios (nombre, apellido, correo_electronico, telefono, empresa, puesto) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssss", $nombre, $apellido, $correo, $telefono, $empresa, $puesto);

            if ($stmt->execute()) {
                echo "Usuario registrado correctamente.<br>";
                // Puedes agregar más depuración en el bloque de correo si es necesario.
            } else {
                echo "Error al registrar el usuario: " . $stmt->error . "<br>";
            }
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error . "<br>";
        }
    } else {
        echo "Datos incompletos: verifica el formulario.<br>";
    }
}

$conn->close();
?>

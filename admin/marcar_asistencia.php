<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "canacintra";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

$qrCodeData = isset($_POST['qrCodeData']) ? $_POST['qrCodeData'] : null;

if ($qrCodeData) {
    // Suponiendo que $qrCodeData tiene el correo electrónico del usuario
    $sql = "UPDATE registros INNER JOIN usuarios ON registros.id_usuario = usuarios.id 
            SET registros.asistencia = 1 WHERE usuarios.correo_electronico = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $qrCodeData);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Asistencia marcada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al marcar la asistencia.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos insuficientes.']);
}

$conn->close();
?>

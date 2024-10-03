<?php
require '/backend/vendor/autoload.php'; // Carga automática de dependencias
require '/backend/plugins/phpqrcode/qrlib.php'; // Librería PHPQRCode para generar códigos QR

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$qrData = isset($_POST['qrData']) ? $_POST['qrData'] : null;

if ($qrData) {
    // Ruta y nombre del archivo QR
    $dir = 'backend/plugins/codes/';
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0777, true)) {
            echo json_encode(['success' => false, 'message' => 'No se pudo crear el directorio para los códigos QR.']);
            exit();
        }
    }
    
    $filename = $dir . uniqid() . '_qr.png';
    $tamaño = 10; // Tamaño del QR
    $level = 'L'; // Nivel de corrección de errores
    $frameSize = 3; // Tamaño del borde

    try {
        // Generar el código QR
        QRcode::png($qrData, $filename, $level, $tamaño, $frameSize);

        if (!file_exists($filename)) {
            throw new Exception('El archivo QR no se generó correctamente.');
        }

        // Retornar la ruta del archivo generado
        echo json_encode(['success' => true, 'qr_path' => $filename]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al generar el código QR: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se recibió el código QR.']);
}
?>

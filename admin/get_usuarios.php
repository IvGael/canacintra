<?php
header('Content-Type: application/json'); // Asegura que la respuesta sea de tipo JSON

$servername = "localhost";
$username = "u449484077_root";
$password = "Expoindustriatkt1";
$dbname = "u449484077_canacintrabd";

// Establece la conexión con la base de datos dentro de un bloque try-catch
try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica la conexión
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }

    // Prepara la consulta SQL
    $sql = "SELECT id, nombre, apellido, correo_electronico, telefono, empresa, puesto FROM usuarios";
    $result = $conn->query($sql);

    // Verifica si hubo un error al ejecutar la consulta
    if (!$result) {
        throw new Exception("Error al ejecutar la consulta: " . $conn->error);
    }

    // Construye el array de usuarios
    $usuarios = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }

    // Devuelve los resultados como JSON
    echo json_encode($usuarios);

} catch (Exception $e) {
    // Manejo de errores: muestra un mensaje genérico y registra los detalles en un log
    http_response_code(500); // Código de error HTTP para errores internos del servidor
    echo json_encode(["error" => "Hubo un problema al procesar la solicitud."]);
    error_log($e->getMessage()); // Registra el error en el archivo de logs del servidor
} finally {
    // Cierra la conexión
    if (isset($conn) && $conn->ping()) {
        $conn->close();
    }
}
?>

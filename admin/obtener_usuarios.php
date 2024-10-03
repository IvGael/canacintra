<?php
// Conexión a la base de datos
$host = 'localhost'; // Cambia esto si es necesario
$dbname = 'canacintra'; // Nombre de tu base de datos
$username = 'root'; // Usuario de la base de datos
$password = ''; // Contraseña de la base de datos

try {
    // Crear conexión con PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

// Consulta para obtener los usuarios registrados
$sql = "SELECT id, nombre, apellido, correo_electronico, telefono, empresa, puesto FROM usuarios";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los datos en formato JSON
echo json_encode($usuarios);
?>

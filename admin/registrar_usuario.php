<?php
// Conexión a la base de datos
$host = 'localhost'; // Cambia esto si tu servidor de base de datos es diferente
$dbname = 'canacintra'; // Nombre de tu base de datos
$username = 'root'; // Usuario de la base de datos
$password = ''; // Contraseña de la base de datos

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

// Obtener los datos enviados a través de POST
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
$apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : null;
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : null;
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;
$empresa = isset($_POST['empresa']) ? trim($_POST['empresa']) : null;
$puesto = isset($_POST['puesto']) ? trim($_POST['puesto']) : null;

// Validar que los campos requeridos no estén vacíos
if (!$nombre || !$apellido || !$correo) {
    echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos obligatorios.']);
    exit();
}

// Preparar la consulta para insertar el nuevo usuario
$sql = "INSERT INTO usuarios (nombre, apellido, correo_electronico, telefono, empresa, puesto) VALUES (:nombre, :apellido, :correo, :telefono, :empresa, :puesto)";
$stmt = $pdo->prepare($sql);

try {
    // Ejecutar la consulta
    $stmt->execute([
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':correo' => $correo,
        ':telefono' => $telefono,
        ':empresa' => $empresa,
        ':puesto' => $puesto
    ]);

    // Obtener el ID del usuario recién creado
    $usuarioId = $pdo->lastInsertId();

    // Insertar registro de asistencia (asistencia por defecto es 0)
    $sqlAsistencia = "INSERT INTO registros (id_usuario, asistencia) VALUES (:id_usuario, 0)";
    $stmtAsistencia = $pdo->prepare($sqlAsistencia);
    $stmtAsistencia->execute([':id_usuario' => $usuarioId]);

    echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: ' . $e->getMessage()]);
}

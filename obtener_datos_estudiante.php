<?php
// Conexión a la base de datos
$servername = "mysql_container2";
$username = "root";
$password = "12345";
$dbname = "itesa";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Verificar si se recibió una matrícula
if(isset($_GET['matricula'])) {
    $matricula = $_GET['matricula']; 
    
    // Consulta preparada para obtener los datos del estudiante
    $stmt = $conn->prepare("SELECT nombre, paterno, materno FROM estudiantes WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        // Construir el nombre completo
        $nombre_completo = $row['nombre'] . " " . $row['paterno'] . " " . $row['materno'];
        echo json_encode(['status' => 'success', 'nombre' => $nombre_completo]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Estudiante no encontrado']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se proporcionó una matrícula']);
}

$conn->close();
?>
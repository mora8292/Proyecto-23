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
    header("Content-Type: image/png");
    readfile("imagenes/Persona.png");
    exit;
}

// Verificar si se recibió una matrícula
if(isset($_GET['matricula'])) {
    $matricula = $_GET['matricula'];
    
    // Consulta preparada para obtener la imagen
    $stmt = $conn->prepare("SELECT foto FROM fotos WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $stmt->store_result();
    
    if($stmt->num_rows > 0) {
        $stmt->bind_result($foto);
        $stmt->fetch();
        
        // Enviar la imagen al navegador
        header("Content-Type: image/jpeg"); // Ajusta según el tipo de imagen
        echo $foto;
    } else {
        // No se encontró la foto, mostrar imagen por defecto
        header("Content-Type: image/png");
        readfile("imagenes/Persona.png");
    }
    
    $stmt->close();
} else {
    // No se proporcionó matrícula, mostrar imagen por defecto
    header("Content-Type: image/png");
    readfile("imagenes/Persona.png");
}

$conn->close();
?>
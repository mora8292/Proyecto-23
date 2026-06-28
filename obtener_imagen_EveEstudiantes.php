<?php
session_start();
header("Content-Type: image/jpeg");

// Configuración de la BD
$host = 'mysql_container2';
$dbname = 'itesa';
$username = 'root';
$password = '12345';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $matricula = $_SESSION["usuario"]["matricula"] ?? '';
    
    if (!empty($matricula)) {
        $stmt = $conn->prepare("SELECT foto FROM fotos WHERE matricula = :matricula");
        $stmt->bindParam(':matricula', $matricula);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['foto'])) {
            echo $result['foto']; // Devuelve el BLOB directamente
        } else {
            // Imagen por defecto si no hay resultados
            readfile('imagenes/Persona.png');
        }
    } else {
        readfile('imagenes/Persona.png');
    }
} catch(PDOException $e) {
    error_log("Error al obtener imagen: " . $e->getMessage());
    readfile('imagenes/Persona.png');
}
?>
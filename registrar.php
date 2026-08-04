<?php
require 'conexion.php';

if (!isset($_POST['mat']) || !isset($_POST['ev'])) {
    echo "error";
    exit;
}

$matricula = trim($_POST['mat']);
$evento = trim($_POST['ev']);

// Verificar si ya existe el registro
$sql = "SELECT COUNT(*) AS total
        FROM qreventos
        WHERE Id_Evento = ?
        AND matricula = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $evento, $matricula);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt->close();

if ($row['total'] > 0) {
    echo "duplicado";
    exit;
}

// Insertar solamente si no existe
$sql = "INSERT INTO qreventos (Id_Evento, matricula)
        VALUES (?, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $evento, $matricula);

if ($stmt->execute()) {
    echo "si";
} else {
    echo "error";
}

$stmt->close();
$mysqli->close();
?>
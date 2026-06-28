<?php
require 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_evento'];
    $nombre = $_POST['evento'];
    $fecha = $_POST['fechayhora'];
    $lugar = $_POST['lugar'];
    
    // Si el lugar es "Otro", usar el valor del campo adicional
    if ($lugar == 'Otro' && isset($_POST['lugar_otro'])) {
        $lugar = $_POST['lugar_otro'];
    }

    try {
        $stmt = $conn->prepare("UPDATE eventos SET Nombre_Evento = ?, Fecha_Evento = ?, Lugar_Evento = ? WHERE Id_Evento = ?");
        $stmt->bind_param("sssi", $nombre, $fecha, $lugar, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Evento actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el evento']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'MÃ©todo no permitido']);
}
?>

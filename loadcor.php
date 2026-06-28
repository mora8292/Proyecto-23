<?php
require 'config.php';

$sql = "SELECT Id_Evento, Nombre_Evento, Fecha_Evento, Lugar_Evento FROM eventos ORDER BY Fecha_Evento DESC";
$resultado = $conn->query($sql);
$num_rows = $resultado->num_rows;
$conn->set_charset("utf8mb4"); // Asegura codificación correcta para caracteres especiales/emojis

$html = '';

if($num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $html .= '<tr data-id="'.$row['Id_Evento'].'">';
        $html .= '<td>'.htmlspecialchars($row['Nombre_Evento']).'</td>';  
        $html .= '<td>'.htmlspecialchars($row['Fecha_Evento']).'</td>'; 
        $html .= '<td>'.htmlspecialchars($row['Lugar_Evento']).'</td>'; 
        $html .= '<td>';
        $html .= '<button class="btn btn-success btn-modificar btn-action">';
        $html .= '<img src="imagenes/modifica.png" style="height:20px; width:20px;"> Modificar</button>';
        $html .= '<button class="btn btn-danger btn-eliminar btn-action">';
        $html .= '<img src="imagenes/borrar.png" style="height:20px; width:20px;"> Eliminar</button>';
        $html .= '</td>';
        $html .= '</tr>';
    }
} else {
    $html = '<tr><td colspan="4" class="text-center">No hay eventos registrados</td></tr>';
}

echo json_encode($html, JSON_UNESCAPED_UNICODE);
?>
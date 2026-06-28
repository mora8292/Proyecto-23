<?php

require 'conexion.php'; 
$columns = ['Nombre_Evento','Fecha_Evento','Lugar_Evento'];

$campo = $_POST["Eventos"];

$sql = "SELECT * FROM eventos WHERE Id_Evento = ".$campo;

$resultado = $mysqli->query($sql);
$num_rows = $resultado->num_rows;

$html =''; 

if($num_rows>0){
    while ($row = $resultado->fetch_assoc()){
        
        $html .='<tr>';
        $html .= '<td>'.$row['Nombre_Evento'].'</td>';   //columna, nombre evento
        $html .= '<td>'.$row['Fecha_Evento'].'</td>'; 
        $html .= '<td>'.$row['Lugar_Evento'].'</td>'; 
        $html .='</tr>';
    }
}

echo json_encode($html, JSON_UNESCAPED_UNICODE);
?>

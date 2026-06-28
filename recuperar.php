<?php
require 'conexion.php';
session_start();

date_default_timezone_set("America/Mexico_City");

// Fecha y hora actual
$fecha_actual = date("Y-m-d H:i:s");

// Fecha actual menos 5 minutos
$fecha_tolerancia = date("Y-m-d H:i:s", strtotime("-5 minutes"));

if (isset($_SESSION["usuario"]["matricula"]) && !empty($_SESSION["usuario"]["matricula"])) {
    // Es estudiante: eventos desde hace 5 minutos hasta el futuro
    $consulta = "SELECT * FROM eventos WHERE Fecha_Evento >= ? ORDER BY Fecha_Evento ASC";
    $stmt = $mysqli->prepare($consulta);
    $stmt->bind_param("s", $fecha_tolerancia);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {
        echo "<option value='" . $fila['Id_Evento'] . "'>" . $fila['Nombre_Evento'] . "</option>";
    }

    $stmt->close();
} else {
    // No es estudiante: todos los eventos
    $consulta = "SELECT * FROM eventos ORDER BY Fecha_Evento ASC";
    $resultado = $mysqli->query($consulta);

    while ($fila = $resultado->fetch_array()) {
        echo "<option value='" . $fila['Id_Evento'] . "'>" . $fila['Nombre_Evento'] . "</option>";
    }
}
?>

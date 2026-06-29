<?php
require 'conexion.php';
session_start();

date_default_timezone_set("America/Mexico_City");

$fecha_tolerancia = date("Y-m-d H:i:s", strtotime("-5 minutes"));

$verTodos = isset($_POST['verTodos']) ? $_POST['verTodos'] : 0;

if (isset($_SESSION["usuario"]["matricula"]) && !empty($_SESSION["usuario"]["matricula"])) {

    $matricula = $_SESSION["usuario"]["matricula"];

    // Obtener la carrera del estudiante
    $sqlCarrera = "SELECT c.carrera
                   FROM estudiantes e
                   INNER JOIN carreras c ON e.carrera = c.id
                   WHERE e.matricula = ?";

    $stmtCarrera = $mysqli->prepare($sqlCarrera);
    $stmtCarrera->bind_param("s", $matricula);
    $stmtCarrera->execute();

    $resultadoCarrera = $stmtCarrera->get_result();
    $datosCarrera = $resultadoCarrera->fetch_assoc();

    $stmtCarrera->close();

    $carrera = $datosCarrera['carrera'];

    if ($verTodos == 1) {

        // Mostrar todos los eventos
        $consulta = "SELECT *
                     FROM eventos
                     WHERE Fecha_Evento >= ?
                     ORDER BY Fecha_Evento ASC";

        $stmt = $mysqli->prepare($consulta);
        $stmt->bind_param("s", $fecha_tolerancia);

    } else {

        // Mostrar únicamente los eventos de la carrera
        $consulta = "SELECT *
                     FROM eventos
                     WHERE `$carrera` = 1
                     AND Fecha_Evento >= ?
                     ORDER BY Fecha_Evento ASC";

        $stmt = $mysqli->prepare($consulta);
        $stmt->bind_param("s", $fecha_tolerancia);

    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    while($fila = $resultado->fetch_assoc()){
        echo "<option value='".$fila['Id_Evento']."'>".$fila['Nombre_Evento']."</option>";
    }

    $stmt->close();

}else{

    // Coordinadores
    $consulta = "SELECT *
                 FROM eventos
                 ORDER BY Fecha_Evento ASC";

    $resultado = $mysqli->query($consulta);

    while($fila = $resultado->fetch_assoc()){
        echo "<option value='".$fila['Id_Evento']."'>".$fila['Nombre_Evento']."</option>";
    }

}
?>
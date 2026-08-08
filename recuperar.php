<?php

require 'conexion.php';
session_start();

date_default_timezone_set("America/Mexico_City");

// Mostrar eventos del día en adelante
$fecha_tolerancia = date("Y-m-d 00:00:00");

$verTodos = isset($_POST['verTodos']) ? intval($_POST['verTodos']) : 0;

echo "<option value=''>Seleccione un evento</option>";

if(isset($_SESSION["usuario"]["matricula"])){

    $matricula = $_SESSION["usuario"]["matricula"];

    $sql = "SELECT c.carrera
            FROM usuarios u
            INNER JOIN usuarios_roles ur ON ur.idUsuario = u.id_usuario
            INNER JOIN roles r ON r.idRol = ur.idRol
            INNER JOIN carreras c
            ON u.carrera=c.id
            WHERE u.matricula=?
              AND r.nombreRol = 'Estudiante'";

    $stmt = $mysqli->prepare($sql);

    if(!$stmt){
        die($mysqli->error);
    }

    $stmt->bind_param("s",$matricula);
    $stmt->execute();

    $res = $stmt->get_result();

    if($res->num_rows==0){
        exit;
    }

    $datos = $res->fetch_assoc();

    $stmt->close();

    $carrera = $datos["carrera"];

    if($verTodos){

        $sql="SELECT *
              FROM eventos
              WHERE Fecha_Evento>=?
              ORDER BY Fecha_Evento ASC";

        $stmt=$mysqli->prepare($sql);

        $stmt->bind_param("s",$fecha_tolerancia);

    }else{

        $sql="SELECT *
              FROM eventos
              WHERE `$carrera`=1
              AND Fecha_Evento>=?
              ORDER BY Fecha_Evento ASC";

        $stmt=$mysqli->prepare($sql);

        if(!$stmt){
            die($mysqli->error);
        }

        $stmt->bind_param("s",$fecha_tolerancia);

    }

    $stmt->execute();

    $resultado=$stmt->get_result();

}else{

    $resultado=$mysqli->query("
        SELECT *
        FROM eventos
        ORDER BY Fecha_Evento ASC
    ");

}

if($resultado && $resultado->num_rows>0){

    while($fila=$resultado->fetch_assoc()){

        echo "<option value='".$fila["Id_Evento"]."'>".$fila["Nombre_Evento"]."</option>";

    }

}else{

    echo "<option value=''>No hay eventos disponibles</option>";

}

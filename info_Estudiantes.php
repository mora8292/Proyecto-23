<?php

require("conexion.php");

if (isset($_SESSION["usuario"]["matricula"])!= ''){

$d=$_SESSION["usuario"]["matricula"];

    }
    $consulta = "SELECT u.*
                 FROM usuarios u
                 INNER JOIN usuarios_roles ur ON ur.idUsuario = u.id_usuario
                 INNER JOIN roles r ON r.idRol = ur.idRol
                 WHERE u.matricula = $d
                   AND r.nombreRol = 'Estudiante'";

    $ejecutarConsulta = $mysqli->query($consulta);
    

    while ($fila = mysqli_fetch_array($ejecutarConsulta)){
        echo "Nombre: " .$fila['nombre'];
        echo " ".$fila['paterno'];
        echo " ".$fila['materno'] ; 
        echo "<br>"; 
        echo " Matricula: ".$fila['matricula'];   
    
}

?>

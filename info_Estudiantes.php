<?php

require("conexion.php");

if (isset($_SESSION["usuario"]["matricula"])!= ''){

$d=$_SESSION["usuario"]["matricula"];

    }
    $consulta = "SELECT * FROM estudiantes WHERE matricula= $d";

    $ejecutarConsulta = $mysqli->query($consulta);
    

    while ($fila = mysqli_fetch_array($ejecutarConsulta)){
        echo "Nombre: " .$fila['nombre'];
        echo " ".$fila['paterno'];
        echo " ".$fila['materno'] ; 
        echo "<br>"; 
        echo " Matricula: ".$fila['matricula'];   
    
}

?>

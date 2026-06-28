<?php
require("conexion.php");

if (isset($_SESSION["usuario"]["clave_D"])!= ''){

$d=$_SESSION["usuario"]["clave_D"];
    }
    $consulta = "SELECT * FROM docentes WHERE clave= $d";

    $ejecutarConsulta = $mysqli->query($consulta);

    while ($fila = mysqli_fetch_array($ejecutarConsulta)){
        echo "Nombre: " .$fila['nombre'];
        echo " ".$fila['paterno'];
        echo " ".$fila['materno'] ; 
        echo "<br>"; 
        echo " Clave: ".$fila['clave'];   
    
}

?>

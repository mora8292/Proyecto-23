<?php
require("conexion.php");

if (isset($_SESSION["usuario"]["clave_D"])!= ''){

$d=$_SESSION["usuario"]["clave_D"];
    }
    $consulta = "SELECT u.*
                 FROM usuarios u
                 INNER JOIN usuarios_roles ur ON ur.idUsuario = u.id_usuario
                 INNER JOIN roles r ON r.idRol = ur.idRol
                 WHERE u.clave = $d
                   AND r.nombreRol = 'Docente'";

    $ejecutarConsulta = $mysqli->query($consulta);

    while ($fila = mysqli_fetch_array($ejecutarConsulta)){
        echo "Nombre: " .$fila['nombre'];
        echo " ".$fila['paterno'];
        echo " ".$fila['materno'] ; 
        echo "<br>"; 
        echo " Clave: ".$fila['clave'];   
    
}

?>

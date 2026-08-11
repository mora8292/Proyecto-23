<?php
require("conexion.php");
require_once("auth.php");

if (usuarioEsAdministrador() && isset($_SESSION["usuario"]["id_usuario"])) {
    $d = (int)$_SESSION["usuario"]["id_usuario"];
    $consulta = "SELECT *
                 FROM usuarios
                 WHERE id_usuario = $d";
} elseif (isset($_SESSION["usuario"]["clave_C"])!= ''){
    $d=$_SESSION["usuario"]["clave_C"];
    $consulta = "SELECT u.*
                 FROM usuarios u
                 INNER JOIN usuarios_roles ur ON ur.idUsuario = u.id_usuario
                 INNER JOIN roles r ON r.idRol = ur.idRol
                 WHERE u.clave = $d
                   AND r.nombreRol = 'Coordinador'";
} else {
    exit;
}

    $ejecutarConsulta = $mysqli->query($consulta);

    while ($fila = mysqli_fetch_array($ejecutarConsulta)){
        echo "Nombre: " .$fila['nombre'];
        echo " ".$fila['paterno'];
        echo " ".$fila['materno'] ; 
        echo "<br>"; 
        echo " Clave: ".$fila['clave'];   
    
}

?>

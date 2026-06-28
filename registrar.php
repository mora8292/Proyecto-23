<?php 
    require 'conexion.php'; 

    $matricula=$_POST['mat'];
    $evento=$_POST['ev'];

   
   $insertar = "INSERT INTO `qreventos`(`Id_Evento`, `matricula`) VALUES ('$evento','$matricula')";
   $resultado = $mysqli->query($insertar);
   echo "si";

    ?>

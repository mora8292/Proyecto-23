<?php

$conn = new mysqli("mysql_container2","root","12345","itesa");
$conn->set_charset("utf8mb4");

if($conn->connect_error){
    die('Error de conexion'.$conn->connect_error);
}

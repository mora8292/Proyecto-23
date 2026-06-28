<?php
error_reporting(0);
ini_set('display_errors', 0);

$status;
$servername = "mysql_container2";
$connectionInfo = array("Database" => "itesa", "UID" => "root", "PWD" => "12345");
$mysqli = mysqli_connect($servername, $connectionInfo["UID"], $connectionInfo["PWD"], $connectionInfo["Database"]);

if (!$mysqli) {
    die();
}
?>

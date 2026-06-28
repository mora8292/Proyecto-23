<?php
    require_once  $_SERVER['DOCUMENT_ROOT']."/botones/Proyect/vendor/autoload.php" ;
    //require_once $_SERVER['DOCUMENT_ROOT']."/crud_mongo/vendor/autoload.php";

    class Conexion {
        public function conectar (){
            try {
                $servidor = "localhost";
                $usuario = "admin";
                $password ="1597";
                $baseDatos = "Itesa";
                $puerto = "27017";
    
                $cadenaConexion = "mongodb://".
                                    $usuario.":".
                                    $password."@".
                                    $servidor.":".
                                    $puerto."/".
                                    $baseDatos;
                $cliente = new MongoDB\Client($cadenaConexion);
                return $cliente->selectDatabase($baseDatos);
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }

    }

?>




<?php
//require_once(".Conexion.php");

class Crud extends Conexion {
    public function mostrarDatos(){
        try{
            $conexion = parent::conectar();
            $coleccion = $conexion->eventos;
            $datos = $coleccion->find();
            return $datos;
        } catch (\Throwable $th) {
            return $th ->getMessage();
        }
    }

}

?>
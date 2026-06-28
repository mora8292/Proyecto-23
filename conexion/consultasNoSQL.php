<?php
    session_start();
    require_once 'conexionNoSQL.php';
    $connNoSQL = connNoSQL::singleton();
    if(isset($_POST['accion'])  && !empty($_POST['accion'])){
        $action = $_POST['accion'];
       
        switch($action){

             case 'guardar':         
                $projeccion = ["projection" => ["eventos"=>1]];
                $tema = $connNoSQL->consultaProjeccion("EVENTOS",["eventos"=>"eventos itesa"],$projeccion);
                if(!isset($tema[0]->eventos)){
                    $connNoSQL->insertar("EVENTOS",["eventos"=>"eventos itesa"]);
                }
                $datos = $_POST['datos'];
                $eve = $_POST['eve'];
                $connNoSQL->modificar("EVENTOS",["eventos"=>"eventos itesa"],["Registrodeeventos.".$eve=>$datos]);
            break;
        /*
            case 'eliminar':

                $var1 = $_POST[''];
                $var2 = $_POST[''];
                $varn = $_POST[''];

                $connNoSQL->modificar("base",["referencia"=>$var],["direccion."=>$grupos]);
                $campo = ["referencia.".direccion=>1];
                $connNoSQL->eliminarCampo("base",["referencia"=>$var],$campo);
            break;
             */ 
/*
            case 'consultartodo':

            $projeccion = ["projection" => 
                    ["Registrodeeventos"=>1,"_id"=>0]];
                
                $resultado = $connNoSQL->consultaProjeccion("EVENTOS",["eventos"=>"eventos itesa"],$projeccion);

                if(isset($resultado[0]->Registrodeeventos)){
                    $resultado = $resultado[0]->Registrodeeventos;
                    echo json_encode($resultado);
                    //return $instrumenta
                }else{
                    echo "";
                }
                break;

            case 'consultar':
                $var1 = $_POST['n'];
                
                $projeccion = ["projection" => ["eventoss.".$var1=>1,"_id"=>0]];
                $tema = $connNoSQL->consultaProjeccion("EVENTOS",["EVENTOS"=>"dfd"],$projeccion);
                if(isset($tema[0]->eventoss->$var1)){
                    $t = $tema[0]->eventoss->$var1;
                    echo json_encode($t);
                }else{
                    echo json_encode("ok");
                }
              

            break;
             */ 
           
/*
            case'eliminar':
                $projeccion=["projection"=>["eventito.invenario."=>1]];
                echo $datos;
                $connNoSQL->eliminarCampo("EVENTOS",["eventos"=>"eventos itesa"],$campo);

            break;
                        
 */ 


        }
    }
?>
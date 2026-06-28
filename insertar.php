
<?php
    require 'conexion.php';
    $ejecutado=0;

    if(isset($_POST['evento'])){
        $evento=$_POST['evento'];
    }
    if(isset($_POST['fechaHora'])){
        $fecha=$_POST['fechaHora'];
    } 
    if(isset($_POST['lugar'])){
        $lugar=$_POST['lugar'];
    }if(isset($_POST['carreras'])){
        $carreras=$_POST["carreras"];
    }

    $arr= array(0,0,0,0,0,0,0,0,0,0);

   for ($i= 0; $i<count($carreras);$i++){
        $arr[$carreras[$i]-1]=1;
   }
   
  
    $sqlTabla1 = "INSERT into eventos(Nombre_Evento, Fecha_Evento,Lugar_Evento,ISC,IIA,IE,IL,IC,IM,IGE,ISA,LA,LT) VALUES ('$evento','$fecha','$lugar',$arr[0],'$arr[1]','$arr[2]','$arr[3]','$arr[4]','$arr[5]','$arr[6]','$arr[7]','$arr[8]','$arr[9]')";

    if($evento==""  || $fecha=="" || $lugar == ""){
        $ejecutado==0;
    }else{
        $ejecutado = $mysqli->query($sqlTabla1);
    }

    
    if($ejecutado==1){
        echo "<script>alertify.success('Registro correcto de evento :D',10)</script>";
    }else{
        echo "<script>alertify.error('Registro incorrecto de evento :(. Por favor rellena los campos antes de crear el evento',10)</script>";

    }

?>

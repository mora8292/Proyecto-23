<?php
require("conexion.php");
//$conexion = mysqli_connect('localhost','root','','itesa');

$usuario=$_POST['nombre'];
//consultar matricula de estudiantes


	if (!isset($usuario)) { /*para saber si una variable de sesion esta definida*/
		header("Location:index.php");
	}
    
    if (strlen($usuario)==5) {
    	//	echo"holaaa";
    	//consultar clave de Docentes
	$Dinicio = "SELECT count(clave) as clave FROM docentes where clave=".$usuario;
    $ejecutadoD = $mysqli->query($Dinicio);
    $clave_docentes=mysqli_fetch_array($ejecutadoD);
    
    if ($clave_docentes["clave"]==1){

    $Dinicio = "SELECT clave FROM docentes where clave=".$usuario;
    $ejecutadoD = $mysqli->query($Dinicio);
    $clave_docentes=mysqli_fetch_array($ejecutadoD);

    $Dcontra = "SELECT contrasena FROM docentes where clave=".$usuario;
    $ejecutadoDContra = $mysqli->query($Dcontra);
    $clave_docentesContra=mysqli_fetch_array($ejecutadoDContra);

    	$contraseña= $clave_docentesContra['contrasena'];

    $claveD=$clave_docentes['clave'];
    if( $usuario==$claveD && $_POST["contraseña"]==$contraseña){
		session_start();

		$_SESSION["usuario"]["clave_D"]=$claveD;

		echo "docente";
	}else {
		echo "Error, usuario o contraseña incorrecta";
	} 
    }else{
	$Cinicio = "SELECT clave FROM coordinadores where clave=".$usuario;
    $ejecutadoC = $mysqli->query($Cinicio);
    $clave_coordinadores=mysqli_fetch_array($ejecutadoC);
    	$clave_C=$clave_coordinadores['clave'];

    $Ccontra = "SELECT contrasena FROM coordinadores where clave=".$usuario;
    $ejecutadoCContra = $mysqli->query($Ccontra);
    $clave_coordinadoresContra=mysqli_fetch_array($ejecutadoCContra);

    	$contraseña= $clave_coordinadoresContra['contrasena'];

    	if( $usuario==$clave_C && $_POST["contraseña"]==$contraseña){
		session_start();

		$_SESSION["usuario"]["clave_C"]=$clave_C;

		echo "coordinador";
	}}}
	else if (strlen($usuario)>=8) {

	$Einicio = "SELECT matricula FROM estudiantes where matricula=".$usuario;
    $ejecutadoE = $mysqli->query($Einicio);
    $matriculaEstudiante=mysqli_fetch_array($ejecutadoE);

    	$matricula= $matriculaEstudiante['matricula'];


    $Econtra = "SELECT contrasena FROM estudiantes where matricula=".$usuario;
    $ejecutadoEContra = $mysqli->query($Econtra);
    $matriculaEstudianteContra=mysqli_fetch_array($ejecutadoEContra);

    	$contraseña= $matriculaEstudianteContra['contrasena'];



    	if ($usuario==$matricula && $_POST["contraseña"]==$contraseña) {
		session_start();

		$_SESSION["usuario"]["matricula"]=$matricula;
		echo "estudiante";
		}else {
		echo "Error, usuario o contraseña incorrecta";
	}
}
?>

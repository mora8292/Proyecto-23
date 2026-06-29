<?php
require("conexion.php");
//Include EL CONECTOR A GMAIL https://console.developers.google.com
include_once 'gpConfig.php';
//VERIFICANDO SI YA SE LOGUEO
$errorNI="";
if(isset($_GET['code'])){
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
	$gClient->setAccessToken($_SESSION['token']);
}
 
$output="";
if ( $gClient->getAccessToken() ) { //SE HA LOGUEADO
	//Get user profile data from google
	
	$gpUserProfile = $google_oauthV2->userinfo->get();
	//RESCATANDO CORREO ELECTR�NICO
	$email = $gpUserProfile[ 'email' ];
	//SEPARANDO POR EL @ 
	$cade=explode ("@",$email);
	$p1=$cade[0];
	$p2=$cade[1];
	$errorNI="";

   
	//PRIMERO SE VALIDA POR DOMINIO DE CORREO ELECTR�NICO
	if($p2!="itesa.edu.mx"){
		$errorNI="No es correo de ITESA";	
		$authUrl = $gClient->createAuthUrl();
		$output = '<a href="' . filter_var( $authUrl, FILTER_SANITIZE_URL ) . '"><img id="gmail" src="imagenes/gmai.png" alt=""/></a>';
	}else{
		//SI SE PUEDE LOGUEAR YA SOLO QUEDA HACER OTRAS VALIDACIONES PARA PODER INGRESAR
		//SE PUEDE HACER CONSULTA A BASE DE DATOS Y VERIFICAR USUARIO
		//busqueda estudiante
    $Einicio = "SELECT matricula FROM estudiantes where matricula=".$p1;
    $ejecutadoE = $mysqli->query($Einicio);
    $matriculaEstudiante=mysqli_fetch_array($ejecutadoE);
    $matricula= $matriculaEstudiante['matricula'];

    if($p1==$matricula){

            session_start();
            $_SESSION["usuario"]["matricula"]=$p1;
            
            header('Location:eventos_Estudiantes.php'); 

        
        }else {
            $errorNI="NO TE ENCUENTRAS EN LA BASE DE DATOS DE ITESA";
            $authUrl = $gClient->createAuthUrl();
            $output = '<a href="' . filter_var( $authUrl, FILTER_SANITIZE_URL ) . '"><img id="gmail" src="imagenes/gmai.png" alt=""/></a>';
        }
        
    $Cinicio = "SELECT count(correo) as val FROM coordinadores where correo=".$cade;
    $ejecutadoC = $mysqli->query($Cinicio);
    $clave_coordinadores=mysqli_fetch_array($ejecutadoC);
    
    if($clave_coordinadores['val']==1){
    $Cinicio = "SELECT correo FROM coordinadores where correo=".$cade;
    $ejecutadoC = $mysqli->query($Cinicio);
    $clave_coordinadores=mysqli_fetch_array($ejecutadoC);
    $claveC=$clave_coordinadores['correo'];
    if ($cade==$claveC) {
            session_start();
            $_SESSION["usuario"]["claveC"]=$cade;
            
           header('Location:coordinador.php'); 
        }else{
            header('Location:index.php'); 
        }
    }else{
        $Dinicio = "SELECT count(correo) as val FROM docentes where correo=".$cade;
    $ejecutadoD = $mysqli->query($Dinicio);
    $clave_docente=mysqli_fetch_array($ejecutadoD);

    if($clave_docente['val']==1){
    $Dinicio = "SELECT correo FROM docentes where correo=".$cade;
    $ejecutadoD = $mysqli->query($Dinicio);
    $clave_docente=mysqli_fetch_array($ejecutadoD);
    $claveD=$clave_docente['correo'];
    if ($cade==$claveD) {
            session_start();
            $_SESSION["usuario"]["claveD"]=$cade;
            
           header('Location:eventos_Docentes.php'); 
        }else{
            header('Location:index.php'); 
        }
    }else{


    }


    }
        
	}
	//echo( $email );

} else { //SI NO SE LOGUEO PIDE QUE SE LOGUEE
	$authUrl = $gClient->createAuthUrl();
	$output = '<a href="' . filter_var( $authUrl, FILTER_SANITIZE_URL ) . '"><img  id="gmail" src="imagenes/gmai.png" alt=""/></a>';
}
?>
<?php header('Content-Type: text/html; charset=UTF-8'); ?>


<html>

<head>
	<meta charset="utf-8" http-equiv="Content-Type">
		<link href="css/bootstrap.min.css" rel="stylesheet">
                    <link href="estilos.css" rel="stylesheet">
                    <link rel="stylesheet" href="alertify/css/alertify.min.css" />
                <link rel="stylesheet" href="alertify/css/themes/default.min.css" />
                <script src="alertify/alertify.min.js"></script>
	<title>LOG IN</title>
	<style type="text/css">
		h3 {
			font-family: Arial, Helvetica, sans-serif;
		}
		#externo{
			border: 2px solid #050505;
						
			color: white;
			text-align: center;
			padding: 20px;
			border-radius: 30px;
			margin: 50 auto;
			height: auto;
			width: 400px;
			
			background-color: #06490E;
		}
		
		#interno{
			color: white;
			margin: 0 auto;
			height: auto;
			padding: 10px;
			width: 300px;
		}
		
		#gmail{
			height: 30px;
			width: 30px;
			
		}

        
	</style>
</head>

<body>
        
        <section class="vh-100">
            <div class="container-fluid ">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-md-9 col-lg-6 col-xl-5">
                        <img alt="Sample image" class="img-fluid" src="imagenes/itesa.png">
                        </img>
                    </div>
                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <form>
                            <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                                <p class="lead fw-normal mb-0 me-3" >
                                    Continuar con google  <?php echo $output; ?>
                                </p>
                            </div>
                            <div class="divider d-flex align-items-center ">
                                <p class="text-center fw-bold mx-3 mb-0">
                                    O
                                </p>
                            </div>
                            <div>
                                <br>
                                </br>
                            </div>
                            <!-- User input -->
                            <div class="form-outline mb-4">
                                <label class="form-label">
                                    Usuario:
                                </label>
                                <input class="form-control form-control-lg" id="txtUsuario" inputmode="numeric" placeholder="Ingresa tu numero de trabajador o matricula" required="" style="text-align: center;" type="text"/>
                                <div class="invalid-feedback text-start">
                                    Debes usar tu numero de trabajador siendo coordinador.
                                </div>
                            </div>
                            <!-- Password input -->
                            <div class="form-outline mb-3">
                                <label class="form-label">
                                    Contraseña:  
                                </label>
                                <input class="form-control form-control-lg" id="txtPass" placeholder="********" required="" style="text-align: center;" type="password"/>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Checkbox -->
                                <div class="form-check mb-0">
                                    <label class="form-check-label" for="form2Example3">
                                        ¿Olvidaste tu contraseña?
                                    </label>
                                </div>
                                <a class="text-body" href="recuperar.html">
                                    Recuperar contraseña
                                </a>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <label id="mensaje"></label>
                             </div>
                            <div class="text-center text-lg-start mt-4 pt-2">
                                <button class="btn btn-primary btn-lg btn_login" style="padding-left: 2.5rem; padding-right: 2.5rem;" type="button" id="entrar">
                                    <img class=" img-fluid" height="20px" src="imagenes/inlog.png" width="20px">
                                    </img>
                                    Ingresar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <footer style="width:100%; margin-left: 0px;">
            <div class="copyright" style="background-color: black;">
                <div class="container-fluid" style="background-color: black; color: #bbdefb;">
                    <div class="row">
                        <div class="col-lg-4 col-sm-4" style="text-align:center;">
                            <b>
                                <br>
                                    Página Oficial: itesa.edu.mx
                                </br>
                            </b>
                        </div>
                        <div class="col-lg-4 col-sm-4" style="text-align:center;">
                            <b>
                                <br>
                                    Teléfono: 01 748-912-4450
                                </br>
                            </b>
                        </div>
                        <div class="col-lg-4 col-sm-4">
                            <center>
                                <img alt="Sample " class="img-fluid" id="img-pie" src="imagenes/hidalgo.png"/>
                            </center>
                        </div>
                    </div>
                </div>
            </div>

        </footer>

        <script src="js/jquery.js"> </script>
        <script type="text/javascript">
            $("#txtUsuario").on("input", function(){
                $(this).removeClass("is-invalid");
            });

            $("#entrar").click(function(){
            let nombre = $("#txtUsuario").val().trim();
            let contraseña = $("#txtPass").val();
            let esCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(nombre);
                
            if (nombre=="") {
                $("#txtUsuario").removeClass("is-invalid");
                alertify.error("Falta el nombre");
            }
            else if (esCorreo) {
                $("#txtUsuario").addClass("is-invalid").focus();
            }
            else if (contraseña=="") {
                alertify.error("Falta la contraseña");
            }
            else {
                let parametros={"nombre":nombre, "contraseña":contraseña};

                $.post("validar.php", parametros, 
                function(resultado){
                    if (resultado=="estudiante") {
                        location.href="eventos_Estudiantes.php";
                    }else if (resultado=="docente"){
                        location.href="eventos_Docentes.php";
                    }else if (resultado=="coordinador"){
                        location.href="coordinador.php";
                    }
                    else {
                        $("#mensaje").html(resultado);
                    }
                });
            }
        });
        </script> 

    
</body>

</html> 


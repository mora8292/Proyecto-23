<?php
  session_start();
  if (isset($_SESSION["usuario"]["clave_C"])!= ''){
?>
<?php 
header('Content-Type: text/html; charset=UTF-8');

?>
<html>
<head>
        <meta charset="utf-8">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="estilos.css">	
	<title> COORDINADOR	 </title> 
</head>
<body>
<section >
	<div class="container-longer">
		<div class="row">
			<div class="col-lg-3 col-sm-3 imagen">
				
				<img class="img img-fluid" src="imagenes/itesa.png" width="150px" height="60px">

     		</div>
     		
     		<div class="col-lg-6 col-sm-6 sup">
				
				<p style="text-align: center; margin-top: 30px; color: black;"><b>INSTITUTO TECNOLÓGICO SUPERIOR DEL ORIENTE DEL ESTADO DE HIDALGO </b></p>
     		</div>

     		<div class="col-lg-3 col-sm-3 imagen">
				<img class="img img-fluid" src="imagenes/tec.png"  width="150px" height="60px">
     		</div>
     	 </div> 
     </div>


   	<div class="container">
   		<div class="row">
     	 	<div class="col-lg-12 col-sm-12" style="height: 40px;">	 				
     		</div>
   		</div>

   		<div class="row">
   			<div class="col-lg-2 col-sm-2">
   				
   			</div>

   			<div class="col-lg-8 col-sm-8">
   				<div class="row">
   					<div class="col-lg-3 col-sm-3">
   						<img class=" img-fluid" src="imagenes/Persona.png" width="150px" height="60px">
     	 				
   					</div>
   					<div class="col-lg-9 col-sm-9">
     	 				<label><h5><br> <?php include "info_coordinador.php"?> </h5><hr> </label>
   					</div>
     	 		</div>

     	 	</div>
          <div class="col-lg-2 col-sm-2">
          
        </div>
   				
   			</div>


   			<div class="row">
     	 		<div class="col-lg-12 col-sm-12" style="height: 30px;">	 				
     			</div>
   			</div>

   			<div class="row">
     	 		<div class="col-lg-1 col-sm-1">	 				
     			</div>
     			<div class="col-lg-4 col-sm-4 textlogin">
     			<button class="btn btn_coordinador btn-secondary me-md-2 btn-g" type="button" onclick="Generar()"><img class=" img-fluid" src="imagenes/Generar_Evento.png" width="100px" height="40px"> </img> <p><b>Generador de Eventos</b></p></button>	 				
     			</div>
     			<div class="col-lg-2 col-sm-2">	 				
     			</div>
     			<div class="col-lg-4 col-sm-4 textlogin">
     			<button class="btn btn_coordinador btn-secondary me-md-2 btn-g" type="button" onclick="MEventos()"><img class=" img-fluid" src="imagenes/Calendario.png" width="100px" height="40px"> </img> <p><b>Mis Eventos</b></p></button>	 				
     			</div>
     			<div class="col-lg-1 col-sm-1">	 				
     			</div>
   			</div>

   			<div class="row">
     	 		<div class="col-lg-12 col-sm-12" style="height: 40px;">

     			</div>
   			</div>

   			<div class="row">
     	 		<div class="col-lg-1 col-sm-1">	 				
     			</div>
     			<div class="col-lg-4 col-sm-4 textlogin">
     			<button class="btn btn_coordinador btn-secondary me-md-2 btn-g" type="button" onclick="reportes()"><img class=" img-fluid" src="imagenes/Registro.png" width="100px" height="40px"><p><b>Reporte de Eventos</b></p></button>	 				
     			</div>
     			<div class="col-lg-2 col-sm-2">

     			</div>
     			<div class="col-lg-4 col-sm-4 textlogin">
     			<button class="btn btn_coordinador btn-secondary me-md-2 btn-g" type="button" onclick="scan()"><img class=" img-fluid" src="imagenes/scaner.png" width="100px" height="40px"><p><b>Scanner</b></p></button>	 	
    
          </div>			
     			</div>
     			<div class="col-lg-1 col-sm-1">	 				
     			</div>
   			</div>

   		</div>
   		
   	</div>
    <div class="row">
            <div class="col-lg-12 col-sm-12" style="height: 40px;">                 
            </div>
        </div>
    
    <div class="row">
    
    <div  class="col-lg-12 col-sm-12 textlogin" style="text-align: center;">
      
        
            <a class="btn btn-primary btn_login" style="padding-left: 2.5rem; padding-right: 2.5rem; text-align: center;" href="destruir.php"> <img class=" img-fluid" height="30px" src="imagenes/regresa.png" width="25px" style="text-align: center;"> Cerrar Sesión</a>  

    </div>
    
  </div>
 

   	<div class="row">
     	 		<div class="col-lg-12 col-sm-12" style="height: 120px;">

     			</div>
   			</div>
  </section>
</body>

<script src="js/jquery.js">
</script>
<script src="js/qr.js">
</script>
<script type="text/javascript">
    function scan() {
        window.location = "p.php";
}
    function Generar() {
      window.location = "Generador de eventos.php";
    }

    function MEventos(){
      window.location = "mis_eventos.php";
    }
    function reportes(){
      window.location = "reportes.php";
    }
</script>

<script type="text/javascript">
 </script>

<footer  style="width:100%; margin-left: 0px;">
<div class="copyright" style="background-color: black;">
    <div class="container-fluid" style="background-color: black; color: #bbdefb;">
      <div class="row">
        <div class="col-lg-4 col-sm-4" style="text-align:center;">
           <b><br>Página Oficial: itesa.edu.mx</b> 
        </div>
        <div class="col-lg-4 col-sm-4" style="text-align:center;">
          <b> <br>Teléfono: 01 748-912-4450</b>
        </div>
        <div class="col-lg-4 col-sm-4" >
          <center><img src="imagenes/hidalgo.png"  class="img-fluid" alt="Sample " id="img-pie"></center>
        </div>
      </div>
    </div>
</div>
</footer>
<style type="text/css">
    html {
  min-height: 100%;
  position: relative;
}
body {
  margin: 0;
  margin-bottom: 80px;
}
footer {
  background-color: black;
  position: absolute;
  bottom: 0;
  width: 100%;
  color: white;
}
</style>
</html>

<?php
}else{
header("Location: index.php");
}
?>

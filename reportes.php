<?php
  session_start();
  if (($_SESSION["usuario"]["clave_C"])!= ''){
?>

<html>
<head> 
	<meta charset="utf-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="estilos.css">	
  <script src="js/jquery.js"></script>
  <script src="alertify/alertify.min.js"></script>
  <link rel="stylesheet" href="alertify/css/alertify.min.css" />
  <link rel="stylesheet" href="alertify/css/themes/default.min.css" />
                

	<title> Reportes Excel y PDF	 </title>
</head>
<body>
  <section>
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

    <div class="container-longer">

      <div class="row">
     	 	<div class="col-lg-12 col-sm-12" style="height: 40px;">	 				
      	</div>
   		</div>
  
      <div class="row">
        <div class="col-lg-3 col-sm-3 ">
        </div> 
          
        <div class="col-lg-6 col-sm-6 ">
          <div class="row">
            <div class="col-lg-4">
              <h3>
                Selecciona el evento
              </h3>
              <form action="PDF_Excel.php" method="post">
                <select id="Eventos" name="Eventos" style="width: 150px; height: 30px; text-align: center;">    
                </select>
              
                <div class="row">
                  <div class="col-lg-12 col-sm-12" style="height: 120px;">	 				
                  </div>
                </div>
             
          
                <button class="btn btn-primary btn-lg btn_login" onclick="regresa()" style="padding-left: 2.5rem; padding-right: 2.5rem; text-align: center;" type="button">
                  <img class=" img-fluid" height="30px" src="imagenes/regresa.png" width="25px" style="text-align: center;">
                  </img>
                  Regresar
                </button>
            </div>
              <div class="col-lg-2">
              </div>
              <div class="col-lg-6">
                <div class="row">
                  <button id="excel" name="excel" type="submit" class="btn btn_coordinador btn-secondary me-md-2 btn-g" type="button" ><img class=" img-fluid" src="imagenes/excel.png" width="40px" height="50px"> </img> <p><b>Excel</b></p></button>	 				
                </div>
                <div  class="col-lg-12 col-sm-12" style="height: 10px;">	 				
                </div>
                <div class="row">
                <button id="pdf" name="pdf" type="submit" class="btn btn_coordinador btn-secondary me-md-2 btn-g" type="button"><img class=" img-fluid" src="imagenes/pdf.png" width="40px" height="50px"> </img> <p><b>PDF</b></p></button>	 				
                </div>
                <br>
                <div class="row">
                <button id="reporte" name="reporte" type="submit" class="btn btn_coordinador btn-secondary me-md-2 btn-g" type="button"><img class=" img-fluid" src="imagenes/reporte.png" width="40px" height="50px"> </img> <p><b>  Reporte</b></p></button>	 				
                </div>
              </form>
            </div>
          </div>
        
        </div>

        <div class="col-lg-3 col-sm-3 " id="resultado">
          
        </div>
      </div> 
    </div>
  
  </section>
</body>
<script>
  $.ajax({type: "POST",
    url: "recuperar.php",
      success: function(res){
        $("#Eventos").html(res);
      }
  });

  /*
  $("#excel").click(function(){

    var evento = $("#Eventos").val();

    $.ajax({type: "POST",
        url: "excel.php",
        data: {evento: evento},
       success: function(res){
          alertify.success('Se ha creado el documento en Excel :D',10)
          $("#resultado").html(res);
          //Creación de modak para avisar que ya descargo xd 
        }
      });
  });
  */
   

  function regresa() {
        history.go(-1);
    }

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

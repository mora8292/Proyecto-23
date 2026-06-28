<?php
    session_start();
    if (($_SESSION["usuario"]["clave_D"])!= ''){
        $matricula=$_SESSION["usuario"]["clave_D"];
?>


<html>
    <head>
        <meta charset="utf-8">
            <link href="css/bootstrap.min.css" rel="stylesheet">
                <link href="estilos.css" rel="stylesheet">
                    <title>
                        EVENTOS 
                    </title>
                </link>
            </link>
        </meta>
    </head>
    <body>
        <div class="container-longer">
            <div class="row">
                <div class="col-lg-3 col-sm-3 imagen">
                    <img class="img img-fluid" height="60px" src="imagenes/itesa.png" width="150px">
                    </img>
                </div>
                <div class="col-lg-6 col-sm-6 sup">
                    <p style="text-align: center; margin-top: 30px; color: white;">
                        INSTITUTO TECNOLÓGICO SUPERIOR DEL ORIENTE DEL ESTADO DE HIDALGO
                    </p>
                </div>
                <div class="col-lg-3 col-sm-3 imagen">
                    <img class="img img-fluid" height="60px" src="imagenes/tec.png" width="150px">
                    </img>
                </div>
            </div>
        </div>

        <div class="row">
                <div class="col-lg-12 col-sm-12" style="height: 20px;">
                </div>
            </div>
        <div class="container">
            <div class="row">
            <div class="col-lg-3 col-sm-3">
                
            </div>

            <div class="col-lg-6 col-sm-6">
                <div class="row">
                    <div class="col-lg-4 col-sm-4">
                        <img class=" img-fluid" src="imagenes/Persona.png" width="150px" height="60px">
                        
                    </div>
                    <div class="col-lg-8 col-sm-8">
                        <label><h5><br> <?php include "info._docente.php"?> </h5><hr> </label>
                    </div>
                </div>

            </div>
                
            </div>

            <div class="col-lg-3 col-sm-3">
                
            </div>
        </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12" style="height: 40px;">
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-sm-3">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin">
                            <h3>
                                Selecciona evento:
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin" style="height: 20px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin">
                            <form action ="" method="post">

                            <select id="Eventos" style="width: 150px; height: 30px; text-align: center;" onclick="getData()">
                                <?php include "recuperar.php" ?> 
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 tabla_eventos">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin">
                            <h3>
                                Información del evento:
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin" style="height: 20px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin">
                            <table class="table">
                                <thead>
                                    <th>Nombre</th>
                                    <th>Fecha y hora</th>
                                    <th>Lugar</th>
                                </thead>
                                <tbody id="content">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-3">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin">
                            <button class="btn_eventos btn-primary me-md-2 btn_eventos" id="generaQR" type="button">
                                Generar QR
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin" style="height: 30px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin">
                            <center>
                                <div class="qr">
                                </div>
                            </center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin" style="height: 30px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin">
                            <button class="btn_eventos_D btn-primary me-md-2 " onclick="scan()" type="button">
                                <img class=" img-fluid" height="25px" src="imagenes/Scaner.png" width="20px" style="text-align: center;">
                                Scaner
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script src="js/jquery.js">
</script>
<script src="js/qr.js">
</script>
<script type="text/javascript">
    function scan() {
        window.location = "p.php";
    }
$.ajax({type: "POST",
                    url: "recuperar.php",
                    success: function(res){
                        //res;
                        $("#Eventos").html(res);
                    }
            })
    function getData(){
        let select = document.getElementById("Eventos").value;
        let content = document.getElementById("content");
        let url ="load.php" ;
        let formaData = new FormData() ;
        formaData.append('Eventos',select); //añadir datos de eventos? 

        fetch(url,{
            method:"POST", 
            body: formaData
        }).then(response=> response.json())
        .then(data=>{
            content.innerHTML=data//Poner datos en el tbody
        }).catch(err => console.log(err))
    }
    
    const   qrfinal = document.querySelector('.qr'),
        evento=document.getElementById('Eventos');
        
        var matricula=<?php echo $matricula; ?>
    
     $("#generaQR").click(function(){
            
            if(evento.value != ""){
        if(qrfinal.childElementCount == 0){
          genera(evento);  
        } else { 
          qrfinal.innerHTML = "";          
          genera(evento);
        }
      } else{
        qrfinal.style = "display none";
      }
            function genera(){
                var qrcode = new QRCode(qrfinal, {
                text: evento.value+' % '+matricula,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
                });
            };
        });

     
</script>

<div class="row">
    
    <div  class="col-lg-12 col-sm-12 textlogin">
        <br>
        
          <a class="btn btn-primary btn_login" style="padding-left: 2.5rem; padding-right: 2.5rem; text-align: center;" href="destruir.php"> <img class=" img-fluid" height="30px" src="imagenes/regresa.png" width="25px" style="text-align: center;"> Cerrar Sesión</a>  
    </div>
    
</div>
 
 
<script type="text/javascript">
    
 </script>

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
<style type="text/css">
    html {
  min-height: 100%;
  position: relative;
}
body {
  margin: 0;
  margin-bottom: 200px;
}
footer {
  background-color: black;
  position: absolute;
  bottom: 0;
  width: 100%;
  color: white;
}
</style>
<?php
}else{
header("Location: index.php");
}
?>

<?php
    session_start();
    if (($_SESSION["usuario"]["matricula"])!= ''){
        $matricula=$_SESSION["usuario"]["matricula"];
?>


<html>
    <head>
        <meta charset="utf-8">
            <link href="css/bootstrap.min.css" rel="stylesheet">
                <link href="estilos.css" rel="stylesheet">
                    <title>
                        EVENTOS ESTUDIANTES
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
                    <img class="img-fluid" src="obtener_imagen_EveEstudiantes.php" width="150" height="150">                        
                    </div>
                    <div class="col-lg-9 col-sm-9">
                        <label><h5><br> <?php include "info_Estudiantes.php"?> </h5><hr> </label>
                    </div>
                </div>

            </div>
                
            </div>

            <div class="col-lg-2 col-sm-2">
                
            </div>
   
            <div class="row">
                <div class="col-lg-12 col-sm-12" style="height: 40px;">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-sm-3">
                    <div class="row">
                        <div class="col-lg-12 col-sm-10 textlogin" style="text-align:left;">
                        <h3 style="margin-left: -30px;">Selecciona evento:</h3>

                        </div>
                        <div class="row">
                            <div class="col-lg-2">
                            </div>
                            <!-- -->
                            <div class="col-lg-8 col-sm-8" style="text-align:center;">
                            
                            <select name="eventos" id="Eventos" style="width: 150px; height: -30px; text-align: center; margin-left: -30px;" onclick="getData()">
   
                            <?php include "recuperar.php" ?>
                                </select>
                            
                            </div>
                            <div class="col-lg-2">
                            </div>
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
                <div class="col-lg-3 col-sm-3">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 textlogin">
                            <button class="btn btn-primary btn-lg btn_login" id="generaQR" style="padding-left: 2.5rem; padding-right: 2.5rem; text-align: center;" type="button">
                                <img class=" img-fluid" height="30px" src="imagenes/QR.png" width="25px" style="text-align: center;">
                                </img>
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
                </div>
            </div>
        </div>
        <div class="row">
    
    <div  class="col-lg-12 col-sm-12 textlogin">
        <br>
        
       <a class="btn btn-primary btn_login" style="padding-left: 2.5rem; padding-right: 2.5rem; text-align: center;" href="destruir.php"> <img class=" img-fluid" height="30px" src="imagenes/regresa.png" width="25px" style="text-align: center;"> Cerrar Sesión</a>  

    </div>
    
    </div>


    </body>
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
    <script src="js/jquery.js"></script>
    <script src="js/qr.js">
    </script>
    <script type="text/javascript">

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
//-----------------------------------------------------------------------------

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

     
      //script para mandar a llamar funcion que obtiene valores de consulta mysql
      /*
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
    */

    
    </script>
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
</html>

<?php
}else{
header("Location: index.php");
}
?> 

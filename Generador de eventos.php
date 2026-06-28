<?php
  session_start();
  if (isset($_SESSION["usuario"]["clave_C"])!= ''){
?>
<html>
    <head>
        <meta charset="utf-8">
            <link href="css/bootstrap.min.css" rel="stylesheet">
                <link href="estilos.css" rel="stylesheet">
                <link rel="stylesheet" href="alertify/css/alertify.min.css" />
                <link rel="stylesheet" href="alertify/css/themes/default.min.css" />
                
                <script src="js/multiselect-dropdown.js" ></script>
                
                
                <script src="alertify/alertify.min.js"></script>
                
                <script src="js/jquery.js"></script>
                <script ></script>
                
                    <title>
                        Creador de eventos
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
 
               
                <div class="container" style="width:300px">

                    <div class="row">
                        <div class="col-lg-3 col-sm-3" style="height: 90px;">
                        </div>
                    </div>
        

                    <div class="row">
                        <div class="col-lg-3 col-sm-3 txtEvento">

                            <h3>
                                Evento:
                                <input name="txtEvento" id="evento" placeholder="Nombre del Evento" style="width:270px" type="text">
                                </input>
                            </h3>
                        </div>
                        <div class="col-lg-10 col-sm-12 txtEvento">
                            <h3>
                                Fecha y Hora:
                                <input name="txtFecha" id ="fechaHora" placeholder="Fecha y Hora" style="width:270px" type="datetime-local">
                                </input>
                            </h3>
                        </div>
                        <div class="col-lg-10 col-sm-12 txtEvento">
                            <h3>
                                Lugar:
                                <select  id="lugar" aria-label="Default select example" class="form-select" style="width:270px" name="lugar" >
                                    <option selected="true" disabled="disabled">
                                        Selecciona un lugar
                                    </option>
                                    <option value="Auditorio">
                                        Auditorio
                                    </option>
                                    <option value="Polideportivo">
                                        Polideportivo
                                    </option>
                                    <option value="Otro">
                                        Otro
                                    </option>
                                </select>
                            </h3>
                        </div>
                        <div class="col-lg-3 col-sm-3 txtEvento" id="lugares"></div>
                        <div class="col-lg-12 col-sm-12 txtEvento">
                            <h3>
                                Carreras Asistentes:
                                <select  id="carreras" aria-label="Default select example" class="form-select" style="width:270px" name="carrera" multiple  >
                                    <option value="1">
                                        ISC
                                    </option>
                                    <option value="2">
                                        IIA
                                    </option>
                                    <option value="3">
                                        IE
                                    </option>
                                    <option value="4">
                                        IL
                                    </option>
                                    <option value="5">
                                        IC
                                    </option>
                                    <option value="6">
                                        IM
                                    </option>
                                    <option value="7">
                                        IGE
                                    </option>
                                    <option value="8">
                                        ISA
                                    </option>
                                    <option value="9">
                                        LA
                                    </option>
                                    <option value="10">
                                        LT
                                    </option>
                                    <!--<option value="11">
                                        MSC
                                    </option>
                                    <option value="12">
                                        MCA
                                    </option>-->
                                </select>
                            </h3>
                        <div id = "resultado">

                        </div>
                    </div>
                        
                        <div class="col-lg-12 col-sm-12 txtEvento">
                            <h3>
                                <br>
                                </br>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-sm-12 textlogin">
                    <!-- <a href="#"> -->
                        <input class="btn_login btn-primary me-md-2 btn" value= "Generar Evento"style="padding-left: 2.5rem; padding-right: 2.5rem;" type="submit" id="boton" src="imagenes/mas.png" type="image" />
<div class="row">
    
    <div  class="col-lg-12 col-sm-12 textlogin">
        <br>
        
        <button class="btn btn-primary btn-lg btn_login" onclick="pagcor()" style="padding-left: 2.5rem; padding-right: 2.5rem; text-align: center;" type="button">
            <img class=" img-fluid" height="30px" src="imagenes/regresa.png" width="25px" style="text-align: center;">
            </img>
            Regresar
        </button>
    </div>
    
</div>
 
 
<script type="text/javascript">

    $("#boton").click(function(){        
        
        var evento = $("#evento").val();
        var fechaHora = $("#fechaHora").val();
        var lugar=$("#lugar").val(); 
        
        var carreras = $("#carreras").val();
        console.log(carreras);

        if($("#lugar").val() == "Otro"){
            var lugar =$("#lugarOtro").val() ; 
        }
       


        $.ajax({type: "POST",
                url: "insertar.php",
                data: {evento: evento, fechaHora: fechaHora, lugar:lugar,carreras:carreras},
                success: function(res){
                    //res;
                    $("#resultado").html(res);
                }
        }).fail(function(){
            alertify.error("Error al enviar los valores :(",10);
        });


    });

    $("#lugar").change(function(){
        var lugar = $("#lugar").val();

        if(lugar == "Otro"){
            
            lugares.innerHTML=' <h3> <input type="text" id= "lugarOtro" placeholder="Especifica el lugar" style="width:270px" > </h3> ';
        }else{
            lugares.innerHTML = '<h3> </h3>';
        }
    });

    function pagcor() {
        history.go(-1);
    }
 </script>
                        <!--   // <img class=" img-fluid" height="25px" src="imagenes/mas.png" width="25px">
                            //</img>
                            
                            Generar evento
                        -->

                        
                        <!--<input type="submit" value="Guardar" id="boton">-->
                        <!---->
                    </a>
                </div>
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

<?php 
session_start();
if (isset($_SESSION["usuario"]["clave_C"]) != '' || isset($_SESSION["usuario"]["clave_D"]) != '') {
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Scanner</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="estilos.css" rel="stylesheet">
  <link rel="stylesheet" href="alertify/css/alertify.min.css" />
  <link rel="stylesheet" href="alertify/css/themes/default.min.css" />
  <script src="alertify/alertify.min.js"></script>
  <style>
    html { min-height: 100%; position: relative; }
    body { margin: 0; margin-bottom: 200px; }
    footer { background-color: black; position: absolute; bottom: 0; width: 100%; color: white; }

    .alumno-info {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.5);
      z-index: 1000;
      text-align: center;
      display: none;
      max-width: 90%;
      width: 400px;
    }

    .alumno-foto {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #007bff;
      margin-bottom: 15px;
    }

    .alumno-nombre {
      font-size: 1.3rem;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .alumno-matricula {
      font-size: 1.1rem;
      color: #555;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="container-longer">
    <div class="row">
      <div class="col-lg-3 col-sm-3 imagen">
        <img class="img img-fluid" height="60px" src="imagenes/itesa.png" width="150px">
      </div>
      <div class="col-lg-6 col-sm-6 sup">
        <p style="text-align: center; margin-top: 30px; color: white;">
          INSTITUTO TECNOLÓGICO SUPERIOR DEL ORIENTE DEL ESTADO DE HIDALGO
        </p>
      </div>
      <div class="col-lg-3 col-sm-3 imagen">
        <img class="img img-fluid" height="60px" src="imagenes/tec.png" width="150px">
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row justify-content-center align-items-center">
      <div class="col-lg-3"></div>
      <div class="col-lg-6">
        <h1 style="text-align: center;">SCANNER</h1>
      </div>
      <div class="col-lg-3"></div>
    </div>

    <div class="row">
      <div class="col-lg-3"></div>
      <div class="col-lg-6">
        <center>
          <video id="video" class="p-1 border" style="width:100%; border: 3px solid #007bff; border-radius: 10px;" autoplay playsinline></video>
        </center>
      </div>
      <div class="col-lg-3"></div>
    </div>

    <div class="row">
      <div class="col-lg-12 col-sm-12 textlogin">
        <br>
        <button class="btn btn-primary btn-lg btn_login" onclick="iniciod()" style="padding-left: 2.5rem; padding-right: 2.5rem;" type="button">
          <img class="img-fluid" height="30px" src="imagenes/regresa.png" width="25px">
          Regresar
        </button>
      </div>
    </div>
  </div>

  <div id="alumnoContainer" class="alumno-info">
    <img id="alumnoFoto" class="alumno-foto" src="imagenes/Persona.png" alt="Foto del alumno">
    <div id="alumnoNombre" class="alumno-nombre"></div>
    <div id="alumnoMatricula" class="alumno-matricula"></div>
  </div>

  <footer>
    <div class="copyright">
      <div class="container-fluid" style="background-color: black; color: #bbdefb;">
        <div class="row">
          <div class="col-lg-4 col-sm-4 text-center">
            <b><br>Página Oficial: itesa.edu.mx</b>
          </div>
          <div class="col-lg-4 col-sm-4 text-center">
            <b><br>Teléfono: 01 748-912-4450</b>
          </div>
          <div class="col-lg-4 col-sm-4 text-center">
            <img alt="Sample" class="img-fluid" id="img-pie" src="imagenes/hidalgo.png" />
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script src="js/jquery.js"></script>
  <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

  <script>
    function iniciod() {
      history.go(-1);
    }

    var scanner = new Instascan.Scanner({
      video: document.getElementById('video'),
      scanPeriod: 1,
      mirror: false
    });

    var scannerActivo = true;
    var timeoutID = null;

    Instascan.Camera.getCameras().then(function (cameras) {
      if (cameras.length > 0) {
        let backCam = cameras.find(cam => cam.name.toLowerCase().includes('back')) || cameras[0];
        scanner.start(backCam).then(() => {
          console.log("Cámara iniciada");
        }).catch(e => {
          console.error("No se pudo iniciar la cámara:", e);
          alertify.error("Error: " + e.name + " - " + e.message);
        });
      } else {
        console.error('No se encontraron cámaras');
        alertify.error('No se encontraron cámaras');
      }
    }).catch(function (e) {
      console.error(e);
      alertify.error("ERROR: " + e.name + " - " + e.message);
    });

    scanner.addListener('scan', function (contenido) {
      if (!scannerActivo) return;

      console.log("Contenido leído: " + contenido);
      let partes = contenido.split(' % ');
      if (partes.length < 2) {
        alertify.error("Formato de QR inválido");
        return;
      }

      let id_evento = partes[0];
      let id_matricula = partes[1];
      scannerActivo = false;

      $('#alumnoFoto').attr('src', 'obtener_imagen.php?matricula=' + id_matricula + '&t=' + new Date().getTime());

      $.ajax({
        url: 'obtener_datos_estudiante.php',
        type: 'GET',
        dataType: 'json',
        data: { matricula: id_matricula },
        success: function(data) {
          if (data.status === 'success') {
            $('#alumnoNombre').text(data.nombre);
            $('#alumnoMatricula').text('Matrícula: ' + id_matricula);
          } else {
            $('#alumnoNombre').text('Estudiante no encontrado');
            $('#alumnoMatricula').text('Matrícula: ' + id_matricula);
            alertify.warning("No se encontró información del estudiante");
          }

          $('#alumnoContainer').fadeIn();

          $.post("registrar.php", {
            mat: id_matricula,
            ev: id_evento
          }, function(resultado) {
            if (resultado == "si") {
              alertify.success("Alumno registrado. Escanea el siguiente.");
            } else {
              alertify.error("Error al registrar alumno");
            }

            scannerActivo = true;
          }).fail(function() {
            alertify.error("Error al registrar, intente nuevamente");
            scannerActivo = true;
          });

          clearTimeout(timeoutID);
          timeoutID = setTimeout(function() {
            $('#alumnoContainer').fadeOut();
            $('#alumnoNombre').text('');
            $('#alumnoMatricula').text('');
            $('#alumnoFoto').attr('src', 'imagenes/Persona.png');
          }, 120000); // 2 minutos
        },
        error: function() {
          $('#alumnoNombre').text('Error al cargar datos');
          $('#alumnoMatricula').text('Matrícula: ' + id_matricula);
          $('#alumnoContainer').fadeIn();
          alertify.error("Error al obtener datos del estudiante");
          scannerActivo = true;
        }
      });
    });
  </script>
</body>
</html>
<?php
} else {
  header("Location: index.php");
}
?>

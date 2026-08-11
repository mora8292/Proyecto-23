<?php
require_once "auth.php";
iniciarSesionSiHaceFalta();
if (usuarioPuedeEntrar(["Coordinador", "Docente"])) {
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

      .alumno-info {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
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

      .btn-cerrar {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 35px;
        height: 35px;
        border: none;
        border-radius: 50%;
        background: #dc3545;
        color: #fff;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        transition: .2s;
      }

      .btn-cerrar:hover {
        background: #bb2d3b;
        transform: scale(1.1);
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
            <div id="reader" style="width:100%;border:3px solid #007bff;border-radius:10px;"></div>
          </center>
        </div>
        <div class="col-lg-3"></div>
      </div>

      <div class="row">
        <div class="col-lg-12 col-sm-12 textlogin">
          <br>
          <button class="btn btn-primary btn-lg btn_login" onclick="iniciod()"
            style="padding-left: 2.5rem; padding-right: 2.5rem;" type="button">
            <img class="img-fluid" height="30px" src="imagenes/regresa.png" width="25px">
            Regresar
          </button>
        </div>
      </div>
    </div>

    <div id="alumnoContainer" class="alumno-info">

      <button id="cerrarAlumno" class="btn-cerrar">
        &times;
      </button>

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
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
      function iniciod() {
        history.go(-1);
      }

      let scannerActivo = true;
      let timeoutID = null;

      const html5QrCode = new Html5Qrcode("reader");

      let procesandoQR = false;

      function cerrarAlumno() {

        $("#alumnoContainer").fadeOut();

        $("#alumnoNombre").text("");

        $("#alumnoMatricula").text("");

        $("#alumnoFoto").attr("src", "imagenes/Persona.png");

        clearTimeout(timeoutID);

        scannerActivo = true;
        procesandoQR = false;

        html5QrCode.resume();

      }

      $("#cerrarAlumno").on("click", function() {

        cerrarAlumno();

      });

      Html5Qrcode.getCameras()
        .then(function(cameras) {

          if (cameras.length == 0) {

            alertify.error("No se encontraron cámaras");
            return;

          }

          const esMovil = /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|Mobile/i.test(navigator.userAgent);

          let camara = null;

          if (esMovil) {

            // Intenta encontrar la cámara trasera por nombre
            camara = cameras.find(c =>
              c.label.toLowerCase().includes("back") ||
              c.label.toLowerCase().includes("rear") ||
              c.label.toLowerCase().includes("environment")
            );

            // Si no la encuentra, usa la última (normalmente la trasera)
            if (!camara) {
              camara = cameras[cameras.length - 1];
            }

          } else {

            // En PC intenta encontrar una webcam integrada o USB
            camara = cameras.find(c =>
              c.label.toLowerCase().includes("integrated") ||
              c.label.toLowerCase().includes("webcam") ||
              c.label.toLowerCase().includes("usb")
            );

            // Si no encuentra ninguna, usa la primera
            if (!camara) {
              camara = cameras[0];
            }

          }

          html5QrCode.start(

            camara.id,

            {

              fps: 10,

              qrbox: {
                width: 250,
                height: 250
              },

              aspectRatio: 1.0

            },

            async function(decodedText) {

                if (!scannerActivo || procesandoQR)
                  return;

                procesandoQR = true;
                scannerActivo = false;

                try {
                  await html5QrCode.pause(true);
                } catch (e) {
                  console.log(e);
                }

                console.log(decodedText);

                let partes = decodedText.split(" % ");

                if (partes.length < 2) {

                  alertify.error("Formato QR inválido");
                  scannerActivo = true;
                  return;

                }

                let id_evento = partes[0];
                let id_matricula = partes[1];

                $("#alumnoFoto").attr(
                  "src",
                  "obtener_imagen.php?matricula=" +
                  id_matricula +
                  "&t=" +
                  new Date().getTime()
                );

                $.ajax({

                  url: "obtener_datos_estudiante.php",

                  type: "GET",

                  dataType: "json",

                  data: {
                    matricula: id_matricula
                  },

                  success: function(data) {

                    if (data.status == "success") {

                      $("#alumnoNombre").text(data.nombre);

                      $("#alumnoMatricula").text(
                        "Matrícula: " + id_matricula
                      );

                    } else {

                      $("#alumnoNombre").text(
                        "Estudiante no encontrado"
                      );

                      $("#alumnoMatricula").text(
                        "Matrícula: " + id_matricula
                      );

                    }

                    $("#alumnoContainer").fadeIn();

                    $.post(

                      "registrar.php",

                      {

                        mat: id_matricula,

                        ev: id_evento

                      },

                      function(resultado) {

                        if (resultado == "si") {

                          alertify.success(
                            "Alumno registrado"
                          );

                        } else if (resultado == "inactivo") {

                          alertify.error(
                            "Alumno inactivo"
                          );

                        } else if (resultado == "duplicado") {

                          alertify.error(
                            "Registro duplicado"
                          );

                        } else {

                          alertify.error(
                            "Error al registrar"
                          );

                        }

                        scannerActivo = true;

                      }

                    ).fail(function() {

                      alertify.error(
                        "Error al registrar"
                      );

                      scannerActivo = true;
                      procesandoQR = false;

                      setTimeout(function() {

                        html5QrCode.resume();

                      }, 1000);

                    });

                    clearTimeout(timeoutID);

                    timeoutID = setTimeout(function() {

                      cerrarAlumno();

                    }, 120000);

                  },

                  error: function() {

                    alertify.error(
                      "Error al obtener datos"
                    );

                    scannerActivo = true;
                    procesandoQR = false;

                    setTimeout(function() {

                      html5QrCode.resume();

                    }, 1000);

                  }

                });

              },

              function(error) {

              }

          );

        })
        .catch(function(err) {

          console.log(err);

          alertify.error(err);

        });
    </script>
  </body>

  </html>
<?php
} else {
  header("Location: index.php");
}
?>

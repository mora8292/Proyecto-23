<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MIS EVENTOS</title>
    
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <script src="js/multiselect-dropdown.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <!-- Toastr para notificaciones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <style>
        body {
            margin: 0;
            padding-bottom: 100px;
            position: relative;
            min-height: 100vh;
        }
        
        .container-longer {
            padding: 20px 0;
        }
        
        .tabla_eventos {
            margin: 30px 0;
        }
        
        footer {
            background-color: #000;
            color: white;
            padding: 20px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
            border-top: none !important;
        }
        
        .modal-content {
            border-radius: 0;
        }
        
        .btn_login {
            background-color: #337ab7;
            color: white;
            margin: 20px 0;
        }
        
        #notification-area {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 300px;
            z-index: 9999;
        }
        
        .btn-action {
            margin: 0 5px;
        }
    </style>

</head>
<body>
    <!-- Área de notificaciones -->
    <div id="notification-area"></div>

    <section>
        <!-- Encabezado institucional -->
        <div class="container-longer">
            <div class="row">
                <div class="col-lg-3 col-sm-3 text-center">
                    <img class="img-fluid" src="imagenes/itesa.png" style="max-height: 60px; max-width: 150px;">
                </div>
                <div class="col-lg-6 col-sm-6 text-center">
                    <p style="margin-top: 30px; color: black; font-weight: bold;">
                        INSTITUTO TECNOLÓGICO SUPERIOR DEL ORIENTE DEL ESTADO DE HIDALGO
                    </p>
                </div>
                <div class="col-lg-3 col-sm-3 text-center">
                    <img class="img-fluid" src="imagenes/tec.png" style="max-height: 60px; max-width: 150px;">
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="container">
            <!-- Tabla de eventos -->
            <div class="row">
                <div class="col-lg-12 col-sm-12 tabla_eventos">
                    <h3 class="text-center">Información del evento:</h3>
                    
                    <table class="table table-bordered" id="Eventos">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Fecha y hora</th>
                                <th>Lugar</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="content">
                            <!-- Contenido dinámico -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Botón de regresar -->
            <div class="row">
                <div class="col-lg-12 text-center">
                    <button class="btn btn-lg btn_login" onclick="pagcor()">
                        <img src="imagenes/regresa.png" style="height: 30px; width: 25px; vertical-align: middle;">
                        Regresar
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Modificar -->
    <div class="modal fade" id="modificar" tabindex="-1" role="dialog" aria-labelledby="modificarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modificarModalLabel">Modificar Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-modificar">
                    <div class="modal-body">
                        <input type="hidden" name="id_evento" id="id_evento">
                        
                        <div class="form-group">
                            <label for="evento">Nombre del Evento</label>
                            <input type="text" class="form-control" id="evento" name="evento" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="fechayhora">Fecha y Hora</label>
                            <input type="datetime-local" class="form-control" id="fechayhora" name="fechayhora" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lugar">Lugar</label>
                            <select class="form-control" id="lugar" name="lugar" required>
                                <option value="" disabled selected>Selecciona un lugar</option>
                                <option value="Auditorio">Auditorio</option>
                                <option value="Polideportivo">Polideportivo</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="lugares"></div>
                        
                        <div class="form-group">
                            <label for="carreras">Carreras Asistentes</label>
                            <select class="form-control" id="carreras" name="carrera" style="width:270px">
                                <option value="1">ISC</option>
                                <option value="2">IIA</option>
                                <option value="3">IE</option>
                                <option value="4">IL</option>
                                <option value="5">IC</option>
                                <option value="6">IM</option>
                                <option value="7">IGE</option>
                                <option value="8">ISA</option>
                                <option value="9">LA</option>
                                <option value="10">LT</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div class="modal fade" id="eliminar" tabindex="-1" role="dialog" aria-labelledby="eliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-eliminar">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="delete_Nombre_Evento">
                        <div class="alert alert-warning">
                            <h5 id="mensaje-eliminacion">¿Estás seguro que deseas eliminar el siguiente evento?</h5>
                            <p><strong>Nombre:</strong> <span id="nombre-evento"></span></p>
                            <p><strong>Fecha:</strong> <span id="fecha-evento"></span></p>
                            <p><strong>Lugar:</strong> <span id="lugar-evento"></span></p>
                            <p class="text-danger">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-sm-4 text-center">
                    <p><strong>Página Oficial: itesa.edu.mx</strong></p>
                </div>
                <div class="col-lg-4 col-sm-4 text-center">
                    <p><strong>Teléfono: 01 748-912-4450</strong></p>
                </div>
                <div class="col-lg-4 col-sm-4 text-center">
                    <img src="imagenes/hidalgo.png" class="img-fluid" style="max-height: 60px;">
                </div>
            </div>
        </div>
    </footer>

    <script>
        $(document).ready(function() {
            // Configuración de Toastr
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000"
            };

            // Cargar eventos al iniciar
            cargarEventos();

            // Manejar cambio en el select de lugar
            $("#lugar").change(function() {
                if ($(this).val() == "Otro") {
                    $("#lugares").html('<input type="text" class="form-control" name="lugar_otro" placeholder="Especifica el lugar" required>');
                } else {
                    $("#lugares").empty();
                }
            });

            // Enviar formulario de modificación
            $("#form-modificar").submit(function(e) {
                e.preventDefault();
                modificarEvento();
            });

            // Enviar formulario de eliminación
            $("#form-eliminar").submit(function(e) {
                e.preventDefault();
                eliminarEvento();
            });
        });

        function cargarEventos() {
            $.ajax({
                url: 'loadcor.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#content').html(data);
                },
                error: function() {
                    toastr.error('Error al cargar los eventos');
                }
            });
        }

        // Función para llenar datos en el modal de modificación
        function llenarModalModificar(id, nombre, fecha, lugar) {
            $('#id_evento').val(id);
            $('#evento').val(nombre);
            $('#fechayhora').val(formatDateTimeForInput(fecha));
            $('#lugar').val(lugar);
            
            // Mostrar campo adicional si el lugar es "Otro"
            if (lugar === "Otro") {
                $("#lugares").html('<input type="text" class="form-control" name="lugar_otro" placeholder="Especifica el lugar" required>');
            } else {
                $("#lugares").empty();
            }
            
            $('#modificar').modal('show');
        }

        // Función para llenar datos en el modal de eliminación
        function llenarModalEliminar(id, nombre, fecha, lugar) {
            $('#delete_Nombre_Evento').val(id);
            $('#nombre-evento').text(nombre);
            $('#fecha-evento').text(fecha);
            $('#lugar-evento').text(lugar);
            
            $('#eliminar').modal('show');
        }

        function modificarEvento() {
            var formData = $('#form-modificar').serialize();
            
            $.ajax({
                url: 'edit.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        $('#modificar').modal('hide');
                        cargarEventos();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Error al conectar con el servidor');
                }
            });
        }

        function eliminarEvento() {
            var id = $('#delete_Nombre_Evento').val();
            
            $.ajax({
                url: 'eliminar.php',
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        $('#eliminar').modal('hide');
                        cargarEventos();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Error al conectar con el servidor');
                }
            });
        }

        function formatDateTimeForInput(dateTimeString) {
            // Convertir la fecha del formato de tabla al formato que acepta input datetime-local
            var date = new Date(dateTimeString);
            return date.toISOString().slice(0, 16);
        }

        function pagcor() {
            window.history.back();
        }

        // Asignar eventos a los botones dinámicamente
        $(document).on('click', '.btn-modificar', function() {
            var fila = $(this).closest('tr');
            var id = fila.data('id');
            var nombre = fila.find('td:eq(0)').text();
            var fecha = fila.find('td:eq(1)').text();
            var lugar = fila.find('td:eq(2)').text();
            
            llenarModalModificar(id, nombre, fecha, lugar);
        });

        $(document).on('click', '.btn-eliminar', function() {
            var fila = $(this).closest('tr');
            var id = fila.data('id');
            var nombre = fila.find('td:eq(0)').text();
            var fecha = fila.find('td:eq(1)').text();
            var lugar = fila.find('td:eq(2)').text();
            
            llenarModalEliminar(id, nombre, fecha, lugar);
        });
    </script>
</body>
</html>

<?php
require_once "auth.php";
require "conexion.php";
require "Classes/PHPExcel/IOFactory.php";

requerirRoles(["Administrador"]);

$mensaje = "";
$errores = [];
$resumen = [
    "procesados" => 0,
    "altas" => 0,
    "reactivados" => 0,
    "actualizados" => 0,
    "bajas" => 0,
    "omitidos" => 0
];

function normalizarTexto($texto)
{
    $texto = trim((string)$texto);
    $transliterado = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    if ($transliterado !== false) {
        $texto = $transliterado;
    }
    $texto = strtolower($texto);
    return preg_replace('/[^a-z0-9]+/', '_', $texto);
}

function leerArchivoAlumnos($archivo, $nombreOriginal)
{
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    if ($extension === "csv") {
        $lector = PHPExcel_IOFactory::createReader("CSV");
    } elseif ($extension === "xls") {
        $lector = PHPExcel_IOFactory::createReader("Excel5");
    } else {
        $lector = PHPExcel_IOFactory::createReader("Excel2007");
    }

    $lector->setReadDataOnly(true);
    $excel = $lector->load($archivo);
    $hoja = $excel->getActiveSheet();
    $filas = $hoja->toArray(null, true, true, true);

    if (count($filas) < 2) {
        return [];
    }

    $encabezados = [];
    foreach ($filas[1] as $columna => $valor) {
        $encabezados[$columna] = normalizarTexto($valor);
    }

    $datos = [];
    foreach ($filas as $indice => $fila) {
        if ($indice == 1) {
            continue;
        }

        $registro = [];
        $tieneDatos = false;

        foreach ($fila as $columna => $valor) {
            $campo = $encabezados[$columna] ?? "";
            if ($campo === "") {
                continue;
            }

            $valor = trim((string)$valor);
            if ($valor !== "") {
                $tieneDatos = true;
            }
            $registro[$campo] = $valor;
        }

        if ($tieneDatos) {
            $registro["_fila"] = $indice;
            $datos[] = $registro;
        }
    }

    return $datos;
}

/**
 * Traduce el nombre completo de una carrera (como viene en el Excel,
 * columna "PE") a la abreviatura que se usa en la tabla `carreras`.
 * Si ya viene abreviada, o no se reconoce, se regresa tal cual.
 */
function abreviarCarrera($valorOriginal)
{
    $valor = trim((string)$valorOriginal);
    if ($valor === "") {
        return "";
    }

    // Quita acentos para comparar sin importar mayúsculas/tildes.
    $sinAcentos = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valor);
    $clave = strtoupper(trim($sinAcentos !== false ? $sinAcentos : $valor));

    $mapa = [
        "INGENIERIA EN SISTEMAS COMPUTACIONALES" => "ISC",
        "INGENIERIA EN INDUSTRIAS ALIMENTARIAS" => "IIA",
        "INGENIERIA ELECTROMECANICA" => "IE",
        "INGENIERIA EN LOGISTICA" => "IL",
        "INGENIERIA CIVIL" => "IC",
        "INGENIERIA MECATRONICA" => "IM",
        "INGENIERIA EN GESTION EMPRESARIAL" => "IGE",
        "INGENIERIA EN SISTEMAS AUTOMOTRICES" => "ISA",
        "LICENCIATURA EN ADMINISTRACION" => "LA",
        "LICENCIATURA EN TURISMO" => "LT",
        "MAESTRIA EN SISTEMAS COMPUTACIONALES" => "MSC",
        "MAESTRIA EN CIENCIAS EN ALIMENTOS" => "MCA",
    ];

    if (isset($mapa[$clave])) {
        return $mapa[$clave];
    }

    // Ya viene abreviada (p. ej. "ISA") u otro valor no reconocido.
    return strtoupper($valor);
}

/**
 * Regresa la primera letra (en mayúscula) de un texto. Se usa para
 * construir la contraseña por defecto de los alumnos.
 */
function inicial($valor)
{
    $valor = trim((string)$valor);
    if ($valor === "") {
        return "";
    }
    return mb_strtoupper(mb_substr($valor, 0, 1, 'UTF-8'), 'UTF-8');
}

function obtenerCarreraId($mysqli, $valor)
{
    $valor = trim((string)$valor);
    if ($valor === "") {
        return null;
    }

    if (ctype_digit($valor)) {
        $id = (int)$valor;
        $stmt = $mysqli->prepare("SELECT id FROM carreras WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $fila ? (int)$fila["id"] : null;
    }

    $abreviatura = abreviarCarrera($valor);

    $stmt = $mysqli->prepare("SELECT id FROM carreras WHERE carrera = ? LIMIT 1");
    $stmt->bind_param("s", $abreviatura);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $fila ? (int)$fila["id"] : null;
}

function idRolEstudiante($mysqli)
{
    $resultado = $mysqli->query("SELECT idRol FROM roles WHERE nombreRol = 'Estudiante' LIMIT 1");
    $fila = $resultado ? $resultado->fetch_assoc() : null;
    return $fila ? (int)$fila["idRol"] : 4;
}

/**
 * Prepara y valida los datos de una fila del Excel para dar de alta,
 * reactivar o actualizar a un alumno. Se usa tanto en la vista previa
 * como al confirmar, para que ambas etapas apliquen las mismas reglas.
 *
 * No exige apellido materno (hay alumnos con apellido único o
 * extranjero). Si no viene contraseña en el archivo, se usa la
 * matrícula como contraseña por defecto.
 */
function prepararDatosAlta($mysqli, $registro)
{
    $matricula = trim($registro["matricula"] ?? "");
    $nombre = trim($registro["nombre"] ?? "");
    $paterno = trim($registro["paterno"] ?? ($registro["apellido_paterno"] ?? ""));
    $materno = trim($registro["materno"] ?? ($registro["apellido_materno"] ?? ""));
    $semestre = (int)($registro["semestre"] ?? 0);
    $sexo = strtoupper(substr(trim($registro["sexo"] ?? ""), 0, 1));
    $contrasena = trim($registro["contrasena"] ?? "");
    $correo = trim($registro["correo"] ?? ($registro["email"] ?? ""));
    $correo = $correo === "" ? null : $correo;

    $carreraOriginal = trim($registro["carrera"] ?? ($registro["pe"] ?? ($registro["id_carrera"] ?? "")));
    $carreraAbrev = abreviarCarrera($carreraOriginal);
    $carreraId = obtenerCarreraId($mysqli, $carreraOriginal);

    if ($contrasena === "") {
        $contrasena = $matricula . inicial($nombre) . inicial($paterno) . inicial($materno);
    }

    $error = null;
    if ($nombre === "") {
        $error = "falta el nombre.";
    } elseif ($paterno === "") {
        $error = "falta el apellido paterno.";
    } elseif ($semestre <= 0) {
        $error = "semestre inválido.";
    } elseif ($sexo === "") {
        $error = "falta el sexo.";
    } elseif (!$carreraId) {
        $error = "carrera \"" . $carreraOriginal . "\" no reconocida en la BD.";
    }

    return [
        "nombre" => $nombre,
        "paterno" => $paterno,
        "materno" => $materno,
        "semestre" => $semestre,
        "sexo" => $sexo,
        "contrasena" => $contrasena,
        "correo" => $correo,
        "carrera_id" => $carreraId,
        "carrera_abrev" => $carreraAbrev,
        "error" => $error
    ];
}

/**
 * Da de alta, reactiva o actualiza a un alumno según su matrícula.
 * Siempre deja al alumno con activo = 1.
 */
function procesarAlta($mysqli, $registro, $idRolEstudiante, &$resumen, &$errores)
{
    $resumen["procesados"]++;
    $matricula = trim($registro["matricula"] ?? "");

    if ($matricula === "") {
        $errores[] = "Fila " . $registro["_fila"] . ": falta matricula.";
        $resumen["omitidos"]++;
        return;
    }

    $datos = prepararDatosAlta($mysqli, $registro);

    if ($datos["error"] !== null) {
        $errores[] = "Fila " . $registro["_fila"] . " (" . $matricula . "): " . $datos["error"];
        $resumen["omitidos"]++;
        return;
    }

    $nombre = $datos["nombre"];
    $paterno = $datos["paterno"];
    $materno = $datos["materno"];
    $semestre = $datos["semestre"];
    $sexo = $datos["sexo"];
    $contrasena = $datos["contrasena"];
    $correo = $datos["correo"];
    $carrera = $datos["carrera_id"];

    $stmt = $mysqli->prepare("SELECT id_usuario, activo FROM usuarios WHERE matricula = ? LIMIT 1");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $existente = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existente) {
        $stmt = $mysqli->prepare("UPDATE usuarios
                                  SET nombre = ?, paterno = ?, materno = ?, sexo = ?, correo = ?, contrasena = ?,
                                      carrera = ?, semestre = ?, activo = 1
                                  WHERE id_usuario = ?");
        $idUsuario = (int)$existente["id_usuario"];
        $stmt->bind_param("ssssssiii", $nombre, $paterno, $materno, $sexo, $correo, $contrasena, $carrera, $semestre, $idUsuario);
        $stmt->execute();
        $stmt->close();

        if ((int)$existente["activo"] === 0) {
            $resumen["reactivados"]++;
        } else {
            $resumen["actualizados"]++;
        }
    } else {
        $stmt = $mysqli->prepare("INSERT INTO usuarios
                                  (nombre, paterno, materno, sexo, correo, contrasena, carrera, matricula, semestre, activo)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("ssssssisi", $nombre, $paterno, $materno, $sexo, $correo, $contrasena, $carrera, $matricula, $semestre);
        $stmt->execute();
        $idUsuario = $stmt->insert_id;
        $stmt->close();
        $resumen["altas"]++;
    }

    $stmt = $mysqli->prepare("INSERT IGNORE INTO usuario_roles (idUsuario, idRol) VALUES (?, ?)");
    $stmt->bind_param("ii", $idUsuario, $idRolEstudiante);
    $stmt->execute();
    $stmt->close();
}

/**
 * Marca como inactivos a todos los alumnos activos cuya matrícula
 * NO aparece en el archivo cargado. Nunca borra registros.
 */
function sincronizarBajas($mysqli, $matriculasExcel, &$resumen)
{
    if (empty($matriculasExcel)) {
        // Ningún alumno en el Excel: se marcan como inactivos todos los
        // alumnos que actualmente están activos.
        $sql = "UPDATE usuarios u
                INNER JOIN usuario_roles ur ON ur.idUsuario = u.id_usuario
                INNER JOIN roles r ON r.idRol = ur.idRol
                SET u.activo = 0
                WHERE r.nombreRol = 'Estudiante'
                  AND u.activo = 1";
        $resultado = $mysqli->query($sql);
        $resumen["bajas"] = $mysqli->affected_rows;
        return;
    }

    $placeholders = implode(',', array_fill(0, count($matriculasExcel), '?'));
    $tipos = str_repeat('s', count($matriculasExcel));

    $sql = "UPDATE usuarios u
            INNER JOIN usuario_roles ur ON ur.idUsuario = u.id_usuario
            INNER JOIN roles r ON r.idRol = ur.idRol
            SET u.activo = 0
            WHERE r.nombreRol = 'Estudiante'
              AND u.matricula NOT IN ($placeholders)
              AND u.activo = 1";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($tipos, ...$matriculasExcel);
    $stmt->execute();
    $resumen["bajas"] = $stmt->affected_rows;
    $stmt->close();
}

/**
 * Obtiene los alumnos activos que quedarían de baja (para la vista
 * previa) según las matrículas presentes en el Excel.
 */
function obtenerBajasPendientes($mysqli, $matriculasExcel)
{
    if (empty($matriculasExcel)) {
        $sql = "SELECT u.matricula, u.nombre, u.paterno, u.materno, c.carrera
                FROM usuarios u
                INNER JOIN usuario_roles ur ON ur.idUsuario = u.id_usuario
                INNER JOIN roles r ON r.idRol = ur.idRol
                LEFT JOIN carreras c ON c.id = u.carrera
                WHERE r.nombreRol = 'Estudiante'
                  AND u.activo = 1";
        $resultado = $mysqli->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    $placeholders = implode(',', array_fill(0, count($matriculasExcel), '?'));
    $tipos = str_repeat('s', count($matriculasExcel));

    $sql = "SELECT u.matricula, u.nombre, u.paterno, u.materno, c.carrera
            FROM usuarios u
            INNER JOIN usuario_roles ur ON ur.idUsuario = u.id_usuario
            INNER JOIN roles r ON r.idRol = ur.idRol
            LEFT JOIN carreras c ON c.id = u.carrera
            WHERE r.nombreRol = 'Estudiante'
              AND u.activo = 1
              AND u.matricula NOT IN ($placeholders)";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($tipos, ...$matriculasExcel);
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $filas;
}

/**
 * Busca alumnos (rol Estudiante) por matrícula o nombre completo,
 * para el buscador manual de bajas.
 */
function buscarAlumnos($mysqli, $termino)
{
    $termino = trim($termino);
    if ($termino === "") {
        return [];
    }

    $like = "%" . $termino . "%";

    $stmt = $mysqli->prepare(
        "SELECT u.id_usuario, u.matricula, u.nombre, u.paterno, u.materno,
                u.semestre, u.activo, c.carrera
         FROM usuarios u
         INNER JOIN usuario_roles ur ON ur.idUsuario = u.id_usuario
         INNER JOIN roles r ON r.idRol = ur.idRol
         LEFT JOIN carreras c ON c.id = u.carrera
         WHERE r.nombreRol = 'Estudiante'
           AND (u.matricula LIKE ?
                OR CONCAT(u.nombre, ' ', u.paterno, ' ', u.materno) LIKE ?)
         ORDER BY u.activo DESC, u.paterno, u.materno, u.nombre
         LIMIT 50"
    );
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $filas;
}

/**
 * Da de baja (activo = 0) a un solo alumno por su id_usuario.
 * Nunca borra el registro. Regresa true si sí estaba activo y se
 * pudo dar de baja.
 */
function darBajaIndividual($mysqli, $idUsuario)
{
    $stmt = $mysqli->prepare(
        "UPDATE usuarios u
         INNER JOIN usuario_roles ur ON ur.idUsuario = u.id_usuario
         INNER JOIN roles r ON r.idRol = ur.idRol
         SET u.activo = 0
         WHERE u.id_usuario = ?
           AND r.nombreRol = 'Estudiante'
           AND u.activo = 1"
    );
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $afectados = $stmt->affected_rows;
    $stmt->close();

    return $afectados > 0;
}

$vistaPrevia = false;
$previewRegistros = [];
$terminoBusqueda = "";
$resultadosBusqueda = [];
$mensajeBaja = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $confirmar = isset($_POST["confirmar"]) && $_POST["confirmar"] == "1";
    $cancelar = isset($_POST["cancelar"]) && $_POST["cancelar"] == "1";
    $buscarAlumno = isset($_POST["buscar_alumno"]);
    $bajaIndividual = isset($_POST["baja_individual"]);

    /*
     * ============================================================
     * CANCELAR VISTA PREVIA (no se aplica ningún cambio)
     * ============================================================
     */
    if ($cancelar) {

        unset($_SESSION["vista_previa_excel"]);
        unset($_SESSION["matriculas_excel"]);

        $mensaje = "Se canceló la vista previa. No se aplicó ningún cambio a la base de datos.";
    }

    /*
     * ============================================================
     * BUSCAR ALUMNO (para baja manual)
     * ============================================================
     */
    elseif ($buscarAlumno) {

        $terminoBusqueda = trim($_POST["termino"] ?? "");
        $resultadosBusqueda = buscarAlumnos($mysqli, $terminoBusqueda);
    }

    /*
     * ============================================================
     * DAR DE BAJA A UN SOLO ALUMNO (desde el buscador)
     * ============================================================
     */
    elseif ($bajaIndividual) {

        $terminoBusqueda = trim($_POST["termino"] ?? "");
        $idUsuario = (int)($_POST["id_usuario"] ?? 0);

        if ($idUsuario > 0 && darBajaIndividual($mysqli, $idUsuario)) {
            $mensajeBaja = "El alumno fue marcado como inactivo.";
        } else {
            $mensajeBaja = "No se pudo dar de baja (ya estaba inactivo o no se encontró).";
        }

        // Se vuelve a buscar con el mismo término para refrescar la lista.
        $resultadosBusqueda = buscarAlumnos($mysqli, $terminoBusqueda);
    }

    /*
     * ============================================================
     * CONFIRMAR CAMBIOS
     * ============================================================
     */
    elseif ($confirmar) {

        $registrosExcel = $_SESSION["vista_previa_excel"] ?? null;
        $matriculasExcel = $_SESSION["matriculas_excel"] ?? null;

        if ($registrosExcel === null || $matriculasExcel === null) {

            $errores[] = "La vista previa ha expirado. Selecciona nuevamente el archivo.";
        } else {

            $idRolEstudiante = idRolEstudiante($mysqli);

            foreach ($registrosExcel as $registro) {
                procesarAlta($mysqli, $registro, $idRolEstudiante, $resumen, $errores);
            }

            sincronizarBajas($mysqli, $matriculasExcel, $resumen);

            $mensaje = "Los cambios fueron aplicados correctamente.";

            unset($_SESSION["vista_previa_excel"]);
            unset($_SESSION["matriculas_excel"]);
        }
    }

    /*
     * ============================================================
     * GENERAR VISTA PREVIA
     * ============================================================
     */ else {

        if (
            !isset($_FILES["archivo"]) ||
            $_FILES["archivo"]["error"] !== UPLOAD_ERR_OK
        ) {

            $errores[] = "No se pudo cargar el archivo.";
        } else {

            try {

                $filas = leerArchivoAlumnos(
                    $_FILES["archivo"]["tmp_name"],
                    $_FILES["archivo"]["name"]
                );

                if (empty($filas)) {

                    $errores[] = "El archivo no contiene registros.";
                } else {

                    $previewRegistros = [];
                    $matriculasExcel = [];

                    foreach ($filas as $registro) {

                        $matricula = trim($registro["matricula"] ?? "");

                        if ($matricula === "") {

                            $registro["_estado"] = "error";
                            $registro["_detalle"] = "Falta la matrícula.";

                            $previewRegistros[] = $registro;
                            continue;
                        }

                        // Se guarda para poder excluirla de las bajas
                        // automáticas, incluso si el resto de la fila
                        // tiene errores.
                        $matriculasExcel[] = $matricula;

                        $datos = prepararDatosAlta($mysqli, $registro);
                        $registro["_carrera_abrev"] = $datos["carrera_abrev"];

                        if ($datos["error"] !== null) {

                            $registro["_estado"] = "error";
                            $registro["_detalle"] = ucfirst($datos["error"]);

                            $previewRegistros[] = $registro;
                            continue;
                        }

                        $stmt = $mysqli->prepare(
                            "SELECT u.id_usuario, u.activo
                             FROM usuarios u
                             INNER JOIN usuario_roles ur ON ur.idUsuario = u.id_usuario
                             INNER JOIN roles r ON r.idRol = ur.idRol
                             WHERE u.matricula = ?
                               AND r.nombreRol = 'Estudiante'
                             LIMIT 1"
                        );

                        $stmt->bind_param("s", $matricula);
                        $stmt->execute();

                        $existente = $stmt->get_result()->fetch_assoc();

                        $stmt->close();

                        if (!$existente) {

                            $registro["_estado"] = "alta";
                            $registro["_detalle"] = "Alumno nuevo.";
                        } elseif ((int)$existente["activo"] === 0) {

                            $registro["_estado"] = "reactivar";
                            $registro["_detalle"] = "El alumno existe pero está inactivo.";
                        } else {

                            $registro["_estado"] = "actualizar";
                            $registro["_detalle"] = "El alumno ya está activo; se actualizarán sus datos.";
                        }

                        $previewRegistros[] = $registro;
                    }

                    /*
                     * Alumnos activos que NO están en el Excel:
                     * se marcarán como inactivos.
                     */
                    $bajasPendientes = obtenerBajasPendientes($mysqli, $matriculasExcel);

                    foreach ($bajasPendientes as $baja) {
                        $previewRegistros[] = [
                            "_fila" => "-",
                            "matricula" => $baja["matricula"],
                            "nombre" => $baja["nombre"],
                            "paterno" => $baja["paterno"],
                            "materno" => $baja["materno"],
                            "carrera" => $baja["carrera"],
                            "_estado" => "baja",
                            "_detalle" => "No aparece en el archivo; se marcará como inactivo."
                        ];
                    }

                    $_SESSION["vista_previa_excel"] = $filas;
                    $_SESSION["matriculas_excel"] = $matriculasExcel;

                    $vistaPrevia = true;
                }
            } catch (Exception $e) {

                $errores[] = "Error al leer el archivo: " . $e->getMessage();
            }
        }
    }
}

?>

<?php if ($vistaPrevia && !empty($previewRegistros)) { ?>

    <hr>

    <h4 class="text-center">
        Vista previa de cambios
    </h4>

    <p class="text-center text-muted">
        Revisa los registros antes de modificar la base de datos.
    </p>


    <?php

    $totalAlta = 0;
    $totalReactivar = 0;
    $totalActualizar = 0;
    $totalBaja = 0;
    $totalError = 0;

    foreach ($previewRegistros as $registro) {

        switch ($registro["_estado"]) {

            case "alta":
                $totalAlta++;
                break;

            case "reactivar":
                $totalReactivar++;
                break;

            case "actualizar":
                $totalActualizar++;
                break;

            case "baja":
                $totalBaja++;
                break;

            case "error":
                $totalError++;
                break;
        }
    }

    ?>

    <p class="text-center">
        Se detectaron:
        <strong><?php echo $totalAlta; ?></strong> alumnos nuevos,
        <strong><?php echo $totalReactivar; ?></strong> reactivaciones,
        <strong><?php echo $totalActualizar; ?></strong> actualizaciones,
        <strong><?php echo $totalBaja; ?></strong> bajas.
    </p>

    <div class="row">

        <div class="col-md-3">
            <div class="alert alert-success text-center">
                <strong><?php echo $totalAlta; ?></strong>
                <br>
                Altas
            </div>
        </div>

        <div class="col-md-3">
            <div class="alert alert-info text-center">
                <strong><?php echo $totalReactivar; ?></strong>
                <br>
                Reactivaciones
            </div>
        </div>

        <div class="col-md-3">
            <div class="alert alert-primary text-center">
                <strong><?php echo $totalActualizar; ?></strong>
                <br>
                Actualizaciones
            </div>
        </div>

        <div class="col-md-3">
            <div class="alert alert-danger text-center">
                <strong><?php echo $totalBaja; ?></strong>
                <br>
                Bajas
            </div>
        </div>

    </div>

    <?php if ($totalError > 0) { ?>
        <div class="alert alert-danger text-center" style="margin-top:10px;">
            <strong><?php echo $totalError; ?></strong> filas con errores (no se pueden aplicar los cambios hasta corregirlas).
        </div>
    <?php } ?>


    <div style="overflow-x:auto;">

        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th>Fila</th>

                    <th>Matrícula</th>

                    <th>Nombre</th>

                    <th>Carrera</th>

                    <th>Estado</th>

                    <th>Acción</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($previewRegistros as $registro) { ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                (string)($registro["_fila"] ?? "")
                            );
                            ?>
                        </td>


                        <td>
                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $registro["matricula"] ?? ""
                                );
                                ?>
                            </strong>
                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                trim(
                                    ($registro["nombre"] ?? "") . " " .
                                        ($registro["paterno"] ?? "") . " " .
                                        ($registro["materno"] ?? "")
                                )
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $registro["_carrera_abrev"] ?? ($registro["carrera"] ?? "")
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            switch ($registro["_estado"]) {

                                case "alta":

                                    echo '<span class="label label-success">
                                            ALTA
                                          </span>';

                                    break;


                                case "reactivar":

                                    echo '<span class="label label-info">
                                            REACTIVAR
                                          </span>';

                                    break;


                                case "actualizar":

                                    echo '<span class="label label-primary">
                                            ACTUALIZAR
                                          </span>';

                                    break;


                                case "baja":

                                    echo '<span class="label label-danger">
                                            BAJA
                                          </span>';

                                    break;


                                case "error":

                                    echo '<span class="label label-danger">
                                            ERROR
                                          </span>';

                                    break;
                            }

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $registro["_detalle"] ?? ""
                            );

                            ?>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>


    <div class="text-center" style="margin-top:20px;">

        <?php if ($totalError == 0) { ?>

            <form method="post" style="display:inline-block;">

                <input
                    type="hidden"
                    name="confirmar"
                    value="1">

                <button
                    type="submit"
                    class="btn btn-success btn-lg"
                    onclick="return confirm('¿Estás seguro de aplicar estos cambios a la base de datos?');">
                    ✓ Confirmar y aplicar cambios
                </button>

            </form>

        <?php } else { ?>

            <div class="alert alert-danger">

                <strong>
                    No se pueden aplicar los cambios.
                </strong>

                Corrige los errores del Excel y vuelve a cargarlo.

            </div>

        <?php } ?>

        <form method="post" style="display:inline-block; margin-left:10px;">

            <input
                type="hidden"
                name="cancelar"
                value="1">

            <button
                type="submit"
                class="btn btn-danger btn-lg"
                onclick="return confirm('¿Cancelar la vista previa? No se aplicará ningún cambio.');">
                ✕ Cancelar cambios
            </button>

        </form>

    </div>

<?php } ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="estilos.css">
    <title>Administrar alumnos</title>
    <style>
        .admin-panel {
            max-width: 920px;
            margin: 40px auto;
        }

        .admin-card {
            border: 1px solid #d8dde5;
            border-radius: 8px;
            padding: 22px;
            background: #fff;
        }

        .columns-note {
            font-size: 14px;
            color: #445;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="container-longer">
        <div class="row">
            <div class="col-lg-3 col-sm-3 imagen">
                <img class="img img-fluid" src="imagenes/itesa.png" width="150px" height="60px">
            </div>
            <div class="col-lg-6 col-sm-6 sup">
                <p style="text-align: center; margin-top: 30px; color: black;"><b>INSTITUTO TECNOLOGICO SUPERIOR DEL ORIENTE DEL ESTADO DE HIDALGO</b></p>
            </div>
            <div class="col-lg-3 col-sm-3 imagen">
                <img class="img img-fluid" src="imagenes/tec.png" width="150px" height="60px">
            </div>
        </div>
    </div>

    <main class="admin-panel">
        <div class="admin-card">
            <h3>Sincronización de Alumnos</h3>

            <p class="text-muted">
                Sube el listado completo de alumnos que deben quedar activos.
                Los que no aparezcan en el archivo y actualmente estén activos
                se marcarán automáticamente como inactivos (no se borran).
            </p>

            <?php if ($mensaje !== "") { ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($mensaje); ?>
                    Procesados: <?php echo $resumen["procesados"]; ?>,
                    altas: <?php echo $resumen["altas"]; ?>,
                    reactivados: <?php echo $resumen["reactivados"]; ?>,
                    actualizados: <?php echo $resumen["actualizados"]; ?>,
                    bajas: <?php echo $resumen["bajas"]; ?>,
                    omitidos: <?php echo $resumen["omitidos"]; ?>.
                </div>
            <?php } ?>

            <?php if (!empty($errores)) { ?>
                <div class="alert alert-warning">
                    <?php foreach (array_slice($errores, 0, 10) as $error) { ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php } ?>
                    <?php if (count($errores) > 10) { ?>
                        <div>Hay <?php echo count($errores) - 10; ?> avisos adicionales.</div>
                    <?php } ?>
                </div>
            <?php } ?>

            <form method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="archivo">Archivo Excel</label>
                    <input class="form-control" id="archivo" name="archivo" type="file" accept=".xlsx,.xls,.csv" required>
                </div>

                <p class="columns-note">
                    Columnas esperadas: Matricula, Nombre, Paterno, Materno (opcional), Semestre, Sexo,
                    Carrera o PE, Email (opcional), Contrasena (opcional).
                    <br>
                    Si el archivo no trae columna de carrera abreviada, se acepta el nombre completo de la
                    carrera (p. ej. "INGENIERÍA EN SISTEMAS AUTOMOTRICES") y se convierte automáticamente
                    a la abreviatura de la BD (ISA).
                    <br>
                    Si no viene columna de contraseña, se genera automáticamente como
                    <strong>matrícula + inicial del nombre + inicial del apellido paterno + inicial del
                        apellido materno</strong> (p. ej. 23030230ACD).
                </p>

                <button class="btn btn-primary" type="submit">Procesar Archivo</button>
                <a class="btn btn-secondary" href="coordinador.php">Regresar</a>
            </form>
        </div>

        <div class="admin-card" style="margin-top:24px;">
            <h3>Dar de baja a un alumno</h3>

            <p class="text-muted">
                Busca por matrícula o nombre para dar de baja (inactivo) a un alumno de forma
                individual, sin necesidad de subir un Excel. No se borra ningún registro.
            </p>

            <?php if ($mensajeBaja !== "") { ?>
                <div class="alert <?php echo strpos($mensajeBaja, 'inactivo') !== false ? 'alert-success' : 'alert-warning'; ?>">
                    <?php echo htmlspecialchars($mensajeBaja); ?>
                </div>
            <?php } ?>

            <form method="post" class="form-inline" style="margin-bottom:16px;">
                <div class="form-group" style="margin-right:10px; flex:1;">
                    <input
                        type="text"
                        class="form-control"
                        style="width:100%;"
                        name="termino"
                        placeholder="Matrícula o nombre del alumno"
                        value="<?php echo htmlspecialchars($terminoBusqueda); ?>">
                </div>
                <button type="submit" name="buscar_alumno" value="1" class="btn btn-primary">
                    Buscar
                </button>
            </form>

            <?php if ($terminoBusqueda !== "" && empty($resultadosBusqueda)) { ?>

                <div class="alert alert-warning">
                    No se encontraron alumnos que coincidan con "<?php echo htmlspecialchars($terminoBusqueda); ?>".
                </div>

            <?php } elseif (!empty($resultadosBusqueda)) { ?>

                <div style="overflow-x:auto;">

                    <table class="table table-bordered table-striped">

                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Carrera</th>
                                <th>Semestre</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($resultadosBusqueda as $alumno) { ?>

                                <tr>

                                    <td><strong><?php echo htmlspecialchars($alumno["matricula"]); ?></strong></td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            trim($alumno["nombre"] . " " . $alumno["paterno"] . " " . $alumno["materno"])
                                        );
                                        ?>
                                    </td>

                                    <td><?php echo htmlspecialchars($alumno["carrera"] ?? ""); ?></td>

                                    <td><?php echo htmlspecialchars((string)$alumno["semestre"]); ?></td>

                                    <td>
                                        <?php if ((int)$alumno["activo"] === 1) { ?>
                                            <span class="label label-success">ACTIVO</span>
                                        <?php } else { ?>
                                            <span class="label label-default">INACTIVO</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?php if ((int)$alumno["activo"] === 1) { ?>
                                            <form method="post" onsubmit="return confirm('¿Dar de baja a <?php echo htmlspecialchars(addslashes($alumno['nombre'] . ' ' . $alumno['paterno'])); ?>?');">
                                                <input type="hidden" name="termino" value="<?php echo htmlspecialchars($terminoBusqueda); ?>">
                                                <input type="hidden" name="id_usuario" value="<?php echo (int)$alumno["id_usuario"]; ?>">
                                                <button type="submit" name="baja_individual" value="1" class="btn btn-danger btn-sm">
                                                    Dar de baja
                                                </button>
                                            </form>
                                        <?php } ?>
                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            <?php } ?>

        </div>
    </main>
</body>

</html>
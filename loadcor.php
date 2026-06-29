<?php
session_start();
require 'config.php';

$conn->set_charset("utf8mb4");

$verTodos = isset($_POST['verTodos']) ? intval($_POST['verTodos']) : 0;

$html = "";

/* Verificar sesión */
if (!isset($_SESSION["usuario"]["clave_C"])) {
    echo json_encode('<tr><td colspan="4" class="text-center">Sesión no válida</td></tr>');
    exit;
}

$clave = $_SESSION["usuario"]["clave_C"];

/* Obtener la carrera del coordinador */
$sqlCarrera = "SELECT carrera
               FROM coordinadores
               WHERE clave = ?";

$stmt = $conn->prepare($sqlCarrera);
$stmt->bind_param("i", $clave);
$stmt->execute();

$resultadoCarrera = $stmt->get_result();

if ($resultadoCarrera->num_rows == 0) {
    echo json_encode('<tr><td colspan="4" class="text-center">Coordinador no encontrado</td></tr>');
    exit;
}

$datos = $resultadoCarrera->fetch_assoc();
$stmt->close();

$idCarrera = $datos["carrera"];

/* Obtener el nombre de la columna de la carrera */
$sqlNombre = "SELECT carrera
              FROM carreras
              WHERE id = ?";

$stmt = $conn->prepare($sqlNombre);
$stmt->bind_param("i", $idCarrera);
$stmt->execute();

$resultadoNombre = $stmt->get_result();

if ($resultadoNombre->num_rows == 0) {
    echo json_encode('<tr><td colspan="4" class="text-center">Carrera no encontrada</td></tr>');
    exit;
}

$datosNombre = $resultadoNombre->fetch_assoc();
$stmt->close();

$columna = $datosNombre["carrera"];

/* Consulta de eventos */

if ($verTodos == 1) {

    $sql = "SELECT Id_Evento,
                   Nombre_Evento,
                   Fecha_Evento,
                   Lugar_Evento
            FROM eventos
            ORDER BY Fecha_Evento DESC";

} else {

    $sql = "SELECT Id_Evento,
                   Nombre_Evento,
                   Fecha_Evento,
                   Lugar_Evento
            FROM eventos
            WHERE `$columna` = 1
            ORDER BY Fecha_Evento DESC";
}

$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {

    while ($row = $resultado->fetch_assoc()) {

        $html .= '<tr data-id="'.$row['Id_Evento'].'">';
        $html .= '<td>'.htmlspecialchars($row['Nombre_Evento']).'</td>';
        $html .= '<td>'.htmlspecialchars($row['Fecha_Evento']).'</td>';
        $html .= '<td>'.htmlspecialchars($row['Lugar_Evento']).'</td>';
        $html .= '<td>';

        $html .= '<button class="btn btn-success btn-modificar btn-action">';
        $html .= '<img src="imagenes/modifica.png" style="height:20px;width:20px;"> Modificar';
        $html .= '</button> ';

        $html .= '<button class="btn btn-danger btn-eliminar btn-action">';
        $html .= '<img src="imagenes/borrar.png" style="height:20px;width:20px;"> Eliminar';
        $html .= '</button>';

        $html .= '</td>';
        $html .= '</tr>';
    }

} else {

    $html = '<tr><td colspan="4" class="text-center">No hay eventos registrados</td></tr>';

}

echo json_encode($html, JSON_UNESCAPED_UNICODE);
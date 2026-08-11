<?php
require("conexion.php");

$consulta = "SELECT u.matricula,
                    u.nombre,
                    u.paterno,
                    u.materno,
                    u.sexo,
                    u.semestre,
                    c.carrera
             FROM usuarios u
             INNER JOIN usuarios_roles ur ON ur.idUsuario = u.id_usuario
             INNER JOIN roles r ON r.idRol = ur.idRol
             LEFT JOIN carreras c ON c.id = u.carrera
             WHERE r.nombreRol = 'Estudiante'
               AND u.activo = 1
             ORDER BY u.paterno, u.materno, u.nombre";

$resultado = $mysqli->query($consulta) or die("ERROR al acceder a la tabla de usuarios: " . $mysqli->error);

echo "<table border=1 width=1200px>
<tr>
<th width=120px>Matricula</th>
<th width=180px>Nombre</th>
<th width=185px>Apellido Paterno</th>
<th width=185px>Apellido Materno</th>
<th width=90px>Genero</th>
<th width=90px>Semestre</th>
<th width=120px>Carrera</th>
</tr>";

while ($fila = $resultado->fetch_assoc()) {
    echo "<tr>
    <td width=120px><center>".$fila['matricula']."</center></td>
    <td width=180px><center>".$fila['nombre']."</center></td>
    <td width=185px><center>".$fila['paterno']."</center></td>
    <td width=185px><center>".$fila['materno']."</center></td>
    <td width=90px><center>".$fila['sexo']."</center></td>
    <td width=90px><center>".$fila['semestre']."</center></td>
    <td width=120px><center>".$fila['carrera']."</center></td>
    </tr>";
}

echo "</table>";
$mysqli->close();
?>

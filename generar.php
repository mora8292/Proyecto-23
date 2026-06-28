
<?php

    $servidor="localhost";
    $usuario="root";
    $conexion=mysql_connect($servidor,$usuario) or die("Problemas al establecer la conexion con el servidor");

    mysql_select_db("itesa",$conexion)or die("problemas al abrir la Base de Datos");

    $comandoSQL=mysql_query("SELECT *FROM estudiantes",$conexion)or die("ERROR al acceder ala tabla de registro de alumnos".mysql_error());

     echo "<table border=1 width=1900px>
 <tr>
 <th width=70px> Clave </th> 
 <th width=180px>Nombre</th>
 <th width=185px>Apellido Paterno</th>
 <th width=185px>Apellido Materno</th>
 <th width=90px>Genero</th>
 <th width=140px>Fecha de nacimiento</th>
 <th width=40px>Edad</th>
 <th width=120px>Estado civil</th>
 <th width=120px> Ocupacion </th>
 <th width=120px> Institucion </th>
 <th width=120px> Direccion </th>
 <th width=120px> Colonia </th>
 <th width=120px> Municipio </th>
 <th width=90px> Telefono </th>
 <th width=120px> Nombre del responsable </th>
 </table>
 ";

while ($consulta=mysql_fetch_array($comandoSQL))

{
echo "

<table border=1 width=1900px>
 <tr>
 <td width=70px><center>".$consulta['id']."</center></td>
 <td width=180px><center>".$consulta['nombre']."</center></td>
 <td width=185px><center>".$consulta ['apellido_p']."</center></td>
 <td width=185px><center>".$consulta ['apellido_m']."</center></td>
 <td width=90px><center>".$consulta['genero']."</center></td>
 <td width=140px><center>".$consulta['fecha']."</center></td>
 <td width=40px><center>".$consulta['edad']."</center></td>
 <td width=120px><center>".$consulta['estado_civil']."</center></td>
 <td width=120px><center>".$consulta['ocupacion']."</center></td>
 <td width=120px><center>".$consulta['institucion']."</center></td>
 <td width=120px><center>".$consulta['direccion']."</center></td>
 <td width=120px><center>".$consulta['colonia']."</center></td>
 <td width=120px><center>".$consulta['municipio']."</center></td>
 <td width=90px><center>".$consulta['telefono']."</center></td>
 <td width=120px><center>".$consulta['nombre_responsable']."</center></td>

 </table>

 ";
 }
mysql_close($conexion);

?>

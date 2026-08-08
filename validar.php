<?php
require("conexion.php");

$usuario = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$passwordIngresada = '';

foreach ($_POST as $campo => $valor) {
    if ($campo !== 'nombre') {
        $passwordIngresada = $valor;
        break;
    }
}

function buscarUsuarioPorRol($mysqli, $campo, $valor, $rol) {
    $campo = $campo === "clave" ? "clave" : "matricula";
    $sql = "SELECT u.matricula, u.clave, u.contrasena
            FROM usuarios u
            INNER JOIN usuarios_roles ur ON ur.idUsuario = u.id_usuario
            INNER JOIN roles r ON r.idRol = ur.idRol
            WHERE u.$campo = ? AND r.nombreRol = ?
            LIMIT 1";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        return null;
    }

    if ($campo === "clave") {
        $valor = (int)$valor;
        $stmt->bind_param("is", $valor, $rol);
    } else {
        $valor = (string)$valor;
        $stmt->bind_param("ss", $valor, $rol);
    }

    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $resultado;
}

if ($usuario === '') {
    header("Location:index.php");
    exit;
}

if (strlen($usuario) == 5) {
    $docente = buscarUsuarioPorRol($mysqli, "clave", $usuario, "Docente");

    if ($docente && $passwordIngresada == $docente['contrasena']) {
        session_start();
        $_SESSION["usuario"]["clave_D"] = $docente['clave'];
        echo "docente";
        exit;
    }

    $coordinador = buscarUsuarioPorRol($mysqli, "clave", $usuario, "Coordinador");

    if ($coordinador && $passwordIngresada == $coordinador['contrasena']) {
        session_start();
        $_SESSION["usuario"]["clave_C"] = $coordinador['clave'];
        echo "coordinador";
        exit;
    }

    echo "Error, usuario o contrasena incorrecta";
    exit;
}

if (strlen($usuario) >= 8) {
    $estudiante = buscarUsuarioPorRol($mysqli, "matricula", $usuario, "Estudiante");

    if ($estudiante && $passwordIngresada == $estudiante['contrasena']) {
        session_start();
        $_SESSION["usuario"]["matricula"] = $estudiante['matricula'];
        echo "estudiante";
        exit;
    }

    echo "Error, usuario o contrasena incorrecta";
    exit;
}

echo "Error, usuario o contrasena incorrecta";
?>

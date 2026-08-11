<?php
require("conexion.php");
require_once("auth.php");

$usuario = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$passwordIngresada = '';

foreach ($_POST as $campo => $valor) {
    if ($campo !== 'nombre') {
        $passwordIngresada = $valor;
        break;
    }
}

function buscarUsuarioLogin($mysqli, $usuario) {
    $campo = strlen($usuario) >= 8 ? "matricula" : "clave";
    $sql = "SELECT u.id_usuario, u.matricula, u.clave, u.contrasena,
                   GROUP_CONCAT(r.nombreRol ORDER BY r.idRol) AS roles
            FROM usuarios u
            INNER JOIN usuarios_roles ur ON ur.idUsuario = u.id_usuario
            INNER JOIN roles r ON r.idRol = ur.idRol
            WHERE u.activo = 1
              AND u.$campo = ?
            GROUP BY u.id_usuario, u.matricula, u.clave, u.contrasena
            LIMIT 1";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        return null;
    }

    if ($campo === "clave") {
        $valor = (int)$usuario;
        $stmt->bind_param("i", $valor);
    } else {
        $stmt->bind_param("s", $usuario);
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

$usuarioEncontrado = buscarUsuarioLogin($mysqli, $usuario);

if (!$usuarioEncontrado || $passwordIngresada != $usuarioEncontrado['contrasena']) {
    echo "Error, usuario o contrasena incorrecta";
    exit;
}

registrarSesionUsuario($usuarioEncontrado);

if (usuarioEsAdministrador() || usuarioTieneRol("Coordinador")) {
    echo "coordinador";
    exit;
}

if (usuarioTieneRol("Docente")) {
    echo "docente";
    exit;
}

if (usuarioTieneRol("Estudiante")) {
    echo "estudiante";
    exit;
}

echo "Error, usuario o contrasena incorrecta";
?>

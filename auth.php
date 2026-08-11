<?php
function iniciarSesionSiHaceFalta() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function rolesDeSesion() {
    iniciarSesionSiHaceFalta();
    return isset($_SESSION["usuario"]["roles"]) && is_array($_SESSION["usuario"]["roles"])
        ? $_SESSION["usuario"]["roles"]
        : [];
}

function usuarioEsAdministrador() {
    iniciarSesionSiHaceFalta();
    return !empty($_SESSION["usuario"]["es_admin"]) || in_array("Administrador", rolesDeSesion());
}

function usuarioTieneRol($rol) {
    return in_array($rol, rolesDeSesion());
}

function registrarSesionUsuario($usuario) {
    iniciarSesionSiHaceFalta();

    $roles = array_filter(array_map('trim', explode(',', $usuario['roles'] ?? '')));

    $_SESSION["usuario"]["id_usuario"] = $usuario['id_usuario'];
    $_SESSION["usuario"]["roles"] = $roles;
    $_SESSION["usuario"]["es_admin"] = in_array("Administrador", $roles);

    if (!empty($usuario['matricula'])) {
        $_SESSION["usuario"]["matricula"] = $usuario['matricula'];
    }

    if (!empty($usuario['clave'])) {
        if (in_array("Docente", $roles)) {
            $_SESSION["usuario"]["clave_D"] = $usuario['clave'];
        }

        if (in_array("Coordinador", $roles) || in_array("Administrador", $roles)) {
            $_SESSION["usuario"]["clave_C"] = $usuario['clave'];
        }
    }
}

function usuarioPuedeEntrar($rolesPermitidos) {
    iniciarSesionSiHaceFalta();

    if (usuarioEsAdministrador()) {
        return true;
    }

    foreach ($rolesPermitidos as $rol) {
        if (usuarioTieneRol($rol)) {
            return true;
        }
    }

    return false;
}

function requerirRoles($rolesPermitidos) {
    if (!usuarioPuedeEntrar($rolesPermitidos)) {
        header("Location: index.php");
        exit;
    }
}
?>

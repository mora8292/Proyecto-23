<?php
require_once "auth.php";
require "conexion.php";

requerirRoles(["Administrador"]);
header('Content-Type: text/html; charset=UTF-8');

if (empty($_SESSION['csrf_editar_roles'])) {
    $_SESSION['csrf_editar_roles'] = bin2hex(random_bytes(32));
}

$rolesEditables = [
    'Coordinador' => 'Coordinadores',
    'Docente' => 'Docentes',
    'Estudiante' => 'Estudiantes'
];
$etiquetasSingulares = [
    'Coordinador' => 'Coordinador',
    'Docente' => 'Docente',
    'Estudiante' => 'Estudiante'
];
$rolSeleccionado = isset($_GET['rol']) ? $_GET['rol'] : 'Coordinador';
if (!isset($rolesEditables[$rolSeleccionado])) {
    $rolSeleccionado = 'Coordinador';
}

$mensaje = '';
$tipoMensaje = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rolSeleccionado = isset($_POST['filtro']) && isset($rolesEditables[$_POST['filtro']])
        ? $_POST['filtro']
        : 'Coordinador';

    if (!hash_equals($_SESSION['csrf_editar_roles'], isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $mensaje = 'La sesión del formulario expiró. Intenta nuevamente.';
        $tipoMensaje = 'danger';
    } else {
        $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $nuevosRoles = isset($_POST['roles']) && is_array($_POST['roles']) ? $_POST['roles'] : [];
        $nuevosRoles = array_values(array_intersect(array_keys($rolesEditables), $nuevosRoles));

        if (!$idUsuario) {
            $mensaje = 'El usuario seleccionado no es válido.';
            $tipoMensaje = 'danger';
        } else {
            $mysqli->begin_transaction();
            try {
                $idsRoles = [];
                $resultadoRoles = $mysqli->query("SELECT idRol, nombreRol FROM roles WHERE nombreRol IN ('Coordinador', 'Docente', 'Estudiante')");
                while ($filaRol = $resultadoRoles->fetch_assoc()) {
                    $idsRoles[$filaRol['nombreRol']] = (int)$filaRol['idRol'];
                }

                $stmtEliminar = $mysqli->prepare(
                    "DELETE ur FROM usuarios_roles ur
                     INNER JOIN roles r ON r.idRol = ur.idRol
                     WHERE ur.idUsuario = ? AND r.nombreRol IN ('Coordinador', 'Docente', 'Estudiante')"
                );
                $stmtEliminar->bind_param('i', $idUsuario);
                $stmtEliminar->execute();

                $stmtInsertar = $mysqli->prepare("INSERT INTO usuarios_roles (idUsuario, idRol) VALUES (?, ?)");
                foreach ($nuevosRoles as $nombreRol) {
                    if (isset($idsRoles[$nombreRol])) {
                        $idRol = $idsRoles[$nombreRol];
                        $stmtInsertar->bind_param('ii', $idUsuario, $idRol);
                        $stmtInsertar->execute();
                    }
                }

                $mysqli->commit();
                $mensaje = 'Los roles se actualizaron correctamente.';
            } catch (Throwable $error) {
                $mysqli->rollback();
                $mensaje = 'No fue posible actualizar los roles.';
                $tipoMensaje = 'danger';
            }
        }
    }
}

$stmtUsuarios = $mysqli->prepare(
    "SELECT u.id_usuario, u.nombre, u.paterno, u.materno, u.correo, u.matricula, u.clave, u.activo,
            GROUP_CONCAT(DISTINCT todos.nombreRol ORDER BY todos.idRol SEPARATOR ',') AS roles
     FROM usuarios u
     INNER JOIN usuarios_roles filtroUr ON filtroUr.idUsuario = u.id_usuario
     INNER JOIN roles filtroRol ON filtroRol.idRol = filtroUr.idRol AND filtroRol.nombreRol = ?
     LEFT JOIN usuarios_roles todosUr ON todosUr.idUsuario = u.id_usuario
     LEFT JOIN roles todos ON todos.idRol = todosUr.idRol
     GROUP BY u.id_usuario, u.nombre, u.paterno, u.materno, u.correo, u.matricula, u.clave, u.activo
     ORDER BY u.paterno, u.materno, u.nombre"
);
$stmtUsuarios->bind_param('s', $rolSeleccionado);
$stmtUsuarios->execute();
$usuarios = $stmtUsuarios->get_result();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar roles de usuarios</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        body { background: #f5f7f8; }
        .roles-panel { max-width: 1150px; margin: 38px auto 90px; padding: 0 18px; }
        .roles-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 18px rgba(0,0,0,.09); padding: 28px; }
        .role-tabs { display: flex; flex-wrap: wrap; gap: 12px; margin: 24px 0; }
        .role-tabs .btn { min-width: 170px; border-color: rgb(0,119,112); color: rgb(0,119,112); }
        .role-tabs .active { background: rgb(0,119,112); color: #fff; }
        .user-row { border-top: 1px solid #e7e7e7; padding: 18px 0; }
        .user-name { font-size: 1.08rem; font-weight: 600; }
        .user-meta { color: #666; font-size: .92rem; }
        .role-checks { display: flex; flex-wrap: wrap; gap: 16px; margin: 10px 0; }
        .save-role { background: rgb(0,119,112); border-color: rgb(0,119,112); }
        @media (max-width: 576px) { .roles-card { padding: 18px; } .role-tabs .btn { width: 100%; } }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-3 imagen"><img class="img img-fluid" src="imagenes/itesa.png" width="150" height="60" alt="ITESA"></div>
            <div class="col-6 sup"><p class="text-center mt-4"><b>INSTITUTO TECNOLÓGICO SUPERIOR DEL ORIENTE DEL ESTADO DE HIDALGO</b></p></div>
            <div class="col-3 imagen"><img class="img img-fluid" src="imagenes/tec.png" width="150" height="60" alt="Tecnológico Nacional de México"></div>
        </div>
    </div>

    <main class="roles-panel">
        <div class="roles-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h2 class="mb-1">Editar roles de usuarios</h2>
                    <p class="text-muted mb-0">Selecciona un grupo para consultar sus usuarios y modificar sus roles.</p>
                </div>
                <a class="btn btn-secondary" href="coordinador.php">Regresar</a>
            </div>

            <?php if ($mensaje !== '') { ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> mt-4" role="alert"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php } ?>

            <nav class="role-tabs" aria-label="Tipos de usuario">
                <?php foreach ($rolesEditables as $rol => $etiqueta) { ?>
                    <a class="btn <?php echo $rolSeleccionado === $rol ? 'active' : ''; ?>" href="?rol=<?php echo urlencode($rol); ?>">
                        <?php echo htmlspecialchars($etiqueta); ?>
                    </a>
                <?php } ?>
            </nav>

            <h4><?php echo htmlspecialchars($rolesEditables[$rolSeleccionado]); ?></h4>
            <?php if ($usuarios->num_rows === 0) { ?>
                <div class="alert alert-info mt-3">No hay usuarios registrados con este rol.</div>
            <?php } ?>

            <?php while ($usuario = $usuarios->fetch_assoc()) {
                $rolesUsuario = $usuario['roles'] ? explode(',', $usuario['roles']) : [];
                $identificador = $usuario['matricula'] ?: $usuario['clave'];
            ?>
                <form class="user-row" method="post">
                    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_editar_roles']); ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo (int)$usuario['id_usuario']; ?>">
                    <input type="hidden" name="filtro" value="<?php echo htmlspecialchars($rolSeleccionado); ?>">
                    <div class="row align-items-center">
                        <div class="col-lg-5">
                            <div class="user-name"><?php echo htmlspecialchars(trim($usuario['nombre'].' '.$usuario['paterno'].' '.$usuario['materno'])); ?></div>
                            <div class="user-meta">
                                <?php echo $usuario['matricula'] ? 'Matrícula: ' : 'Clave: '; ?><?php echo htmlspecialchars((string)$identificador); ?>
                                <?php if ($usuario['correo']) { ?> · <?php echo htmlspecialchars($usuario['correo']); ?><?php } ?>
                                · <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="role-checks">
                                <?php foreach ($rolesEditables as $rol => $etiqueta) { ?>
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="<?php echo htmlspecialchars($rol); ?>" <?php echo in_array($rol, $rolesUsuario, true) ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($etiquetasSingulares[$rol]); ?>
                                    </label>
                                <?php } ?>
                            </div>
                            <?php if (in_array('Administrador', $rolesUsuario, true)) { ?><span class="badge bg-dark">Administrador</span><?php } ?>
                        </div>
                        <div class="col-lg-2 text-lg-end mt-3 mt-lg-0">
                            <button class="btn btn-primary save-role" type="submit">Guardar roles</button>
                        </div>
                    </div>
                </form>
            <?php } ?>
        </div>
    </main>
</body>
</html>

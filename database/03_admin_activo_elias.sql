SET @columna_activo := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'usuarios'
    AND COLUMN_NAME = 'activo'
);

SET @sql_activo := IF(
  @columna_activo = 0,
  'ALTER TABLE usuarios ADD COLUMN activo tinyint(1) NOT NULL DEFAULT 1',
  'SELECT 1'
);

PREPARE stmt_activo FROM @sql_activo;
EXECUTE stmt_activo;
DEALLOCATE PREPARE stmt_activo;

UPDATE usuarios
SET activo = 1
WHERE activo IS NULL;

INSERT IGNORE INTO usuarios_roles (idUsuario, idRol)
SELECT u.id_usuario, r.idRol
FROM usuarios u
INNER JOIN roles r ON r.nombreRol = 'Administrador'
WHERE u.nombre = 'ELIAS'
  AND u.paterno = 'RUIZ'
  AND u.materno = 'HERNANDEZ'
  AND u.clave = 32070;

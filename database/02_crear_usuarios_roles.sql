CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `paterno` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `materno` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `sexo` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `correo` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contrasena` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `carrera` tinyint(4) DEFAULT NULL,
  `matricula` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `clave` int(11) DEFAULT NULL,
  `semestre` tinyint(4) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `matricula` (`matricula`),
  UNIQUE KEY `clave` (`clave`),
  UNIQUE KEY `correo` (`correo`),
  KEY `Con_Car_Usu_Del_Res_Upd_Cas` (`carrera`),
  CONSTRAINT `Con_Car_Usu_Del_Res_Upd_Cas`
    FOREIGN KEY (`carrera`) REFERENCES `carreras` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `idRol` tinyint(4) NOT NULL AUTO_INCREMENT,
  `nombreRol` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`idRol`),
  UNIQUE KEY `nombreRol` (`nombreRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `roles` (`idRol`, `nombreRol`) VALUES
(1, 'Administrador'),
(2, 'Coordinador'),
(3, 'Docente'),
(4, 'Estudiante');

INSERT IGNORE INTO `usuarios`
(`nombre`, `paterno`, `materno`, `sexo`, `correo`, `contrasena`, `carrera`, `matricula`, `clave`, `semestre`, `activo`)
SELECT `nombre`, `paterno`, `materno`, `sexo`, `correo`, `contrasena`, `carrera`, NULL, `clave`, NULL, 1
FROM `coordinadores`;

INSERT IGNORE INTO `usuarios`
(`nombre`, `paterno`, `materno`, `sexo`, `correo`, `contrasena`, `carrera`, `matricula`, `clave`, `semestre`, `activo`)
SELECT `nombre`, `paterno`, `materno`, `sexo`, `correo`, `contrasena`, `carrera`, NULL, `clave`, NULL, 1
FROM `docentes`;

INSERT IGNORE INTO `usuarios`
(`nombre`, `paterno`, `materno`, `sexo`, `correo`, `contrasena`, `carrera`, `matricula`, `clave`, `semestre`, `activo`)
SELECT `nombre`, `paterno`, `materno`, `sexo`, NULL, `contrasena`, `carrera`, `matricula`, NULL, `semestre`, 1
FROM `estudiantes`;

DROP VIEW IF EXISTS `usuarios_roles`;

CREATE TABLE IF NOT EXISTS `usuarios_roles` (
  `idUsuario` int(11) NOT NULL,
  `idRol` tinyint(4) NOT NULL,
  PRIMARY KEY (`idUsuario`, `idRol`),
  KEY `Con_Rol_UsuariosRol_Del_Res_Upd_Cas` (`idRol`),
  CONSTRAINT `Con_Rol_UsuariosRol_Del_Res_Upd_Cas`
    FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`) ON UPDATE CASCADE,
  CONSTRAINT `Con_Usu_UsuariosRol_Del_Cas_Upd_Cas`
    FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `usuarios_roles` (`idUsuario`, `idRol`)
SELECT u.`id_usuario`, 2
FROM `usuarios` u
INNER JOIN `coordinadores` c ON c.`clave` = u.`clave`;

INSERT IGNORE INTO `usuarios_roles` (`idUsuario`, `idRol`)
SELECT u.`id_usuario`, 3
FROM `usuarios` u
INNER JOIN `docentes` d ON d.`clave` = u.`clave`;

INSERT IGNORE INTO `usuarios_roles` (`idUsuario`, `idRol`)
SELECT u.`id_usuario`, 4
FROM `usuarios` u
INNER JOIN `estudiantes` e ON e.`matricula` = u.`matricula`;

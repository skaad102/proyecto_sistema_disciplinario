-- Crear nueva base de datos
CREATE DATABASE IF NOT EXISTS `sistema_disciplinario` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_disciplinario`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

-- Tabla rol (sin cambios, es correcta)
CREATE TABLE `rol` (
  `cod_rol` int(11) NOT NULL AUTO_INCREMENT,
  `rol` varchar(255) NOT NULL,
  PRIMARY KEY (`cod_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla tipo_documento (sin cambios, es correcta)
CREATE TABLE `tipo_documento` (
  `cod_tipodocumento` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_documento` varchar(255) NOT NULL,
  PRIMARY KEY (`cod_tipodocumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla usuario (base para docentes y estudiantes)
CREATE TABLE `usuario` (
  `cod_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipo_documento` int(11) NOT NULL,
  `numero_documento` varchar(255) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `direccion` varchar(255),
  `usuario` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `estado` ENUM('ACTIVO', 'INACTIVO', 'GRADUADO') DEFAULT 'ACTIVO',
  PRIMARY KEY (`cod_usuario`),
  UNIQUE KEY `uq_documento` (`id_tipo_documento`, `numero_documento`),
  UNIQUE KEY `uq_usuario` (`usuario`),
  UNIQUE KEY `uq_correo` (`correo`),
  CONSTRAINT `fk_usuario_tipo_documento` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documento` (`cod_tipodocumento`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`cod_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla asignatura (mejorada)
CREATE TABLE `asignatura` (
  `cod_asignatura` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_asignatura` varchar(255) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`cod_asignatura`),
  UNIQUE KEY `uq_nombre_asignatura` (`nombre_asignatura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla grado (mejorada)
CREATE TABLE `grado` (
  `cod_grado` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_grado` varchar(255) NOT NULL,
  `nivel` ENUM('PRIMARIA', 'SECUNDARIA', 'MEDIA') NOT NULL,
  PRIMARY KEY (`cod_grado`),
  UNIQUE KEY `uq_nombre_grado` (`nombre_grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla docente (optimizada)
CREATE TABLE `docente` (
  `cod_docente` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `especialidad` text,
  PRIMARY KEY (`cod_docente`),
  UNIQUE KEY `uq_docente_usuario` (`id_usuario`),
  CONSTRAINT `fk_docente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla curso (mejorada)
CREATE TABLE `curso` (
  `cod_curso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(255) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `id_director_grupo` int(11),
  `ano_lectivo` int(11) NOT NULL,
  `estado` ENUM('ACTIVO', 'INACTIVO') DEFAULT 'ACTIVO',
  PRIMARY KEY (`cod_curso`),
  UNIQUE KEY `uq_curso_ano` (`nombre_curso`, `ano_lectivo`),
  CONSTRAINT `fk_curso_grado` FOREIGN KEY (`id_grado`) REFERENCES `grado` (`cod_grado`),
  CONSTRAINT `fk_curso_director` FOREIGN KEY (`id_director_grupo`) REFERENCES `docente` (`cod_docente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla asignacion_docente (nueva tabla para manejar docentes por curso y asignatura)
CREATE TABLE `asignacion_docente` (
  `cod_asignacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_docente` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `ano_lectivo` int(11) NOT NULL,
  PRIMARY KEY (`cod_asignacion`),
  UNIQUE KEY `uq_asignacion` (`id_docente`, `id_curso`, `id_asignatura`, `ano_lectivo`),
  CONSTRAINT `fk_asignacion_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`cod_docente`),
  CONSTRAINT `fk_asignacion_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`cod_curso`),
  CONSTRAINT `fk_asignacion_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`cod_asignatura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla estudiante (optimizada)
CREATE TABLE `estudiante` (
  `cod_estudiante` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  PRIMARY KEY (`cod_estudiante`),
  UNIQUE KEY `uq_estudiante_usuario` (`id_usuario`),
  CONSTRAINT `fk_estudiante_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla matricula (nueva tabla para manejar estudiantes por curso)
CREATE TABLE `matricula` (
  `cod_matricula` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `fecha_matricula` date NOT NULL,
  `estado` ENUM('ACTIVA', 'RETIRADO', 'GRADUADO') DEFAULT 'ACTIVA',
  PRIMARY KEY (`cod_matricula`),
  UNIQUE KEY `uq_matricula` (`id_estudiante`, `id_curso`),
  CONSTRAINT `fk_matricula_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`cod_estudiante`),
  CONSTRAINT `fk_matricula_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`cod_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla tipo_falta (mejorada)
CREATE TABLE `tipo_falta` (
  `cod_tipofalta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(255) NOT NULL,
  `descripcion_tipo` text NOT NULL,
  `gravedad` ENUM('LEVE', 'GRAVE', 'MUY_GRAVE') NOT NULL,
  PRIMARY KEY (`cod_tipofalta`),
  UNIQUE KEY `uq_nombre_tipo` (`nombre_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla falta (mejorada)
CREATE TABLE `falta` (
  `cod_falta` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipofalta` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `sancion_sugerida` text,
  PRIMARY KEY (`cod_falta`),
  CONSTRAINT `fk_falta_tipo` FOREIGN KEY (`id_tipofalta`) REFERENCES `tipo_falta` (`cod_tipofalta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla registro_falta (mejorada)
CREATE TABLE `registro_falta` (
  `cod_registro` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_registro` date NOT NULL,
  `hora_registro` time NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_docente` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_falta` int(11) NOT NULL,
  `descripcion_falta` text NOT NULL,
  `descargos_estudiante` text,
  `correctivos_disciplinarios` text,
  `compromisos` text,
  `observaciones` text,
  `estado` ENUM('REPORTADA', 'EN_PROCESO', 'SANCIONADA', 'ARCHIVADA') DEFAULT 'REPORTADA',
  PRIMARY KEY (`cod_registro`),
  CONSTRAINT `fk_registro_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`cod_estudiante`),
  CONSTRAINT `fk_registro_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`cod_docente`),
  CONSTRAINT `fk_registro_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`cod_curso`),
  CONSTRAINT `fk_registro_falta` FOREIGN KEY (`id_falta`) REFERENCES `falta` (`cod_falta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla seguimiento (mejorada)
CREATE TABLE `seguimiento` (
  `cod_seguimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_registro` int(11) NOT NULL,
  `fecha_seguimiento` datetime NOT NULL,
  `realizado_por` int(11) NOT NULL,
  `observaciones` text NOT NULL,
  `resultado` ENUM('POSITIVO', 'NEGATIVO', 'EN_PROCESO') NOT NULL,
  PRIMARY KEY (`cod_seguimiento`),
  CONSTRAINT `fk_seguimiento_registro` FOREIGN KEY (`id_registro`) REFERENCES `registro_falta` (`cod_registro`),
  CONSTRAINT `fk_seguimiento_usuario` FOREIGN KEY (`realizado_por`) REFERENCES `usuario` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Restaurar verificación de claves foráneas
-- Insertar datos iniciales
-- Insertar roles
INSERT INTO `rol` (`cod_rol`, `rol`) VALUES
(1, 'Directivo'),
(2, 'Docente'),
(3, 'Estudiante');

-- Insertar tipos de documento
INSERT INTO `tipo_documento` (`cod_tipodocumento`, `tipo_documento`) VALUES
(1, 'Registro Civil'),
(2, 'Tarjeta de Identidad'),
(3, 'Cedula de Ciudadania'),
(4, 'Pasaporte'),
(5, 'Permiso Especial');

-- Insertar grados
INSERT INTO `grado` (`cod_grado`, `nombre_grado`, `nivel`) VALUES
(1, 'Primero', 'PRIMARIA'),
(2, 'Segundo', 'PRIMARIA'),
(3, 'Tercero', 'PRIMARIA'),
(4, 'Cuarto', 'PRIMARIA'),
(5, 'Quinto', 'PRIMARIA');

-- Insertar asignaturas
INSERT INTO `asignatura` (`cod_asignatura`, `nombre_asignatura`, `descripcion`) VALUES
(1, 'Inglés', 'Asignatura de lengua extranjera - Inglés');

-- Insertar usuarios iniciales (necesario para docentes y estudiantes)
INSERT INTO `usuario` (
    `cod_usuario`, 
    `id_tipo_documento`, 
    `numero_documento`, 
    `nombres`, 
    `apellidos`, 
    `telefono`, 
    `correo`, 
    `usuario`, 
    `clave`, 
    `id_rol`
) VALUES
(4, 1, '1082498535', 'Danna', 'Agudelo', '3126429827', 'danna@gmail.com', 'danna.agudelo', '123456', 2);

-- Insertar docente
INSERT INTO `docente` (`cod_docente`, `id_usuario`, `especialidad`) VALUES
(3, 4, 'Inglés');

-- Insertar cursos (corregidos con la nueva estructura)
INSERT INTO `curso` (`cod_curso`, `nombre_curso`, `id_grado`, `id_director_grupo`, `ano_lectivo`) VALUES
(1, '11-1', 1, 3, 2025),
(2, '6-3', 1, 3, 2025);

-- Insertar tipos de falta (corregido con estructura adecuada)
INSERT INTO `tipo_falta` (`cod_tipofalta`, `nombre_tipo`, `descripcion_tipo`, `gravedad`) VALUES
(1, 'Falta de Asistencia', 'Inasistencia injustificada a clases', 'LEVE'),
(2, 'Indisciplina', 'Comportamiento inadecuado en clase', 'GRAVE');

-- Insertar faltas
INSERT INTO `falta` (`cod_falta`, `id_tipofalta`, `descripcion`, `sancion_sugerida`) VALUES
(1, 2, 'No asiste a clase', 'Cuando ingresa al aula de clase se comporta de manera indebida');

-- Restaurar verificación de claves foráneas y commit
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
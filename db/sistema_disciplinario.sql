-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2025 at 02:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

DROP DATABASE IF EXISTS `sistema_disciplinario`;
CREATE DATABASE `sistema_disciplinario` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sistema_disciplinario`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistema_disciplinario`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `cod_admin` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asignacion_docente`
--

CREATE TABLE `asignacion_docente` (
  `cod_asignacion` int(11) NOT NULL,
  `id_docente` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `ano_lectivo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asignacion_docente`
--

INSERT INTO `asignacion_docente` (`cod_asignacion`, `id_docente`, `id_curso`, `id_asignatura`, `ano_lectivo`) VALUES
(1, 3, 2, 1, 2025),
(7, 4, 2, 3, 2025);

-- --------------------------------------------------------

--
-- Table structure for table `asignatura`
--

CREATE TABLE `asignatura` (
  `cod_asignatura` int(11) NOT NULL,
  `nombre_asignatura` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asignatura`
--

INSERT INTO `asignatura` (`cod_asignatura`, `nombre_asignatura`, `descripcion`) VALUES
(1, 'Inglés', 'Asignatura de lengua extranjera - Inglés'),
(3, 'Sistemas', 'Aula 202 caney 2'),
(15, 'Español', 'Español para niños'),
(25, 'Español 2', 'nuevo');

-- --------------------------------------------------------

--
-- Table structure for table `curso`
--

CREATE TABLE `curso` (
  `cod_curso` int(11) NOT NULL,
  `nombre_curso` varchar(255) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `id_director_grupo` int(11) DEFAULT NULL,
  `ano_lectivo` int(11) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curso`
--

INSERT INTO `curso` (`cod_curso`, `nombre_curso`, `id_grado`, `id_director_grupo`, `ano_lectivo`, `estado`) VALUES
(1, '11-1', 1, 3, 2025, 'ACTIVO'),
(2, '6-3', 1, 3, 2025, 'ACTIVO'),
(5, 'Español tarde', 4, 3, 2025, 'ACTIVO'),
(6, 'Español MAñana', 5, 4, 2025, 'ACTIVO');

-- --------------------------------------------------------

--
-- Table structure for table `docente`
--

CREATE TABLE `docente` (
  `cod_docente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `especialidad` text DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `docente`
--

INSERT INTO `docente` (`cod_docente`, `id_usuario`, `especialidad`, `estado`) VALUES
(3, 4, 'Inglés', 'ACTIVO'),
(4, 10000000, 'Matematicas', 'ACTIVO'),
(5, 10000016, 'Matematicas', 'ACTIVO');

-- --------------------------------------------------------

--
-- Table structure for table `estudiante`
--

CREATE TABLE `estudiante` (
  `cod_estudiante` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_nacimiento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `estudiante`
--

INSERT INTO `estudiante` (`cod_estudiante`, `id_usuario`, `fecha_nacimiento`) VALUES
(2, 9, '1998-02-10'),
(13, 13, '2025-11-09'),
(30, 20, '2025-11-04'),
(31, 24, '1998-05-10'),
(39, 10000014, '2020-04-12'),
(41, 10000017, '2022-02-10');

-- --------------------------------------------------------

--
-- Table structure for table `falta`
--

CREATE TABLE `falta` (
  `cod_falta` int(11) NOT NULL,
  `id_tipofalta` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `sancion_sugerida` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `falta`
--

INSERT INTO `falta` (`cod_falta`, `id_tipofalta`, `descripcion`, `sancion_sugerida`) VALUES
(1, 2, 'No asiste a clase', 'Cuando ingresa al aula de clase se comporta de manera indebida');

-- --------------------------------------------------------

--
-- Table structure for table `grado`
--

CREATE TABLE `grado` (
  `cod_grado` int(11) NOT NULL,
  `nombre_grado` varchar(255) NOT NULL,
  `nivel` enum('PRIMARIA','SECUNDARIA','MEDIA') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grado`
--

INSERT INTO `grado` (`cod_grado`, `nombre_grado`, `nivel`) VALUES
(1, 'Primero', 'PRIMARIA'),
(2, 'Segundo', 'PRIMARIA'),
(3, 'Tercero', 'PRIMARIA'),
(4, 'Cuarto', 'PRIMARIA'),
(5, 'Quinto', 'PRIMARIA');

-- --------------------------------------------------------

--
-- Table structure for table `matricula`
--

CREATE TABLE `matricula` (
  `cod_matricula` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `fecha_matricula` date NOT NULL,
  `estado` enum('ACTIVA','RETIRADO','GRADUADO') DEFAULT 'ACTIVA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matricula`
--

INSERT INTO `matricula` (`cod_matricula`, `id_estudiante`, `id_curso`, `fecha_matricula`, `estado`) VALUES
(1, 2, 2, '2025-11-09', 'ACTIVA'),
(2, 13, 2, '2025-11-01', 'ACTIVA'),
(3, 30, 2, '2025-11-04', 'ACTIVA'),
(4, 41, 2, '2025-11-02', 'ACTIVA'),
(5, 31, 6, '2025-11-11', 'ACTIVA'),
(6, 39, 5, '2025-11-10', 'ACTIVA'),
(7, 30, 6, '2025-11-13', 'ACTIVA'),
(8, 2, 6, '2025-11-13', 'RETIRADO'),
(9, 13, 6, '2025-11-13', 'ACTIVA'),
(18, 41, 6, '2025-11-13', 'ACTIVA'),
(29, 39, 6, '2025-11-13', 'ACTIVA');

-- --------------------------------------------------------

--
-- Table structure for table `registro_falta`
--

CREATE TABLE `registro_falta` (
  `cod_registro` int(11) NOT NULL,
  `fecha_registro` date NOT NULL,
  `hora_registro` time NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_docente` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_falta` int(11) NOT NULL,
  `descripcion_falta` text NOT NULL,
  `descargos_estudiante` text DEFAULT NULL,
  `correctivos_disciplinarios` text DEFAULT NULL,
  `compromisos` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('REPORTADA','EN_PROCESO','SANCIONADA','ARCHIVADA') DEFAULT 'REPORTADA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rol`
--

CREATE TABLE `rol` (
  `cod_rol` int(11) NOT NULL,
  `rol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rol`
--

INSERT INTO `rol` (`cod_rol`, `rol`) VALUES
(1, 'Directivo'),
(2, 'Docente'),
(3, 'Estudiante');

-- --------------------------------------------------------

--
-- Table structure for table `seguimiento`
--

CREATE TABLE `seguimiento` (
  `cod_seguimiento` int(11) NOT NULL,
  `id_registro` int(11) NOT NULL,
  `fecha_seguimiento` datetime NOT NULL,
  `realizado_por` int(11) NOT NULL,
  `observaciones` text NOT NULL,
  `resultado` enum('POSITIVO','NEGATIVO','EN_PROCESO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `cod_tipodocumento` int(11) NOT NULL,
  `tipo_documento` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipo_documento`
--

INSERT INTO `tipo_documento` (`cod_tipodocumento`, `tipo_documento`) VALUES
(1, 'Registro Civil'),
(2, 'Tarjeta de Identidad'),
(3, 'Cedula de Ciudadania'),
(4, 'Pasaporte'),
(5, 'Permiso Especial');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_falta`
--

CREATE TABLE `tipo_falta` (
  `cod_tipofalta` int(11) NOT NULL,
  `nombre_tipo` varchar(255) NOT NULL,
  `descripcion_tipo` text NOT NULL,
  `gravedad` enum('LEVE','GRAVE','MUY_GRAVE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipo_falta`
--

INSERT INTO `tipo_falta` (`cod_tipofalta`, `nombre_tipo`, `descripcion_tipo`, `gravedad`) VALUES
(1, 'Falta de Asistencia', 'Inasistencia injustificada a clases', 'LEVE'),
(2, 'Indisciplina', 'Comportamiento inadecuado en clase', 'GRAVE');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `cod_usuario` int(11) NOT NULL,
  `id_tipo_documento` int(11) NOT NULL,
  `numero_documento` varchar(255) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `usuario` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO','GRADUADO') DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`cod_usuario`, `id_tipo_documento`, `numero_documento`, `nombres`, `apellidos`, `telefono`, `correo`, `direccion`, `usuario`, `clave`, `id_rol`, `estado`) VALUES
(4, 1, '1082498535', 'Danna', 'Agudelo', '3126429827', 'danna@gmail.com', NULL, 'danna.agudelo', '123456', 2, 'ACTIVO'),
(9, 3, '10065677', 'danie', 'vele', '31188888', 'correo@coreee', 'calle1215', 'danie.vele', '123123', 3, 'ACTIVO'),
(12, 3, '1023', 'juean', 'veas', '321215132', 'corere@coreq.com', 'calle11111', 'juean', 'juean', 3, 'ACTIVO'),
(13, 3, '1023456789', 'Juan Carlos', 'Vélez Ramírez', '3212151321', 'jvelez@email.com', 'Calle 11 #45-67', 'jvelez', 'jvelez123', 3, 'ACTIVO'),
(14, 1, '1024567890', 'María José', 'Palacios Silva', '3156789012', 'mpalacios@email.com', 'Carrera 23 #12-45', 'mpalacios', 'mpalacios123', 3, 'ACTIVO'),
(15, 2, '1025678901', 'Daniel', 'Ospina Torres', '3167890123', 'dospina@email.com', 'Avenida 34 #56-78', 'dospina', 'dospina123', 3, 'ACTIVO'),
(16, 3, '1026789012', 'Laura Valentina', 'Martínez Ruiz', '3178901234', 'lmartinez@email.com', 'Calle 45 #89-12', 'lmartinez', 'lmartinez123', 3, 'ACTIVO'),
(17, 1, '1027890123', 'Andrés Felipe', 'García López', '3189012345', 'agarcia@email.com', 'Carrera 67 #34-56', 'agarcia', 'agarcia123', 3, 'ACTIVO'),
(18, 2, '1028901234', 'Sofía', 'Herrera Díaz', '3190123456', 'sherrera@email.com', 'Avenida 78 #90-23', 'sherrera', 'sherrera123', 3, 'ACTIVO'),
(19, 3, '1029012345', 'Carlos Eduardo', 'Rojas Pérez', '3201234567', 'crojas@email.com', 'Calle 89 #12-34', 'crojas', 'crojas123', 3, 'ACTIVO'),
(20, 1, '1030123456', 'Isabella', 'Mendoza Castro', '3212345678', 'imendoza@email.com', 'Carrera 90 #45-67', 'imendoza', 'imendoza123', 3, 'ACTIVO'),
(21, 2, '1031234567', 'Santiago', 'Vargas Molina', '3223456789', 'svargas@email.com', 'Avenida 12 #67-89', 'svargas', 'svargas123', 3, 'ACTIVO'),
(22, 3, '1032345678', 'Valeria', 'Sánchez Ríos', '3234567890', 'vsanchez@email.com', 'Calle 23 #90-12', 'vsanchez', 'vsanchez123', 3, 'ACTIVO'),
(24, 3, '1000101010', 'daniel', 'adame', '31651651', 'core@vorekalsdk.co', 'cale 12r\'opas', 'daniel.agudelo', '123456', 3, 'ACTIVO'),
(9999999, 3, '99999999', 'super', 'admin', '31000000', 'admin@colegio.conm', 'av siempre viva 123', 'admin', 'admin123', 1, 'ACTIVO'),
(10000000, 3, '555555', 'juan carlos', 'perez', '3100055808', 'micore@coreo.com', 'caler qwe', 'juan.perez', '123456', 2, 'ACTIVO'),
(10000001, 3, '45454545', 'Sebstian', 'Vega', '31215188', 'sebas@corer.com', 'calle 2312 23', 'sebstian.vega', '123456', 3, 'ACTIVO'),
(10000005, 3, '5050505050', 'jaun p', 'perez', '3505050', 'pelawz@correo.com', 'calle 36 050 5 5', 'jaun.perez', '123456', 3, 'ACTIVO'),
(10000006, 3, '4040404', 'sebaston', 'tobon', '340404040', 'cocente@coreel.com', 'calel 2e ', 'sebaston.tobon', '123456', 3, 'ACTIVO'),
(10000007, 3, '10101010', 'juan carlo', 'vale', '31022645065', 'jcar@core.com', 'cid ada ', 'juan.vale', '123456', 3, 'ACTIVO'),
(10000008, 3, '123123123', 'juan vcamil', 'camilin', '3123123123', 'camilin@corer.com', 'calle 45 123123', 'juan.camilin', '123456', 3, 'ACTIVO'),
(10000009, 3, '456456456', 'sebas', 'gonzales', '32132213515', 'senas@corre.com', 'cara 34 dok', 'sebas.gonzales', '123456', 3, 'ACTIVO'),
(10000011, 3, '789789', 'juan jose', 'jose', '3505018789', 'docente@creco.co', 'calle 987 78', 'juan.jose', '123456', 3, 'ACTIVO'),
(10000014, 3, '1006565656', 'maria jose', 'lopez', '2132235135', 'estudiente@cor.c', 'maria ', 'maria.lopez', '123456', 3, 'ACTIVO'),
(10000015, 3, '90909090', 'carlos ', 'añdaen', '350550', 'acela.q@corcom.co', 'calle 123', 'carlos.andaen', '123456', 3, 'ACTIVO'),
(10000016, 3, '65636521', 'docente', 'deoce', '365320325', 'docente@corec.com', 'calle a0 ', 'docente.deoce', '123456', 2, 'ACTIVO'),
(10000017, 3, '10068895', 'jose seito', 'hita', '32155', 'estiajo@cor.com', 'aleta a', 'jose.hita', '123456', 3, 'ACTIVO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`cod_admin`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  ADD PRIMARY KEY (`cod_asignacion`),
  ADD UNIQUE KEY `uq_asignacion` (`id_docente`,`id_curso`,`id_asignatura`,`ano_lectivo`),
  ADD KEY `fk_asignacion_curso` (`id_curso`),
  ADD KEY `fk_asignacion_asignatura` (`id_asignatura`);

--
-- Indexes for table `asignatura`
--
ALTER TABLE `asignatura`
  ADD PRIMARY KEY (`cod_asignatura`),
  ADD UNIQUE KEY `uq_nombre_asignatura` (`nombre_asignatura`);

--
-- Indexes for table `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`cod_curso`),
  ADD UNIQUE KEY `uq_curso_ano` (`nombre_curso`,`ano_lectivo`),
  ADD KEY `fk_curso_grado` (`id_grado`),
  ADD KEY `fk_curso_director` (`id_director_grupo`);

--
-- Indexes for table `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`cod_docente`),
  ADD UNIQUE KEY `uq_docente_usuario` (`id_usuario`);

--
-- Indexes for table `estudiante`
--
ALTER TABLE `estudiante`
  ADD PRIMARY KEY (`cod_estudiante`),
  ADD UNIQUE KEY `uq_estudiante_usuario` (`id_usuario`);

--
-- Indexes for table `falta`
--
ALTER TABLE `falta`
  ADD PRIMARY KEY (`cod_falta`),
  ADD KEY `fk_falta_tipo` (`id_tipofalta`);

--
-- Indexes for table `grado`
--
ALTER TABLE `grado`
  ADD PRIMARY KEY (`cod_grado`),
  ADD UNIQUE KEY `uq_nombre_grado` (`nombre_grado`);

--
-- Indexes for table `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`cod_matricula`),
  ADD UNIQUE KEY `uq_matricula` (`id_estudiante`,`id_curso`),
  ADD KEY `fk_matricula_curso` (`id_curso`);

--
-- Indexes for table `registro_falta`
--
ALTER TABLE `registro_falta`
  ADD PRIMARY KEY (`cod_registro`),
  ADD KEY `fk_registro_estudiante` (`id_estudiante`),
  ADD KEY `fk_registro_docente` (`id_docente`),
  ADD KEY `fk_registro_curso` (`id_curso`),
  ADD KEY `fk_registro_falta` (`id_falta`);

--
-- Indexes for table `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`cod_rol`);

--
-- Indexes for table `seguimiento`
--
ALTER TABLE `seguimiento`
  ADD PRIMARY KEY (`cod_seguimiento`),
  ADD KEY `fk_seguimiento_registro` (`id_registro`),
  ADD KEY `fk_seguimiento_usuario` (`realizado_por`);

--
-- Indexes for table `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`cod_tipodocumento`);

--
-- Indexes for table `tipo_falta`
--
ALTER TABLE `tipo_falta`
  ADD PRIMARY KEY (`cod_tipofalta`),
  ADD UNIQUE KEY `uq_nombre_tipo` (`nombre_tipo`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cod_usuario`),
  ADD UNIQUE KEY `uq_documento` (`id_tipo_documento`,`numero_documento`),
  ADD UNIQUE KEY `uq_usuario` (`usuario`),
  ADD UNIQUE KEY `uq_correo` (`correo`),
  ADD KEY `fk_usuario_rol` (`id_rol`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  MODIFY `cod_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `asignatura`
--
ALTER TABLE `asignatura`
  MODIFY `cod_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `curso`
--
ALTER TABLE `curso`
  MODIFY `cod_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `docente`
--
ALTER TABLE `docente`
  MODIFY `cod_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `cod_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `falta`
--
ALTER TABLE `falta`
  MODIFY `cod_falta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grado`
--
ALTER TABLE `grado`
  MODIFY `cod_grado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `matricula`
--
ALTER TABLE `matricula`
  MODIFY `cod_matricula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `registro_falta`
--
ALTER TABLE `registro_falta`
  MODIFY `cod_registro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rol`
--
ALTER TABLE `rol`
  MODIFY `cod_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seguimiento`
--
ALTER TABLE `seguimiento`
  MODIFY `cod_seguimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `cod_tipodocumento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tipo_falta`
--
ALTER TABLE `tipo_falta`
  MODIFY `cod_tipofalta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `cod_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000018;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`cod_usuario`);

--
-- Constraints for table `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  ADD CONSTRAINT `fk_asignacion_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`cod_asignatura`),
  ADD CONSTRAINT `fk_asignacion_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`cod_curso`),
  ADD CONSTRAINT `fk_asignacion_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`cod_docente`);

--
-- Constraints for table `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `fk_curso_director` FOREIGN KEY (`id_director_grupo`) REFERENCES `docente` (`cod_docente`),
  ADD CONSTRAINT `fk_curso_grado` FOREIGN KEY (`id_grado`) REFERENCES `grado` (`cod_grado`);

--
-- Constraints for table `docente`
--
ALTER TABLE `docente`
  ADD CONSTRAINT `fk_docente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`cod_usuario`);

--
-- Constraints for table `estudiante`
--
ALTER TABLE `estudiante`
  ADD CONSTRAINT `fk_estudiante_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`cod_usuario`);

--
-- Constraints for table `falta`
--
ALTER TABLE `falta`
  ADD CONSTRAINT `fk_falta_tipo` FOREIGN KEY (`id_tipofalta`) REFERENCES `tipo_falta` (`cod_tipofalta`);

--
-- Constraints for table `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `fk_matricula_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`cod_curso`),
  ADD CONSTRAINT `fk_matricula_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`cod_estudiante`);

--
-- Constraints for table `registro_falta`
--
ALTER TABLE `registro_falta`
  ADD CONSTRAINT `fk_registro_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`cod_curso`),
  ADD CONSTRAINT `fk_registro_docente` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`cod_docente`),
  ADD CONSTRAINT `fk_registro_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`cod_estudiante`),
  ADD CONSTRAINT `fk_registro_falta` FOREIGN KEY (`id_falta`) REFERENCES `falta` (`cod_falta`);

--
-- Constraints for table `seguimiento`
--
ALTER TABLE `seguimiento`
  ADD CONSTRAINT `fk_seguimiento_registro` FOREIGN KEY (`id_registro`) REFERENCES `registro_falta` (`cod_registro`),
  ADD CONSTRAINT `fk_seguimiento_usuario` FOREIGN KEY (`realizado_por`) REFERENCES `usuario` (`cod_usuario`);

--
-- Constraints for table `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`cod_rol`),
  ADD CONSTRAINT `fk_usuario_tipo_documento` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documento` (`cod_tipodocumento`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

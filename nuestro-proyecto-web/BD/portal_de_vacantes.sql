-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-08-2025 a las 14:12:40
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `portal_de_vacantes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `item`
--

CREATE TABLE `item` (
  `nro_item` int(11) NOT NULL,
  `ID_Vacante` int(11) NOT NULL,
  `descripcion` varchar(256) NOT NULL,
  `valor_max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `item`
--

INSERT INTO `item` (`nro_item`, `ID_Vacante`, `descripcion`, `valor_max`) VALUES
(2, 2, 'Titulo universitario', 10),
(7, 4, 'Trabajo en equipo', 8),
(8, 4, 'Manejo de Workbench y lenguaje Mysql', 12),
(10, 1, 'valentía', 10),
(11, 1, 'limpieza', 50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(256) NOT NULL,
  `apellido` varchar(256) NOT NULL,
  `mail` varchar(256) NOT NULL,
  `clave` varchar(256) NOT NULL,
  `DNI` int(11) NOT NULL,
  `rol` tinyint(1) NOT NULL DEFAULT 0,
  `cv` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`ID`, `nombre`, `apellido`, `mail`, `clave`, `DNI`, `rol`, `cv`) VALUES
(1, 'admin', 'administrador', 'admin@example.com', '$2y$10$51H.VUNWKPqw3JlllWFUgurgRhLq70D/lZjF6I05TakEonCRs5HsC', 12345678, 1, ''),
(5, 'Francisco', 'Bebo', 'fransbebobruno@gmail.com', '$2y$10$GQ3c/ObU8kLd.3vJD/9lK.zjEu.UhRhpVx/s6KKAnxyrGVT1KKLPG', 40312859, 0, '../uploads/cv_40312859.pdf'),
(6, 'Tomas Malcolm', 'Gigli', 'tomasgigli@yahoo.com.ar', '$2y$10$s0/X9ldOjmkcqeUXjWxkje5mdf.gGlhhBzMoMm1FgcWNo0/oSggPG', 42959191, 0, '../uploads/cv_42959191.pdf'),
(7, 'Jefe', 'De Catedra', 'jefedecatedra@gmail.com', '$2y$10$EBWE.jCCjNkMt4fG/Vzk4eB.yJ/BF7ll.lzvV4LT1H55D4L/muxaS', 12345677, 2, ''),
(8, 'Cande', 'Gigli', 'candegigli@gmail.com', '$2y$10$1ultIMT1cipOVt7uw0VtF.dCkYupiNdinWOJBFBc/MguiAL4xomw.', 44123123, 0, '../uploads/cv_44123123.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postulacion`
--

CREATE TABLE `postulacion` (
  `ID_Persona` int(11) NOT NULL,
  `ID_Vacante` int(11) NOT NULL,
  `fecha_hora_post` date NOT NULL,
  `resultado` varchar(256) NOT NULL,
  `puntaje` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `postulacion`
--

INSERT INTO `postulacion` (`ID_Persona`, `ID_Vacante`, `fecha_hora_post`, `resultado`, `puntaje`) VALUES
(5, 4, '2025-08-05', '', 0),
(6, 1, '2025-08-05', '', 0),
(6, 3, '2025-08-05', '', 0),
(8, 1, '2025-08-05', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultado_item`
--

CREATE TABLE `resultado_item` (
  `ID` int(11) NOT NULL,
  `ID_Vacante` int(11) NOT NULL,
  `nro_item` int(11) NOT NULL,
  `resultado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resultado_item`
--

INSERT INTO `resultado_item` (`ID`, `ID_Vacante`, `nro_item`, `resultado`) VALUES
(5, 4, 7, 5),
(5, 4, 8, 12),
(6, 1, 10, 8),
(6, 1, 11, 28);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacante`
--

CREATE TABLE `vacante` (
  `ID` int(11) NOT NULL,
  `estado` varchar(256) NOT NULL,
  `descripcion` varchar(525) NOT NULL,
  `fecha_ini` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `ID_Jefe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vacante`
--

INSERT INTO `vacante` (`ID`, `estado`, `descripcion`, `fecha_ini`, `fecha_fin`, `titulo`, `ID_Jefe`) VALUES
(1, 'abierta', 'La Facultad Regional busca incorporar un/a docente para el dictado de la asignatura Matemática I correspondiente al primer año de la carrera de Ingeniería.\r\nEl/la postulante deberá contar con conocimientos sólidos en álgebra, funciones, límites y cálculo diferencial. Se valorará experiencia previa en docencia universitaria y manejo de herramientas digitales para la enseñanza.\r\nCarga horaria: 6hs.', '2025-06-05', '2025-08-05', 'Profesor/a de Matemática', 7),
(2, 'abierta', 'Se requiere un/a profesor/a auxiliar para colaborar en clases prácticas de la materia Matemática Discreta en la carrera de Ingeniería en Sistemas.\r\nEntre las tareas se incluyen: resolución de ejercicios en clase, asistencia en corrección de trabajos prácticos y apoyo a los estudiantes durante consultas.\r\nEs deseable tener conocimientos de lógica proposicional, conjuntos, relaciones, grafos y estructuras algebraicas básicas.\r\nCarga horaria: 4 hs.', '2025-06-25', '2025-08-25', 'Profesor/a Auxiliar de Matemática Discreta', 7),
(3, 'cerrada', 'Se busca un/a profesor/a para dictar clases teóricas y prácticas de la materia Física I en la carrera de Ingeniería. Se valorará experiencia previa docente y conocimiento en mecánica clásica, cinemática, dinámica y leyes de Newton. Carga horaria: 6 hs semanales.\r\n', '2025-07-01', '2025-08-01', 'Profesor/a de Física I', 7),
(4, 'abierta', 'La Facultad Regional busca incorporar un/a docente para el dictado de la asignatura Sistemas de Bases de Datos, correspondiente al tercer año de la carrera de Ingeniería en Sistemas de Información. El/la postulante deberá contar con conocimientos sólidos en modelado relacional, lenguaje SQL, normalización y administración de bases de datos. Carga horaria: 20 hs. semanales.', '2025-07-15', '2025-08-29', 'Profesor de Base de Datos', 7),
(5, 'cerrada', 'Vacante para profesor/a responsable del dictado de la asignatura Geometría y Álgebra. El candidato ideal deberá tener conocimientos sólidos en álgebra lineal, matrices, determinantes, espacios vectoriales y geometría analítica. Experiencia docente deseable. Carga horaria: 6 hs semanales.', '2025-01-01', '2025-02-28', 'Profesor/a de Geometría y Álgebra', 7);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`nro_item`,`ID_Vacante`),
  ADD KEY `FK_VACANTE2` (`ID_Vacante`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `postulacion`
--
ALTER TABLE `postulacion`
  ADD PRIMARY KEY (`ID_Persona`,`ID_Vacante`,`fecha_hora_post`),
  ADD KEY `FK_VACANTE` (`ID_Vacante`);

--
-- Indices de la tabla `resultado_item`
--
ALTER TABLE `resultado_item`
  ADD PRIMARY KEY (`ID`,`ID_Vacante`,`nro_item`),
  ADD KEY `ID_Vacante` (`ID_Vacante`,`nro_item`);

--
-- Indices de la tabla `vacante`
--
ALTER TABLE `vacante`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_JEFE_DE_CATEDRA` (`ID_Jefe`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `item`
--
ALTER TABLE `item`
  MODIFY `nro_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `vacante`
--
ALTER TABLE `vacante`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `FK_VACANTE2` FOREIGN KEY (`ID_Vacante`) REFERENCES `vacante` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `postulacion`
--
ALTER TABLE `postulacion`
  ADD CONSTRAINT `FK_PERSONA` FOREIGN KEY (`ID_Persona`) REFERENCES `persona` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_VACANTE` FOREIGN KEY (`ID_Vacante`) REFERENCES `vacante` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `resultado_item`
--
ALTER TABLE `resultado_item`
  ADD CONSTRAINT `resultado_item_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `persona` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `resultado_item_ibfk_2` FOREIGN KEY (`ID_Vacante`,`nro_item`) REFERENCES `item` (`ID_Vacante`, `nro_item`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vacante`
--
ALTER TABLE `vacante`
  ADD CONSTRAINT `FK_JEFE_DE_CATEDRA` FOREIGN KEY (`ID_Jefe`) REFERENCES `persona` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

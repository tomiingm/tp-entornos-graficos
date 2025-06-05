-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-06-2025 a las 19:10:15
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
-- Estructura de tabla para la tabla `vacante`
--

CREATE TABLE `vacante` (
  `ID` int(11) NOT NULL,
  `estado` varchar(256) NOT NULL,
  `descripcion` varchar(525) NOT NULL,
  `fecha_ini` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `titulo` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vacante`
--

INSERT INTO `vacante` (`ID`, `estado`, `descripcion`, `fecha_ini`, `fecha_fin`, `titulo`) VALUES
(1, 'abierta', 'La Facultad Regional busca incorporar un/a docente para el dictado de la asignatura Matemática I correspondiente al primer año de la carrera de Ingeniería.\r\nEl/la postulante deberá contar con conocimientos sólidos en álgebra, funciones, límites y cálculo diferencial. Se valorará experiencia previa en docencia universitaria y manejo de herramientas digitales para la enseñanza.\r\nCarga horaria: 6hs.', '2025-06-05', '2025-08-05', 'Profesor/a de Matemática'),
(2, 'abierta', 'Se requiere un/a profesor/a auxiliar para colaborar en clases prácticas de la materia Matemática Discreta en la carrera de Ingeniería en Sistemas.\r\nEntre las tareas se incluyen: resolución de ejercicios en clase, asistencia en corrección de trabajos prácticos y apoyo a los estudiantes durante consultas.\r\nEs deseable tener conocimientos de lógica proposicional, conjuntos, relaciones, grafos y estructuras algebraicas básicas.\r\nCarga horaria: 4 hs.', '2025-06-25', '2025-08-25', 'Profesor/a Auxiliar de Matemática Discreta');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `vacante`
--
ALTER TABLE `vacante`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `vacante`
--
ALTER TABLE `vacante`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-08-2024 a las 22:59:36
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `canacintra`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros`
--

CREATE TABLE `registros` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `asistencia` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registros`
--

INSERT INTO `registros` (`id`, `id_usuario`, `asistencia`) VALUES
(1, 1, 0),
(2, 2, 0),
(3, 3, 0),
(4, 4, 0),
(5, 5, 0),
(6, 6, 0),
(7, 7, 0),
(8, 8, 0),
(9, 9, 0),
(10, 10, 0),
(11, 11, 0),
(12, 12, 0),
(13, 15, 0),
(14, 16, 0),
(15, 17, 0),
(16, 18, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `empresa` varchar(100) NOT NULL,
  `puesto` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `correo_electronico`, `telefono`, `empresa`, `puesto`) VALUES
(1, 'gg', 'ivv', 'gaEL.CHAVEZ@UABC.EDU.MX', '6651207782', '', ''),
(2, 'gg', 'ivv', 'gael.chavez@uabc.edu.mx', '6651207782', '', ''),
(3, 'gg', 'ivv', 'gael.chavez@uabc.edu.mx', '6651207782', '', ''),
(4, 'gg', 'ivv', 'gael.chavez@uabc.edu.mx', '6651207782', '', ''),
(5, 'gael', 'dsa', 'mym.meggs@gmail.com', '6651207782', '', ''),
(6, 'gael', 'ivan', 'mym.meggs@gmail.com', '6651207782', '', ''),
(7, 'Gaeld', 'gas', 'mym.meggs@gmail.com', '66651282', '', ''),
(8, 'fds', 'dsa', 'mym.meggs@gmail.com', '323222', '', ''),
(9, 'fds', 'dsa', 'andreeit@hotmail.com', '323222', '', ''),
(10, 'fds', 'dsa', 'mym.meggs@gmail.com', '323222', '', ''),
(11, 'fdsjfds', 'dsjfbdsuilfhuwif', 'gael.chavez@uabc.edu.mx', '21', '', ''),
(12, 'fdsjfds', 'dsjfbdsuilfhuwif', 'johan.barragan@uabc.edu.mx', '21', '', ''),
(13, 'Johan', 'fndjsn gey', 'johan.barragan@uabc.edu.mx', '6651207782', 'Canacintra', 'Ca'),
(14, 'Johan', 'fndjsn gey', 'Gael.chavez@uabc.edu.mx', '6651207782', 'Canacintra', 'Ca'),
(15, 'fdsf', 'fds', 'gael.chavez@uabc.edu.mx', '665120782', 'Canacintra', 'si'),
(16, 'Gsel', 'Ivan', 'gael.chavez@uabc.edu.mx', '6651207782', 'Canacintra ', 'Empresario'),
(17, 'Gael', 'Chavez', 'gael.chavez@uabc.edu.mx', '6651287782', 'Canacintra', 'Interpol'),
(18, 'Paulina Mora Trasvina Libiracon', 'lib', 'mora.paulina@uabc.edu.mx', '666', 'Canacintra', 'si');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `registros`
--
ALTER TABLE `registros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `registros`
--
ALTER TABLE `registros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `registros`
--
ALTER TABLE `registros`
  ADD CONSTRAINT `registros_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

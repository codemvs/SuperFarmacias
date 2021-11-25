-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-11-2021 a las 05:11:32
-- Versión del servidor: 10.4.21-MariaDB
-- Versión de PHP: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `superfarmacia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblcanal`
--

CREATE TABLE `tblcanal` (
  `idCanal` int(11) NOT NULL,
  `idAdmin` int(11) NOT NULL,
  `idCliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblmensaje`
--

CREATE TABLE `tblmensaje` (
  `idMensaje` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `idCanal` int(11) NOT NULL,
  `mensaje` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `visto` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbltipousuario`
--

CREATE TABLE `tbltipousuario` (
  `idTipoUsuario` int(11) NOT NULL,
  `nombre` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` varchar(20) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbltipousuario`
--

INSERT INTO `tbltipousuario` (`idTipoUsuario`, `nombre`, `descripcion`) VALUES
(1, 'admin', 'Usuario Aministrador'),
(2, 'cliente', 'Usuario normal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tblusuario`
--

CREATE TABLE `tblusuario` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `idTipoUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tblusuario`
--

INSERT INTO `tblusuario` (`idUsuario`, `nombre`, `correo`, `idTipoUsuario`) VALUES
(1, 'Super Farmacias Chicontepec', 'superfarmaciachicontepec@gmail.com', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tblcanal`
--
ALTER TABLE `tblcanal`
  ADD PRIMARY KEY (`idCanal`);

--
-- Indices de la tabla `tblmensaje`
--
ALTER TABLE `tblmensaje`
  ADD PRIMARY KEY (`idMensaje`);

--
-- Indices de la tabla `tbltipousuario`
--
ALTER TABLE `tbltipousuario`
  ADD PRIMARY KEY (`idTipoUsuario`);

--
-- Indices de la tabla `tblusuario`
--
ALTER TABLE `tblusuario`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tblcanal`
--
ALTER TABLE `tblcanal`
  MODIFY `idCanal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tblmensaje`
--
ALTER TABLE `tblmensaje`
  MODIFY `idMensaje` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbltipousuario`
--
ALTER TABLE `tbltipousuario`
  MODIFY `idTipoUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tblusuario`
--
ALTER TABLE `tblusuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

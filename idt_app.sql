-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-08-2021 a las 00:05:40
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `idt_app`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido`
--

CREATE TABLE `contenido` (
  `ID` int(11) NOT NULL,
  `URL` varchar(50) NOT NULL,
  `Tipo` varchar(10) NOT NULL,
  `Estado` tinyint(1) NOT NULL,
  `Orden` varchar(20) NOT NULL,
  `Lista-Reproduccion` int(11) NOT NULL,
  `Fecha_Modificación` datetime NOT NULL,
  `Usuario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `contenido`
--

INSERT INTO `contenido` (`ID`, `URL`, `Tipo`, `Estado`, `Orden`, `Lista-Reproduccion`, `Fecha_Modificación`, `Usuario`) VALUES
(2, 'KIOSKO_IDT_1080x1920_HANDBRAKE.mp4', 'video', 1, '1', 1, '2021-07-24 10:35:00', 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `frame`
--

CREATE TABLE `frame` (
  `ID` int(11) NOT NULL,
  `URL` varchar(150) NOT NULL,
  `Fecha-Modificacion` datetime NOT NULL,
  `Usuario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `frame`
--

INSERT INTO `frame` (`ID`, `URL`, `Fecha-Modificacion`, `Usuario`) VALUES
(1, 'https://bogotadc.travel/', '2021-07-15 15:21:28', 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funciones_usuario`
--

CREATE TABLE `funciones_usuario` (
  `ID` int(11) NOT NULL,
  `Funcion` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `funciones_usuario`
--

INSERT INTO `funciones_usuario` (`ID`, `Funcion`) VALUES
(1, 'Administrador'),
(2, 'Colaborador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista-reproduccion`
--

CREATE TABLE `lista-reproduccion` (
  `ID` int(11) NOT NULL,
  `Nombre` varchar(80) NOT NULL,
  `Fecha-Inicio` datetime NOT NULL,
  `Estado` tinyint(11) NOT NULL,
  `Fecha-Modificacion` datetime NOT NULL,
  `Usuario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `lista-reproduccion`
--

INSERT INTO `lista-reproduccion` (`ID`, `Nombre`, `Fecha-Inicio`, `Estado`, `Fecha-Modificacion`, `Usuario`) VALUES
(1, 'Default', '2021-07-22 15:25:00', 1, '2021-07-22 15:23:05', 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `ID` int(11) NOT NULL,
  `nombre_modulo` varchar(150) NOT NULL,
  `ubicacion` varchar(150) NOT NULL,
  `fecha_modificacion` datetime NOT NULL,
  `Usuario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`ID`, `nombre_modulo`, `ubicacion`, `fecha_modificacion`, `Usuario`) VALUES
(1, 'Módulo 1', 'Terminal de Transportes', '2020-06-03 13:59:49', 'Admin'),
(2, 'Módulo 2', 'Avenida Jimenez', '2020-06-11 21:04:40', 'Admin'),
(3, 'Módulo 3', 'Carrera 24 - Park Way', '2021-08-03 17:11:00', 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reco`
--

CREATE TABLE `reco` (
  `ID` int(50) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `Hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Tiempo` int(50) NOT NULL,
  `Genero` varchar(50) NOT NULL,
  `Edad` varchar(50) NOT NULL,
  `Expresion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID` int(11) NOT NULL,
  `Nombre` varchar(80) NOT NULL,
  `Correo` varchar(80) NOT NULL,
  `Foto_Usuario` varchar(80) NOT NULL,
  `Funcion` int(11) NOT NULL,
  `Password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `Nombre`, `Correo`, `Foto_Usuario`, `Funcion`, `Password`) VALUES
(1, 'Admin', 'web@electronika.info', 'assets/img_users/Perfil_1.jpg', 1, '028a383eb4e80a375a5ac3be898948af'),
(9, 'Colaborador', 'demo@idt.gov.co', 'assets/img_users/Perfil_2.jpg', 2, 'e10adc3949ba59abbe56e057f20f883e');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitantes`
--

CREATE TABLE `visitantes` (
  `ID` int(11) NOT NULL,
  `Tipo_Documento` varchar(50) NOT NULL,
  `No_Documento` varchar(30) NOT NULL,
  `Nombres` varchar(50) NOT NULL,
  `Apellidos` varchar(50) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `Fecha_Ingreso` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `visitantes`
--

INSERT INTO `visitantes` (`ID`, `Tipo_Documento`, `No_Documento`, `Nombres`, `Apellidos`, `modulo`, `Fecha_Ingreso`) VALUES
(1, 'Cédula de ciudadanía', '123', 'Alan', 'Brito', 'Módulo 1', '2021-07-17 15:25:29');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contenido`
--
ALTER TABLE `contenido`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_ListaReproduccion` (`Lista-Reproduccion`);

--
-- Indices de la tabla `frame`
--
ALTER TABLE `frame`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `funciones_usuario`
--
ALTER TABLE `funciones_usuario`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `lista-reproduccion`
--
ALTER TABLE `lista-reproduccion`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `reco`
--
ALTER TABLE `reco`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_Funcion-Usuario` (`Funcion`);

--
-- Indices de la tabla `visitantes`
--
ALTER TABLE `visitantes`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contenido`
--
ALTER TABLE `contenido`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `frame`
--
ALTER TABLE `frame`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `funciones_usuario`
--
ALTER TABLE `funciones_usuario`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `lista-reproduccion`
--
ALTER TABLE `lista-reproduccion`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `reco`
--
ALTER TABLE `reco`
  MODIFY `ID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=572;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `visitantes`
--
ALTER TABLE `visitantes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contenido`
--
ALTER TABLE `contenido`
  ADD CONSTRAINT `fk_ListaReproduccion` FOREIGN KEY (`Lista-Reproduccion`) REFERENCES `lista-reproduccion` (`ID`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_Funcion-Usuario` FOREIGN KEY (`Funcion`) REFERENCES `funciones_usuario` (`ID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

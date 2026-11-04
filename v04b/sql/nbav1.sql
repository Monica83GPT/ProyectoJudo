-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-07-2023 a las 11:33:01
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nbav1`
--
CREATE DATABASE IF NOT EXISTS `nbav1` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `nbav1`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `amistadjugadores`
--

CREATE TABLE `amistadjugadores` (
  `idjugador` smallint(5) UNSIGNED NOT NULL,
  `idjugador2` smallint(5) UNSIGNED NOT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `check`
--

CREATE TABLE `check` (
  `check` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `idciudad` smallint(5) UNSIGNED NOT NULL,
  `nomciudad` varchar(30) NOT NULL,
  `iorden` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`idciudad`, `nomciudad`, `iorden`) VALUES
(7, 'Akron', 1),
(2, 'Boston', 1),
(4, 'Chicago', 1),
(12, 'Detroit', 1),
(14, 'Detroit', 2),
(3, 'Lansing', 1),
(1, 'Los Ángeles', 1),
(8, 'Louisville', 1),
(10, 'Miami', 1),
(15, 'New York', 1),
(9, 'Newark', 1),
(11, 'Philadelphia', 1),
(5, 'San Francisco', 1),
(6, 'St. Croix', 1),
(13, 'West Baden Springs', 1),
(16, 'Sombor', 1),
(17, 'Liubliana', 1),
(18, 'Atenas', 1),
(19, 'Toronto', 1),
(20, 'San Luis', 1),
(21, 'Le Chesnay', 1),
(22, 'Atlanta', 1),
(23, 'New Brunswick', 1),
(24, 'Washington', 1),
(25, 'Yaundé', 1),
(26, 'Grand Rapids', 1),
(27, 'Elmsford', 1),
(28, 'Oshkosh', 1),
(29, 'Dalzell', 1),
(30, 'Melbourne', 1),
(31, 'Marietta', 1),
(32, 'Portland', 1),
(33, 'Seattle', 1),
(34, 'Arlington', 1),
(35, 'Lubbock', 1),
(36, 'Nueva Orleans', 1),
(37, 'Edison', 1),
(38, 'Giresun', 1),
(39, 'Minneapolis', 1),
(40, 'Garland', 1),
(41, 'Newport', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conferencias`
--

CREATE TABLE `conferencias` (
  `idconferencia` tinyint(3) UNSIGNED NOT NULL,
  `nomconferencia` char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `conferencias`
--

INSERT INTO `conferencias` (`idconferencia`, `nomconferencia`) VALUES
(1, 'Este'),
(2, 'Oeste');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratos`
--

CREATE TABLE `contratos` (
  `idequipo` tinyint(3) UNSIGNED NOT NULL,
  `idjugador` smallint(5) UNSIGNED NOT NULL,
  `fecfirmacontrato` date NOT NULL,
  `feciniciocontrato` date NOT NULL,
  `fecfincontrato` date NOT NULL,
  `salario` float(10,2) UNSIGNED NOT NULL,
  `doccontrato` varchar(30) NOT NULL,
  `idposicion` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Estos son los partidos previstos.';

--
-- Volcado de datos para la tabla `contratos`
--

INSERT INTO `contratos` (`idequipo`, `idjugador`, `fecfirmacontrato`, `feciniciocontrato`, `fecfincontrato`, `salario`, `doccontrato`, `idposicion`) VALUES
(6, 1, '2021-08-06', '2022-07-01', '2026-06-30', 55000000.00, 'docscontrato/curry2021.pdf', 1),
(3, 4, '2025-08-02', '2025-07-01', '2028-06-30', 54000000.00, 'docscontrato/doncic2025.pdf', 1),
(4, 7, '2024-07-01', '2025-07-01', '2030-06-30', 54000000.00, 'docscontrato/tatum2024.pdf', 3),
(1, 10, '2024-07-12', '2025-07-01', '2029-06-30', 39000000.00, 'docscontrato/brunson2024.pdf', 1),
(2, 23, '2024-07-07', '2025-07-01', '2030-06-30', 45000000.00, 'docscontrato/cade2024.pdf', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `divisiones`
--

CREATE TABLE `divisiones` (
  `iddivision` tinyint(3) UNSIGNED NOT NULL,
  `nomdivision` varchar(20) NOT NULL,
  `idconferencia` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `divisiones`
--

INSERT INTO `divisiones` (`iddivision`, `nomdivision`, `idconferencia`) VALUES
(1, 'División Atlántico', 1),
(2, 'División Central', 1),
(3, 'División Sureste', 1),
(4, 'División Noroeste', 2),
(5, 'División Pacífico', 2),
(6, 'División Suroeste', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `idequipo` tinyint(3) UNSIGNED NOT NULL,
  `nomequipo` varchar(30) NOT NULL,
  `logoequipo` char(20) NOT NULL,
  `nomestadio` varchar(30) NOT NULL,
  `numespectadores` smallint(5) UNSIGNED NOT NULL,
  `iddivision` tinyint(3) UNSIGNED NOT NULL,
  `idciudad` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`idequipo`, `nomequipo`, `logoequipo`, `nomestadio`, `numespectadores`, `iddivision`, `idciudad`) VALUES
(1, 'New York Knicks', 'images/logos/KNI.svg', 'Madison Square Garden', 29000, 1, 15),
(2, 'Detroit Pistons', 'images/logos/PIS.svg', 'Little Caesars Arena', 31000, 2, 12),
(3, 'Los Ángeles Lakers', 'images/logos/LAL.svg', 'Staples Center', 30000, 5, 1),
(4, 'Boston Celtics', 'images/logos/BOS.svg', 'TD Garden', 19580, 1, 2),
(5, 'Chicago Bulls', 'images/logos/BUL.svg', 'United Center', 25000, 2, 4),
(6, 'Golden State Warriors', 'images/logos/WAR.svg', 'Chase Center', 28800, 5, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes`
--

CREATE TABLE `imagenes` (
  `idimagen` mediumint(8) UNSIGNED NOT NULL,
  `nomimagen` char(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenesjugadores`
--

CREATE TABLE `imagenesjugadores` (
  `idjugador` smallint(5) UNSIGNED NOT NULL,
  `idimagen` mediumint(8) UNSIGNED NOT NULL,
  `iordenjugador` smallint(5) UNSIGNED NOT NULL COMMENT 'Para ordenar las fotos del jugador.',
  `iordenaparicion` tinyint(3) UNSIGNED NOT NULL COMMENT 'Para ordenar los jugadores en una foto.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `idjugador` smallint(5) UNSIGNED NOT NULL,
  `nomjugador` varchar(30) NOT NULL,
  `iorden` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `fecnacimiento` date NOT NULL,
  `altura` tinyint(3) UNSIGNED NOT NULL,
  `idposicion` tinyint(3) UNSIGNED NOT NULL,
  `idciudad` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`idjugador`, `nomjugador`, `iorden`, `fecnacimiento`, `altura`, `idposicion`, `idciudad`) VALUES
(1, 'Stephen Curry2', 1, '1988-03-14', 188, 1, 7),
(2, 'LeBron James', 1, '1984-12-30', 206, 3, 7),
(3, 'Nikola Jokić', 1, '1995-02-19', 211, 5, 16),
(4, 'Luka Dončić', 1, '1999-02-28', 198, 1, 17),
(5, 'Giannis Antetokounmpo', 1, '1994-12-06', 211, 4, 18),
(6, 'Shai Gilgeous-Alexander', 1, '1998-07-12', 198, 1, 19),
(7, 'Jayson Tatum', 1, '1998-03-03', 203, 3, 20),
(8, 'Victor Wembanyama', 1, '2004-01-04', 224, 5, 21),
(9, 'Anthony Edwards', 1, '2001-08-05', 193, 2, 22),
(10, 'Jalen Brunson', 1, '1996-08-31', 188, 1, 23),
(11, 'Kevin Durant', 1, '1988-09-29', 211, 3, 24),
(12, 'Joel Embiid', 1, '1994-03-16', 213, 5, 25),
(13, 'Devin Booker', 1, '1996-10-30', 196, 2, 26),
(14, 'Donovan Mitchell', 1, '1996-09-07', 191, 2, 27),
(15, 'Tyrese Haliburton', 1, '2000-02-29', 196, 1, 28),
(16, 'Ja Morant', 1, '1999-08-10', 188, 1, 29),
(17, 'Anthony Davis', 1, '1993-03-11', 208, 4, 4),
(18, 'Kyrie Irving', 1, '1992-03-23', 188, 1, 30),
(19, 'Jaylen Brown', 1, '1996-10-24', 198, 2, 31),
(20, 'Bam Adebayo', 1, '1997-07-18', 206, 5, 9),
(21, 'Domantas Sabonis', 1, '1996-05-03', 208, 5, 32),
(22, 'Paolo Banchero', 1, '2002-11-12', 208, 4, 33),
(23, 'Cade Cunningham', 1, '2001-09-25', 198, 1, 34),
(24, 'Trae Young', 1, '1998-09-19', 185, 1, 35),
(25, 'De\'Aaron Fox', 1, '1997-12-20', 191, 1, 36),
(26, 'Karl-Anthony Towns', 1, '1995-11-15', 213, 5, 37),
(27, 'Alperen Şengün', 1, '2002-07-25', 211, 5, 38),
(28, 'Chet Holmgren', 1, '2002-05-01', 216, 4, 39),
(29, 'Tyrese Maxey', 1, '2000-11-04', 188, 2, 40),
(30, 'Cooper Flagg', 1, '2006-12-21', 206, 3, 41);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE `partidos` (
  `idequipolocal` tinyint(3) UNSIGNED NOT NULL,
  `idequipovisitante` tinyint(3) UNSIGNED NOT NULL,
  `fecpartido` date NOT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidoscelebrados`
--

CREATE TABLE `partidoscelebrados` (
  `idequipolocal` tinyint(3) UNSIGNED NOT NULL,
  `idequipovisitante` tinyint(3) UNSIGNED NOT NULL,
  `fecpartido` date NOT NULL,
  `resultadolocal` tinyint(3) UNSIGNED NOT NULL,
  `resultadovisitante` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posiciones`
--

CREATE TABLE `posiciones` (
  `idposicion` tinyint(3) UNSIGNED NOT NULL,
  `nomposicion` varchar(10) NOT NULL COMMENT 'Base, Escolta, Alero, Ala-pivot, Pivot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `posiciones`
--

INSERT INTO `posiciones` (`idposicion`, `nomposicion`) VALUES
(4, 'Ala-pivot'),
(3, 'Alero'),
(1, 'Base'),
(2, 'Escolta'),
(5, 'Pivot');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `amistadjugadores`
--
ALTER TABLE `amistadjugadores`
  ADD PRIMARY KEY (`idjugador`,`idjugador2`),
  ADD KEY `fk_AMISTADJUGADORES_idjugador2` (`idjugador2`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`idciudad`),
  ADD UNIQUE KEY `uniqueciudad` (`nomciudad`,`iorden`);

--
-- Indices de la tabla `conferencias`
--
ALTER TABLE `conferencias`
  ADD PRIMARY KEY (`idconferencia`),
  ADD UNIQUE KEY `nomconferencia` (`nomconferencia`);

--
-- Indices de la tabla `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`idequipo`,`idjugador`,`fecfirmacontrato`),
  ADD KEY `fk_CONTRATOS_idjugador` (`idjugador`),
  ADD KEY `fk_CONTRATOS_idposicion` (`idposicion`);

--
-- Indices de la tabla `divisiones`
--
ALTER TABLE `divisiones`
  ADD PRIMARY KEY (`iddivision`),
  ADD UNIQUE KEY `nomdivision` (`nomdivision`),
  ADD KEY `fk_DIVISIONES_idconferencia` (`idconferencia`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`idequipo`),
  ADD UNIQUE KEY `nomequipo` (`nomequipo`),
  ADD KEY `fk_EQUIPOS_iddivision` (`iddivision`),
  ADD KEY `fk_EQUIPOS_idciudad` (`idciudad`);

--
-- Indices de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  ADD PRIMARY KEY (`idimagen`);

--
-- Indices de la tabla `imagenesjugadores`
--
ALTER TABLE `imagenesjugadores`
  ADD PRIMARY KEY (`idjugador`,`idimagen`),
  ADD KEY `fk_IMAGENESJUGADORES_idimagen` (`idimagen`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`idjugador`),
  ADD UNIQUE KEY `uniquejugador` (`nomjugador`,`iorden`),
  ADD KEY `fk_JUGADORES_idposicion` (`idposicion`),
  ADD KEY `fk_JUGADORES_idciudad` (`idciudad`);

--
-- Indices de la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD PRIMARY KEY (`idequipolocal`,`idequipovisitante`,`fecpartido`),
  ADD KEY `fk_PARTIDOS_idequipovisitante` (`idequipovisitante`);

--
-- Indices de la tabla `partidoscelebrados`
--
ALTER TABLE `partidoscelebrados`
  ADD PRIMARY KEY (`idequipolocal`,`idequipovisitante`,`fecpartido`);

--
-- Indices de la tabla `posiciones`
--
ALTER TABLE `posiciones`
  ADD PRIMARY KEY (`idposicion`),
  ADD UNIQUE KEY `nomposicion` (`nomposicion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `idciudad` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `conferencias`
--
ALTER TABLE `conferencias`
  MODIFY `idconferencia` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `divisiones`
--
ALTER TABLE `divisiones`
  MODIFY `iddivision` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `idequipo` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  MODIFY `idimagen` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `idjugador` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `posiciones`
--
ALTER TABLE `posiciones`
  MODIFY `idposicion` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `amistadjugadores`
--
ALTER TABLE `amistadjugadores`
  ADD CONSTRAINT `fk_AMISTADJUGADORES_idjugador` FOREIGN KEY (`idjugador`) REFERENCES `jugadores` (`idjugador`),
  ADD CONSTRAINT `fk_AMISTADJUGADORES_idjugador2` FOREIGN KEY (`idjugador2`) REFERENCES `jugadores` (`idjugador`);

--
-- Filtros para la tabla `contratos`
--
ALTER TABLE `contratos`
  ADD CONSTRAINT `fk_CONTRATOS_idequipo` FOREIGN KEY (`idequipo`) REFERENCES `equipos` (`idequipo`),
  ADD CONSTRAINT `fk_CONTRATOS_idjugador` FOREIGN KEY (`idjugador`) REFERENCES `jugadores` (`idjugador`),
  ADD CONSTRAINT `fk_CONTRATOS_idposicion` FOREIGN KEY (`idposicion`) REFERENCES `posiciones` (`idposicion`);

--
-- Filtros para la tabla `divisiones`
--
ALTER TABLE `divisiones`
  ADD CONSTRAINT `fk_DIVISIONES_idconferencia` FOREIGN KEY (`idconferencia`) REFERENCES `conferencias` (`idconferencia`);

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `fk_EQUIPOS_idciudad` FOREIGN KEY (`idciudad`) REFERENCES `ciudades` (`idciudad`),
  ADD CONSTRAINT `fk_EQUIPOS_iddivision` FOREIGN KEY (`iddivision`) REFERENCES `divisiones` (`iddivision`);

--
-- Filtros para la tabla `imagenesjugadores`
--
ALTER TABLE `imagenesjugadores`
  ADD CONSTRAINT `fk_IMAGENESJUGADORES_idimagen` FOREIGN KEY (`idimagen`) REFERENCES `imagenes` (`idimagen`),
  ADD CONSTRAINT `fk_IMAGENESJUGADORES_idjugador` FOREIGN KEY (`idjugador`) REFERENCES `jugadores` (`idjugador`);

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `fk_JUGADORES_idciudad` FOREIGN KEY (`idciudad`) REFERENCES `ciudades` (`idciudad`),
  ADD CONSTRAINT `fk_JUGADORES_idposicion` FOREIGN KEY (`idposicion`) REFERENCES `posiciones` (`idposicion`);

--
-- Filtros para la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD CONSTRAINT `fk_PARTIDOS_idequipolocal` FOREIGN KEY (`idequipolocal`) REFERENCES `equipos` (`idequipo`),
  ADD CONSTRAINT `fk_PARTIDOS_idequipovisitante` FOREIGN KEY (`idequipovisitante`) REFERENCES `equipos` (`idequipo`);

--
-- Filtros para la tabla `partidoscelebrados`
--
ALTER TABLE `partidoscelebrados`
  ADD CONSTRAINT `fk_PARTIDOSCELEBRADOS_idequipolocal` FOREIGN KEY (`idequipolocal`,`idequipovisitante`,`fecpartido`) REFERENCES `partidos` (`idequipolocal`, `idequipovisitante`, `fecpartido`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

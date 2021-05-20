-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 03-08-2011 a las 13:18:59
-- Versión del servidor: 5.1.41
-- Versión de PHP: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `desaSGNP`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Contacto`
--

CREATE TABLE IF NOT EXISTS `Contacto` (
  `IdContacto` int(11) NOT NULL,
  `NombreContacto` varchar(100) NOT NULL,
  `EMailContacto` varchar(50) DEFAULT NULL,
  `TelefonoContacto` varchar(40) DEFAULT NULL,
  `Centrex` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`IdContacto`),
  UNIQUE KEY `NombreContacto` (`NombreContacto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Contacto`
--

INSERT INTO `Contacto` (`IdContacto`, `NombreContacto`, `EMailContacto`, `TelefonoContacto`, `Centrex`) VALUES
(5, 'Junta de Escalafonamiento Docente - Primaria', 'juntaprimzn@santafe.edu.ar', '4572519 int. 32', '*2519'),
(6, 'Junta de Escalafonamiento Docente - Inicial', 'juntainicialzn@santafe.edu.ar', '4572519 int. 31', '*2519'),
(7, 'Junta de Escalafonamiento Docente - Especial', 'juntaespecial@santafe.edu.ar', '4572519 int. 33', '*2519'),
(9, 'Departamento de Títulos', '', '4506600/6800 int. 2196', '*6600/6800'),
(10, 'Dirección de Primaria', 'primaria@santafe.edu.ar', '4506600', ''),
(11, 'Dirección de Inicial', '', '4506800', ''),
(12, 'Direccion de Especial', '', '', '*6799');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ContactoNorma`
--

CREATE TABLE IF NOT EXISTS `ContactoNorma` (
  `IdContacto` int(11) NOT NULL,
  `IdNorma` int(11) NOT NULL,
  PRIMARY KEY (`IdContacto`,`IdNorma`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `ContactoNorma`
--

INSERT INTO `ContactoNorma` (`IdContacto`, `IdNorma`) VALUES
(5, 3),
(6, 2),
(11, 2),
(12, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ContactoPauta`
--

CREATE TABLE IF NOT EXISTS `ContactoPauta` (
  `IdContacto` int(11) NOT NULL,
  `IdPauta` int(11) NOT NULL,
  PRIMARY KEY (`IdContacto`,`IdPauta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `ContactoPauta`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ItemPauta`
--

CREATE TABLE IF NOT EXISTS `ItemPauta` (
  `IdPauta` int(11) NOT NULL,
  `IdNivel` smallint(6) NOT NULL,
  `Descripcion` text NOT NULL,
  `Observacion` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`IdPauta`,`IdNivel`),
  FULLTEXT KEY `Descripcion` (`Descripcion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `ItemPauta`
--

INSERT INTO `ItemPauta` (`IdPauta`, `IdNivel`, `Descripcion`, `Observacion`) VALUES
(5, 1, 'Los examenes para Asistentes escolares se realizaran el 18/02/2011. Con la debida antelación se publicaran los padrones que definen en que establecimiento, aula y horario rinde cada aspirante', ''),
(5, 2, 'prueba', 'prueba');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `LogAbm`
--

CREATE TABLE IF NOT EXISTS `LogAbm` (
  `IdLog` bigint(20) NOT NULL,
  `FeLog` date NOT NULL,
  `IdRegistro` int(11) NOT NULL,
  `DatoOriginal` text NOT NULL,
  `IdTabla` smallint(6) NOT NULL,
  `IdTipoAccion` smallint(6) NOT NULL,
  `Usuario` varchar(20) NOT NULL,
  PRIMARY KEY (`IdLog`),
  KEY `FeLog` (`FeLog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `LogAbm`
--

INSERT INTO `LogAbm` (`IdLog`, `FeLog`, `IdRegistro`, `DatoOriginal`, `IdTabla`, `IdTipoAccion`, `Usuario`) VALUES
(9, '2011-07-04', 12, 'eliminar', 3, 3, 'root'),
(5, '2011-07-04', 4, 'Norma-10', 5, 1, 'root'),
(8, '2011-07-04', 4, 'Pauta-10', 5, 1, 'root'),
(7, '2011-07-04', 4, 'Norma-11', 5, 1, 'root'),
(10, '2011-07-04', 12, 'eliminar', 3, 2, 'root'),
(11, '2011-07-04', 11, 'Prueba nivel 2', 3, 2, 'root'),
(12, '2011-07-04', 10, 'Prueba nivel', 3, 2, 'root'),
(13, '2011-07-04', 13, 'Prueba nivel', 3, 1, 'root'),
(14, '2011-07-04', 14, 'Prueba nivel 2', 3, 1, 'root'),
(15, '2011-07-04', 14, 'Prueba nivel 2', 3, 3, 'root'),
(16, '2011-07-04', 14, 'Prueba nivel 2', 3, 3, 'root'),
(17, '2011-07-04', 13, 'Prueba nivel', 3, 3, 'root'),
(18, '2011-07-04', 13, 'Prueba nivel', 3, 2, 'root'),
(19, '2011-07-04', 14, 'Prueba nivel 2', 3, 2, 'root'),
(20, '2011-07-04', 15, 'PARA ELIMINAR', 3, 1, 'root'),
(21, '2011-07-04', 15, 'PARA ELIMINAR', 3, 2, 'root'),
(22, '2011-07-04', 10, 'pautita', 4, 3, 'root'),
(23, '2011-07-04', 11, 'prueba log', 4, 1, 'root'),
(24, '2011-07-04', 11, 'prueba log', 4, 2, 'root'),
(25, '2011-07-04', 18, 'prueba', 7, 1, 'root'),
(28, '2011-07-04', 15, 'Para Eliminar', 1, 1, 'root'),
(27, '2011-07-04', 8, 'Modos de Control', 7, 2, 'root'),
(29, '2011-07-04', 15, 'Para Eliminar2', 1, 3, 'root'),
(30, '2011-07-04', 15, 'Para Eliminar2', 1, 2, 'root'),
(31, '2011-07-10', 10, 'pautita', 4, 3, 'root'),
(32, '2011-07-11', 19, 'eliminar', 7, 1, 'root'),
(33, '2011-07-11', 19, 'eliminar', 7, 2, 'root'),
(34, '2011-07-11', 2, 'Norma-1', 5, 1, 'root'),
(35, '2011-07-12', 1, 'Norma-2', 5, 1, 'root'),
(36, '2011-07-12', 2, 'Norma-1', 5, 1, 'root'),
(37, '2011-07-12', 3, 'Norma-5', 5, 1, 'root'),
(38, '2011-07-12', 3, 'Pauta-10', 5, 1, 'root'),
(39, '2011-07-12', 3, 'Pauta-10', 5, 1, 'root'),
(40, '2011-07-18', 16, 'Norma de Prueba', 3, 1, 'root'),
(41, '2011-07-18', 12, 'Pauta de Prueba', 4, 1, 'root'),
(42, '2011-07-18', 13, 'Pauta de Prueba 2', 4, 1, 'root'),
(43, '2011-07-18', 13, 'Pauta de Prueba 2', 4, 2, 'root'),
(44, '2011-07-18', 17, 'Norma de Prueba 2', 3, 1, 'root'),
(45, '2011-07-18', 18, 'Norma de Prueba 2', 3, 1, 'root'),
(46, '2011-07-18', 18, 'Norma de Prueba 2', 3, 2, 'root'),
(47, '2011-07-18', 14, 'Pauta Tema', 4, 1, 'agilli'),
(48, '2011-07-18', 19, 'Prueba Select', 3, 1, 'agilli'),
(49, '2011-07-18', 15, 'Pru Calendario', 4, 1, 'agilli'),
(50, '2011-07-18', 20, 'Prueba nivel 3', 3, 1, 'agilli'),
(51, '2011-07-18', 21, 'Prueba 4', 3, 1, 'agilli'),
(52, '2011-07-19', 22, 'prueba final ', 3, 1, 'agilli'),
(53, '2011-07-19', 16, 'calen', 4, 1, 'root'),
(54, '2011-07-19', 16, 'calen', 4, 3, 'root'),
(55, '2011-07-19', 16, 'calen', 4, 2, 'root'),
(56, '2011-07-19', 23, 'superior', 3, 1, 'root'),
(57, '2011-07-19', 23, 'superior', 3, 3, 'root'),
(58, '2011-07-19', 10, 'pautita', 4, 3, 'root'),
(59, '2011-07-19', 23, 'superior', 3, 3, 'root'),
(60, '2011-07-19', 15, 'Pru Calendario', 4, 3, 'root'),
(61, '2011-07-19', 24, 'sdf', 3, 1, 'agilli'),
(62, '2011-07-19', 24, '4to y 5to turno examen', 3, 3, 'agilli'),
(63, '2011-07-19', 4, 'Norma-24', 5, 1, 'agilli'),
(64, '2011-07-19', 17, 'Pautaso', 4, 1, 'agilli'),
(65, '2011-07-19', 5, 'Pauta-17', 5, 1, 'agilli'),
(66, '2011-07-19', 20, 'Tema de Prueba', 7, 1, 'agilli'),
(67, '2011-07-19', 13, 'pepe lui', 1, 3, 'agilli'),
(68, '2011-07-19', 24, '4to y 5to turno examen', 3, 3, 'agilli'),
(69, '2011-07-20', 25, 'Prueba Firefox', 3, 1, 'root'),
(70, '2011-07-20', 3, 'Norma-25', 5, 1, 'root'),
(71, '2011-07-20', 21, 'dddddddddddd', 7, 1, 'root'),
(72, '2011-07-20', 22, 'kkkkkkkkkk', 7, 1, 'root'),
(73, '2011-07-24', 10, 'pautita', 4, 2, 'root'),
(74, '2011-07-25', 26, 'Norma de Prueba', 3, 1, 'root'),
(75, '2011-07-25', 26, 'Norma de Prueba', 3, 2, 'root'),
(76, '2011-07-25', 18, 'Pauta de Prueba', 4, 1, 'root'),
(77, '2011-07-25', 18, 'Pauta de Prueba', 4, 2, 'root'),
(78, '2011-07-26', 15, 'Pru Calendario', 4, 2, 'root'),
(79, '2011-07-26', 22, 'kkkkkkkkkk', 7, 2, 'root'),
(80, '2011-07-26', 21, 'dddddddddddd', 7, 2, 'root'),
(81, '2011-07-26', 23, 'Tema de Prueba2', 7, 1, 'root'),
(82, '2011-07-26', 23, 'Tema de Prueba2', 7, 2, 'root'),
(83, '2011-07-28', 19, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 4, 1, 'root'),
(84, '2011-07-28', 19, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 4, 2, 'root'),
(85, '2011-07-31', 27, 'Prueba ajax', 3, 1, 'root'),
(86, '2011-07-31', 28, 'Prueba ajax', 3, 1, 'root'),
(87, '2011-07-31', 29, 'Prueba ajax', 3, 1, 'root'),
(88, '2011-07-31', 30, 'Prueba ajax', 3, 1, 'root'),
(89, '2011-07-31', 31, 'prueba ajax 2', 3, 1, 'root'),
(90, '2011-07-31', 32, 'title', 3, 1, 'root'),
(91, '2011-07-31', 32, 'title', 3, 2, 'root'),
(92, '2011-08-01', 24, 'ppp', 7, 1, 'root'),
(93, '2011-08-01', 25, 'pppp', 7, 1, 'root'),
(94, '2011-08-01', 26, 'ppppp', 7, 1, 'root'),
(95, '2011-08-01', 27, 'qq', 7, 1, 'root'),
(96, '2011-08-01', 28, 'qqq', 7, 1, 'root'),
(97, '2011-08-01', 29, 'qqqq', 7, 1, 'root'),
(98, '2011-08-01', 30, 'prueba', 7, 1, 'root'),
(99, '2011-08-01', 26, 'ppppp', 7, 2, 'root'),
(100, '2011-08-01', 27, 'qq', 7, 2, 'root'),
(101, '2011-08-01', 28, 'qqq', 7, 2, 'root'),
(102, '2011-08-01', 29, 'qqqq', 7, 2, 'root'),
(103, '2011-08-01', 30, 'prueba', 7, 2, 'root'),
(104, '2011-08-01', 25, 'pppp', 7, 2, 'root'),
(105, '2011-08-01', 24, 'ppp', 7, 2, 'root'),
(106, '2011-08-01', 31, 'prueba', 7, 1, 'root'),
(107, '2011-08-02', 20, 'AddConcurso', 4, 1, 'root'),
(108, '2011-08-02', 33, 'Nada', 3, 1, 'root'),
(109, '2011-08-02', 34, 'Testing', 3, 1, 'root'),
(110, '2011-08-02', 34, 'Testing', 3, 3, 'root'),
(111, '2011-08-02', 34, 'Testing', 3, 3, 'root'),
(112, '2011-08-02', 16, 'testing', 1, 1, 'root'),
(113, '2011-08-02', 32, 'canción', 7, 1, 'root'),
(114, '2011-08-02', 33, 'acento - guión', 7, 1, 'root'),
(115, '2011-08-02', 33, 'acento - guión', 7, 2, 'root'),
(116, '2011-08-03', 35, 'testeo nombre 4', 3, 1, 'root'),
(117, '2011-08-03', 35, 'guión - nombre 4', 3, 3, 'root'),
(118, '2011-08-03', 5, 'Pauta-12', 5, 1, 'agilli'),
(119, '2011-08-03', 5, 'Pauta-12', 5, 1, 'agilli'),
(120, '2011-08-03', 17, 'test', 1, 1, 'agilli'),
(121, '2011-08-03', 36, 'Resolución 1470', 3, 1, 'root'),
(122, '2011-08-03', 36, 'Resolución 1470', 3, 3, 'root'),
(123, '2011-08-03', 36, 'Resolución 1470', 3, 3, 'root'),
(124, '2011-08-03', 2, 'Resolución 1470', 3, 3, 'root'),
(125, '2011-08-03', 36, 'probando como quedo', 3, 3, 'root'),
(126, '2011-08-03', 36, 'probando como quedo', 3, 2, 'root'),
(127, '2011-08-03', 37, 'pepita', 3, 1, 'root'),
(128, '2011-08-03', 37, 'pepita', 3, 2, 'root'),
(129, '2011-08-03', 16, 'Norma de Prueba', 3, 2, 'agilli'),
(130, '2011-08-03', 17, 'Norma de Prueba 2', 3, 2, 'agilli'),
(131, '2011-08-03', 19, 'Prueba Select', 3, 2, 'agilli'),
(132, '2011-08-03', 20, 'Prueba nivel 3', 3, 2, 'agilli'),
(133, '2011-08-03', 21, 'Prueba 4', 3, 2, 'agilli'),
(134, '2011-08-03', 22, 'prueba final ', 3, 2, 'agilli'),
(135, '2011-08-03', 23, 'superior', 3, 2, 'agilli'),
(136, '2011-08-03', 25, 'Prueba Firefox', 3, 2, 'agilli'),
(137, '2011-08-03', 33, 'Nada', 3, 2, 'agilli'),
(138, '2011-08-03', 34, 'Testing', 3, 2, 'agilli'),
(139, '2011-08-03', 35, 'guión - nombre 4', 3, 2, 'agilli'),
(140, '2011-08-03', 12, 'Pauta de Prueba', 4, 2, 'agilli'),
(141, '2011-08-03', 17, 'Pautaso', 4, 2, 'agilli'),
(142, '2011-08-03', 14, 'Pauta Tema', 4, 2, 'agilli'),
(143, '2011-08-03', 20, 'AddConcurso', 4, 2, 'agilli'),
(144, '2011-08-03', 7, 'Mesas Examinadoras', 7, 2, 'agilli'),
(145, '2011-08-03', 32, 'canción', 7, 2, 'agilli'),
(146, '2011-08-03', 31, 'prueba', 7, 2, 'agilli'),
(147, '2011-08-03', 20, 'Tema de Prueba', 7, 2, 'agilli'),
(148, '2011-08-03', 18, 'testing', 1, 1, 'agilli'),
(149, '2011-08-03', 19, 'testing', 1, 1, 'agilli'),
(150, '2011-08-03', 20, 'test', 1, 1, 'agilli');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `LogAbmUsr`
--

CREATE TABLE IF NOT EXISTS `LogAbmUsr` (
  `IdLog` int(11) NOT NULL,
  `FeLog` date NOT NULL,
  `Nick` varchar(20) NOT NULL,
  `IdTipoAnt` smallint(6) DEFAULT NULL,
  `IdTipoPos` smallint(6) DEFAULT NULL,
  `IdTipoAccion` smallint(6) NOT NULL,
  `Usuario` varchar(20) NOT NULL,
  PRIMARY KEY (`IdLog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `LogAbmUsr`
--

INSERT INTO `LogAbmUsr` (`IdLog`, `FeLog`, `Nick`, `IdTipoAnt`, `IdTipoPos`, `IdTipoAccion`, `Usuario`) VALUES
(1, '2011-07-05', 'prueba', 2, 1, 3, 'root'),
(4, '2011-07-05', 'sfarre', 1, NULL, 3, 'root'),
(5, '2011-07-05', 'mgilli', 1, 1, 2, 'root'),
(7, '2011-07-11', 'smontini', 1, NULL, 3, 'root'),
(10, '2011-07-11', 'prueba', 1, NULL, 3, 'root'),
(12, '2011-07-11', 'smontini', 1, NULL, 3, 'root'),
(15, '2011-07-12', 'pela022', 1, NULL, 3, 'root'),
(18, '2011-07-19', 'root', 3, 3, 3, 'agilli'),
(19, '2011-07-19', 'root', 3, 3, 3, 'agilli'),
(21, '2011-07-24', 'sapo pepe', 1, 1, 2, 'root'),
(22, '2011-07-24', 'sapo_pepe', 1, 1, 2, 'root'),
(23, '2011-07-24', 'cgilli', 1, 1, 3, 'root'),
(25, '2011-07-31', 'prueba espacio', 1, NULL, 3, 'root'),
(27, '2011-08-01', 'prueba', 1, NULL, 3, 'root'),
(34, '2011-08-02', 'agilli', 3, 3, 2, 'root'),
(39, '2011-08-02', 'cgilli', 1, 1, 3, 'root'),
(40, '2011-08-02', 'cgilli', 1, 1, 3, 'root'),
(42, '2011-08-02', 'smontini', 1, 1, 3, 'root'),
(43, '2011-08-02', 'smontini', 1, 1, 3, 'root');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `LogUsr`
--

CREATE TABLE IF NOT EXISTS `LogUsr` (
  `IdLog` bigint(20) NOT NULL,
  `FeLog` date NOT NULL,
  `IdTabla` smallint(6) NOT NULL,
  `IdRegistro` int(11) NOT NULL,
  `DatoObservado` text NOT NULL,
  `Usuario` varchar(20) NOT NULL,
  PRIMARY KEY (`IdLog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `LogUsr`
--

INSERT INTO `LogUsr` (`IdLog`, `FeLog`, `IdTabla`, `IdRegistro`, `DatoObservado`, `Usuario`) VALUES
(3, '2011-07-04', 4, 3, 'FAQs', 'root'),
(2, '2011-07-04', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(4, '2011-07-04', 4, 3, 'FAQs', 'root'),
(5, '2011-07-05', 3, 2, 'Resolución 1470', 'root'),
(6, '2011-07-08', 3, 2, 'Resolución 1470', 'agilli'),
(7, '2011-07-08', 3, 2, 'Resolución 1470', 'agilli'),
(8, '2011-07-08', 4, 3, 'FAQs', 'agilli'),
(9, '2011-07-08', 4, 2, 'Sigae - FAQs', 'agilli'),
(10, '2011-07-08', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(11, '2011-07-08', 4, 3, 'FAQs', 'root'),
(12, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(13, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(14, '2011-07-11', 3, 3, 'Resolución 1471', 'root'),
(15, '2011-07-11', 3, 3, 'Resolución 1471', 'root'),
(16, '2011-07-11', 3, 3, 'Resolución 1471', 'root'),
(17, '2011-07-11', 4, 3, 'FAQs', 'root'),
(18, '2011-07-11', 4, 3, 'FAQs', 'root'),
(19, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(20, '2011-07-11', 4, 5, 'Examenes', 'root'),
(21, '2011-07-11', 4, 3, 'FAQs', 'root'),
(22, '2011-07-11', 4, 3, 'FAQs', 'root'),
(23, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(24, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(25, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(26, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(27, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(28, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(29, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(30, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(31, '2011-07-11', 4, 3, 'FAQs', 'root'),
(32, '2011-07-11', 4, 3, 'FAQs', 'root'),
(33, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(34, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(35, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(36, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(37, '2011-07-11', 3, 5, 'Calendario Escolar 2011', 'root'),
(38, '2011-07-11', 3, 5, 'Calendario Escolar 2011', 'root'),
(39, '2011-07-11', 4, 3, 'FAQs', 'root'),
(40, '2011-07-11', 4, 3, 'FAQs', 'root'),
(41, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(42, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(43, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(44, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(45, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(46, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(47, '2011-07-11', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(48, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(49, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(50, '2011-07-11', 4, 3, 'FAQs', 'root'),
(51, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(52, '2011-07-11', 3, 2, 'Resolución 1470', 'root'),
(53, '2011-07-12', 3, 2, 'Resolución 1470', 'root'),
(54, '2011-07-12', 3, 2, 'Resolución 1470', 'root'),
(55, '2011-07-12', 4, 4, 'REDFIE - FAQs', 'root'),
(56, '2011-07-12', 4, 3, 'FAQs', 'root'),
(57, '2011-07-12', 4, 3, 'FAQs', 'root'),
(58, '2011-07-12', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(59, '2011-07-12', 3, 2, 'Resolución 1470', 'root'),
(60, '2011-07-12', 3, 2, 'Resolución 1470', 'root'),
(61, '2011-07-12', 3, 2, 'Resolución 1470', 'root'),
(62, '2011-07-12', 4, 3, 'FAQs', 'root'),
(63, '2011-07-12', 4, 3, 'FAQs', 'root'),
(64, '2011-07-12', 4, 5, 'Examenes', 'root'),
(65, '2011-07-12', 4, 5, 'Examenes', 'root'),
(66, '2011-07-12', 4, 5, 'Examenes', 'root'),
(67, '2011-07-12', 4, 5, 'Examenes', 'root'),
(68, '2011-07-12', 4, 5, 'Examenes', 'root'),
(69, '2011-07-12', 4, 5, 'Examenes', 'root'),
(70, '2011-07-13', 3, 2, 'Resolución 1470', 'root'),
(71, '2011-07-13', 4, 4, 'REDFIE - FAQs', 'root'),
(72, '2011-07-13', 4, 4, 'REDFIE - FAQs', 'root'),
(73, '2011-07-13', 3, 2, 'Resolución 1470', 'root'),
(74, '2011-07-13', 4, 10, 'pautita', 'root'),
(75, '2011-07-14', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(76, '2011-07-14', 3, 2, 'Resolución 1470', 'agilli'),
(77, '2011-07-14', 3, 2, 'Resolución 1470', 'agilli'),
(78, '2011-07-14', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(79, '2011-07-14', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(80, '2011-07-15', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(81, '2011-07-15', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(82, '2011-07-15', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(83, '2011-07-16', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(84, '2011-07-16', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(85, '2011-07-16', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(86, '2011-07-16', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(87, '2011-07-16', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(88, '2011-07-16', 3, 2, 'Resolución 1470', 'root'),
(89, '2011-07-16', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(90, '2011-07-16', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(91, '2011-07-16', 4, 2, 'Sigae - FAQs', 'root'),
(92, '2011-07-16', 4, 2, 'Sigae - FAQs', 'root'),
(93, '2011-07-16', 4, 3, 'FAQs', 'root'),
(94, '2011-07-16', 4, 4, 'REDFIE - FAQs', 'root'),
(95, '2011-07-16', 4, 5, 'Examenes', 'root'),
(96, '2011-07-16', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(97, '2011-07-16', 4, 10, 'pautita', 'root'),
(98, '2011-07-16', 4, 5, 'Examenes', 'root'),
(99, '2011-07-16', 4, 5, 'Examenes', 'root'),
(100, '2011-07-16', 4, 5, 'Examenes', 'root'),
(101, '2011-07-19', 3, 2, 'Resolución 1470', 'agilli'),
(102, '2011-07-19', 3, 2, 'Resolución 1470', 'agilli'),
(103, '2011-07-19', 3, 2, 'Resolución 1470', 'agilli'),
(104, '2011-07-19', 3, 2, 'Resolución 1470', 'agilli'),
(105, '2011-07-19', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(106, '2011-07-19', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(107, '2011-07-19', 4, 5, 'Examenes', 'agilli'),
(108, '2011-07-19', 3, 1, 'Anexo I Decreto 516/10', 'agilli'),
(109, '2011-07-19', 4, 10, 'pautita', 'agilli'),
(110, '2011-07-19', 4, 10, 'pautita', 'agilli'),
(111, '2011-07-19', 3, 24, '4to y 5to turno examen', 'agilli'),
(112, '2011-07-19', 3, 5, 'Calendario Escolar 2011', 'agilli'),
(113, '2011-07-19', 3, 24, '4to y 5to turno examen', 'agilli'),
(114, '2011-07-19', 4, 17, 'Pautaso', 'agilli'),
(115, '2011-07-19', 4, 17, 'Pautaso', 'agilli'),
(116, '2011-07-19', 4, 17, 'Pautaso', 'agilli'),
(117, '2011-07-20', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(118, '2011-07-20', 3, 1, 'Anexo I Decreto 516/10', 'root'),
(119, '2011-07-20', 4, 5, 'Examenes', 'root'),
(120, '2011-07-20', 3, 24, '4to y 5to turno examen', 'root'),
(121, '2011-07-20', 3, 24, '4to y 5to turno examen', 'root'),
(122, '2011-07-22', 3, 24, '4to y 5to turno examen', 'root'),
(123, '2011-07-24', 3, 2, 'Resolución 1470', 'cgilli'),
(124, '2011-07-24', 3, 1, 'Anexo I Decreto 516/10', 'cgilli'),
(125, '2011-07-24', 4, 2, 'Sigae - FAQs', 'cgilli'),
(126, '2011-07-24', 4, 5, 'Examenes', 'cgilli'),
(127, '2011-07-26', 4, 5, 'Examenes', 'root'),
(128, '2011-07-26', 3, 25, 'Prueba Firefox', 'root'),
(129, '2011-07-26', 3, 2, 'Resolución 1470', 'root'),
(130, '2011-07-26', 3, 25, 'Prueba Firefox', 'root'),
(131, '2011-07-26', 3, 24, '4to y 5to turno examen', 'root'),
(132, '2011-07-26', 4, 2, 'Sigae - FAQs', 'root'),
(133, '2011-07-26', 4, 3, 'FAQs', 'root'),
(134, '2011-07-27', 3, 17, 'Norma de Prueba 2', 'root'),
(135, '2011-07-27', 3, 17, 'Norma de Prueba 2', 'root'),
(136, '2011-07-27', 4, 5, 'Examenes', 'root'),
(137, '2011-07-28', 4, 5, 'Examenes', 'root'),
(138, '2011-07-30', 3, 2, 'Resolución 1470', 'root'),
(139, '2011-07-30', 3, 2, 'Resolución 1470', 'root'),
(140, '2011-07-30', 3, 4, 'Resolución 1472', 'root'),
(141, '2011-07-30', 4, 3, 'FAQs', 'root'),
(142, '2011-07-31', 3, 25, 'Prueba Firefox', 'root'),
(143, '2011-08-02', 3, 3, 'Resolución 1471', 'root'),
(144, '2011-08-02', 4, 5, 'Examenes', 'agilli'),
(145, '2011-08-03', 4, 2, 'Sigae - FAQs', 'agilli'),
(146, '2011-08-03', 3, 35, 'guión - nombre 4', 'root');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Nivel`
--

CREATE TABLE IF NOT EXISTS `Nivel` (
  `IdNivel` smallint(6) NOT NULL,
  `Nivel` varchar(40) NOT NULL,
  PRIMARY KEY (`IdNivel`),
  KEY `Nivel` (`Nivel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Nivel`
--

INSERT INTO `Nivel` (`IdNivel`, `Nivel`) VALUES
(2, 'Inicial'),
(3, 'Primario'),
(4, 'Especial'),
(5, 'Secundario'),
(6, 'Superior'),
(1, 'Todos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Norma`
--

CREATE TABLE IF NOT EXISTS `Norma` (
  `IdNorma` int(11) NOT NULL,
  `NroNorma` int(11) NOT NULL,
  `FeNorma` date NOT NULL,
  `TituloNorma` varchar(60) NOT NULL,
  `DescripcionNorma` text NOT NULL,
  `DocNorma` varchar(100) DEFAULT NULL,
  `Owner` varchar(20) NOT NULL,
  `IdTema` bigint(20) NOT NULL,
  `IdNivel` smallint(6) NOT NULL,
  PRIMARY KEY (`IdNorma`),
  KEY `TituloNorma` (`TituloNorma`),
  KEY `NroNorma` (`NroNorma`),
  KEY `FeNorma` (`FeNorma`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Norma`
--

INSERT INTO `Norma` (`IdNorma`, `NroNorma`, `FeNorma`, `TituloNorma`, `DescripcionNorma`, `DocNorma`, `Owner`, `IdTema`, `IdNivel`) VALUES
(1, 516, '2010-10-01', 'Anexo I Decreto 516/10', 'En este anexo se definen las pautas para la correcta inscripción de aspirantes al concurso de Asistentes Escolares.', './docs/normas/norma1.pdf', 'root', 4, 1),
(2, 1470, '2010-10-01', 'Resolución 1470', 'Pautas para la correcta inscripción de aspirantes al concurso de ingreso a la docencia en el nivel inicial.', './docs/normas/norma2.pdf', 'root', 3, 2),
(3, 1471, '2010-10-01', 'Resolución 1471', 'Esta resolución establece las pautas para la correcta inscripción de docentes al concurso de ingreso en el nivel Primario.', './docs/normas/norma3.pdf', 'root', 3, 3),
(4, 1472, '2010-10-01', 'Resolución 1472', 'Esta resolución establece las pautas para la correcta inscripción de docentes para el concurso de ingreso en la modalidad Especial.', './docs/normas/norma4.pdf', 'root', 3, 4),
(5, -1, '2010-12-28', 'Calendario Escolar 2011', 'Adelanto del Calendario escolar 2011.', './docs/normas/norma5.pdf', 'root', 6, 1),
(24, -1, '2011-02-19', '4to y 5to turno examen', 'examenes', './docs/normas/norma24.pdf', 'agilli', 6, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `NormaPauta`
--

CREATE TABLE IF NOT EXISTS `NormaPauta` (
  `IdNorma` int(11) NOT NULL,
  `IdPauta` int(11) NOT NULL,
  PRIMARY KEY (`IdNorma`,`IdPauta`)
) ENGINE=MyISAM DEFAULT CHARSET=ucs2;

--
-- Volcar la base de datos para la tabla `NormaPauta`
--

INSERT INTO `NormaPauta` (`IdNorma`, `IdPauta`) VALUES
(1, 5),
(2, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Novedad`
--

CREATE TABLE IF NOT EXISTS `Novedad` (
  `IdNovedad` smallint(6) NOT NULL,
  `IdNorma` int(11) DEFAULT NULL,
  `IdPauta` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdNovedad`)
) ENGINE=MyISAM DEFAULT CHARSET=ucs2;

--
-- Volcar la base de datos para la tabla `Novedad`
--

INSERT INTO `Novedad` (`IdNovedad`, `IdNorma`, `IdPauta`) VALUES
(1, 2, NULL),
(2, 1, NULL),
(3, NULL, NULL),
(4, 24, NULL),
(5, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Pauta`
--

CREATE TABLE IF NOT EXISTS `Pauta` (
  `IdPauta` int(11) NOT NULL,
  `FePauta` date NOT NULL,
  `NombrePauta` varchar(100) NOT NULL,
  `DocPauta` varchar(100) DEFAULT NULL,
  `IdTema` bigint(20) NOT NULL,
  `Owner` varchar(20) NOT NULL,
  PRIMARY KEY (`IdPauta`),
  KEY `FePauta` (`FePauta`),
  KEY `NombrePauta` (`NombrePauta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Pauta`
--

INSERT INTO `Pauta` (`IdPauta`, `FePauta`, `NombrePauta`, `DocPauta`, `IdTema`, `Owner`) VALUES
(2, '2009-08-06', 'Sigae - FAQs', './docs/pautas/pauta2.pdf', 2, 'agilli'),
(3, '2009-03-02', 'FAQs', './docs/pautas/pauta3.pdf', 1, 'root'),
(4, '2010-04-06', 'REDFIE - FAQs', './docs/pautas/pauta4.pdf', 2, 'agilli'),
(5, '2010-12-27', 'Examenes', NULL, 4, 'root');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SeqContacto`
--

CREATE TABLE IF NOT EXISTS `SeqContacto` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `SeqContacto`
--

INSERT INTO `SeqContacto` (`id`) VALUES
(20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SeqLogAbm`
--

CREATE TABLE IF NOT EXISTS `SeqLogAbm` (
  `id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `SeqLogAbm`
--

INSERT INTO `SeqLogAbm` (`id`) VALUES
(150);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SeqLogAbmUsr`
--

CREATE TABLE IF NOT EXISTS `SeqLogAbmUsr` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `SeqLogAbmUsr`
--

INSERT INTO `SeqLogAbmUsr` (`id`) VALUES
(51);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SeqLogUsr`
--

CREATE TABLE IF NOT EXISTS `SeqLogUsr` (
  `id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `SeqLogUsr`
--

INSERT INTO `SeqLogUsr` (`id`) VALUES
(146);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SeqNivel`
--

CREATE TABLE IF NOT EXISTS `SeqNivel` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcar la base de datos para la tabla `SeqNivel`
--

INSERT INTO `SeqNivel` (`id`) VALUES
(7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SeqNorma`
--

CREATE TABLE IF NOT EXISTS `SeqNorma` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `SeqNorma`
--

INSERT INTO `SeqNorma` (`id`) VALUES
(37);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SeqPauta`
--

CREATE TABLE IF NOT EXISTS `SeqPauta` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `SeqPauta`
--

INSERT INTO `SeqPauta` (`id`) VALUES
(20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SeqTema`
--

CREATE TABLE IF NOT EXISTS `SeqTema` (
  `id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `SeqTema`
--

INSERT INTO `SeqTema` (`id`) VALUES
(33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Sexo`
--

CREATE TABLE IF NOT EXISTS `Sexo` (
  `IdSexo` smallint(6) NOT NULL,
  `Sexo` varchar(10) NOT NULL,
  PRIMARY KEY (`IdSexo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Sexo`
--

INSERT INTO `Sexo` (`IdSexo`, `Sexo`) VALUES
(1, 'Femenino'),
(2, 'Masculino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tabla`
--

CREATE TABLE IF NOT EXISTS `Tabla` (
  `IdTabla` smallint(6) NOT NULL,
  `NomreTabla` varchar(20) NOT NULL,
  PRIMARY KEY (`IdTabla`),
  UNIQUE KEY `NomreTabla` (`NomreTabla`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Tabla`
--

INSERT INTO `Tabla` (`IdTabla`, `NomreTabla`) VALUES
(1, 'Contacto'),
(2, 'ItemPauta'),
(3, 'Norma'),
(4, 'Pauta'),
(5, 'Novedad'),
(6, 'Usuario'),
(7, 'Tema');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tema`
--

CREATE TABLE IF NOT EXISTS `Tema` (
  `IdTema` bigint(20) NOT NULL,
  `Tema` varchar(200) NOT NULL,
  PRIMARY KEY (`IdTema`),
  UNIQUE KEY `Tema` (`Tema`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Tema`
--

INSERT INTO `Tema` (`IdTema`, `Tema`) VALUES
(1, 'Conectividad'),
(2, 'Sistema de Gestión Escolar'),
(3, 'Concurso Docentes'),
(4, 'Concurso Asistentes Escolares'),
(6, 'Calendario Escolar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TipoAccion`
--

CREATE TABLE IF NOT EXISTS `TipoAccion` (
  `IdTipoAccion` smallint(6) NOT NULL,
  `Accion` varchar(20) NOT NULL,
  PRIMARY KEY (`IdTipoAccion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `TipoAccion`
--

INSERT INTO `TipoAccion` (`IdTipoAccion`, `Accion`) VALUES
(1, 'Alta'),
(2, 'Baja'),
(3, 'Modificación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TipoDocumento`
--

CREATE TABLE IF NOT EXISTS `TipoDocumento` (
  `IdTipoDoc` smallint(6) NOT NULL,
  `TipoDoc` varchar(40) NOT NULL,
  `ValorTipoDoc` varchar(4) NOT NULL,
  PRIMARY KEY (`IdTipoDoc`),
  UNIQUE KEY `ValorTipoDoc` (`ValorTipoDoc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `TipoDocumento`
--

INSERT INTO `TipoDocumento` (`IdTipoDoc`, `TipoDoc`, `ValorTipoDoc`) VALUES
(1, 'Libreta de Enrolamiento', 'LE'),
(2, 'Libreta Cívica', 'LC'),
(3, 'Documento Nacional de Identidad', 'DNI'),
(4, 'Pasaporte', 'PA'),
(5, 'Documento Único', 'DU');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TipoUsuario`
--

CREATE TABLE IF NOT EXISTS `TipoUsuario` (
  `IdTipoUsuario` smallint(6) NOT NULL,
  `TipoUsuario` varchar(30) NOT NULL,
  `ObsUsuario` text,
  PRIMARY KEY (`IdTipoUsuario`),
  UNIQUE KEY `TipoUsuario` (`TipoUsuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `TipoUsuario`
--

INSERT INTO `TipoUsuario` (`IdTipoUsuario`, `TipoUsuario`, `ObsUsuario`) VALUES
(1, 'Agente', 'Pueden realizar consultas sobre los datos.\r\nSólo pueden modificar datos de su perfil de usuario.'),
(2, 'Coordinador', 'Pueden realizar altas, bajas y modificaciones de documentos.\r\nPueden modificar datos personales de su perfil'),
(3, 'Administrador', 'Pueden realizar altas, bajas y modificaciones sobre los documentos asociados.\r\nPueden realizar altas, bajas y modificaciones de usuarios.\r\nPueden modificar datos perosonales de su perfil.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuario`
--

CREATE TABLE IF NOT EXISTS `Usuario` (
  `Nick` varchar(20) NOT NULL,
  `Password` varchar(120) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(30) NOT NULL,
  `NroDocumento` varchar(8) NOT NULL,
  `FeNacimiento` date DEFAULT NULL,
  `Domicilio` varchar(60) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Celular` varchar(20) DEFAULT NULL,
  `EMail` varchar(50) NOT NULL,
  `UrlFoto` varchar(100) NOT NULL,
  `IdTipoDoc` smallint(6) NOT NULL,
  `IdSexo` smallint(6) NOT NULL,
  `IdTipoUsuario` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`Nick`),
  UNIQUE KEY `NroDocumento` (`NroDocumento`),
  KEY `Apellido` (`Apellido`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Usuario`
--

INSERT INTO `Usuario` (`Nick`, `Password`, `Nombre`, `Apellido`, `NroDocumento`, `FeNacimiento`, `Domicilio`, `Telefono`, `Celular`, `EMail`, `UrlFoto`, `IdTipoDoc`, `IdSexo`, `IdTipoUsuario`) VALUES
('root', 'c74a689eaf6e4b801ad29bf6b45ce05a2323b62b', 'root', 'root', '-1', '0000-00-00', 'root', 'root', 'root', 'root@root.gov.ar', './imgs/avatars/v_avatar.png', 3, 2, 3),
('rcosta', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Rocío', 'Costa', '28996555', '1979-05-25', 'Rincón', '1234566', '', 'costarocio@gmail.com', './imgs/avatars/witch_avatar.png', 3, 1, 1),
('smontini', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Santiago', 'Montini', '34156333', '1987-06-19', '25 de Mayo y Mendoza', '(0342)4834455', '', 'smonti@hotmail.com', './imgs/avatars/v_avatar.png', 3, 2, 1),
('agilli', 'c74a689eaf6e4b801ad29bf6b45ce05a2323b62b', 'Andrés', 'Gilli', '30532101', '1983-12-29', 'La Rioja 3764', '4831612', '', 'aagilli20@gmail.com', './imgs/avatars/v_avatar.png', 3, 2, 3),
('cgilli', '689518a437d3b7dae0d486eb53c6ad7748bb000b', 'Carlos', 'Gilli', '13621793', '1986-06-04', 'La Rioja 3764', '154666898', '', 'carlosgilli@hotmail.com', './imgs/avatars/witch_avatar.png', 3, 1, 1),
('sgarcia', 'c74a689eaf6e4b801ad29bf6b45ce05a2323b62b', 'Sebastián', 'García', '12666333', '1972-08-10', '', '', '154666999', 'sgarcia@gmail.com', './imgs/avatars/v_avatar.png', 3, 2, NULL),
('sperez', 'c74a689eaf6e4b801ad29bf6b45ce05a2323b62b', 'Salvador', 'Perez', '10321666', '1960-10-10', '', '', '153999666', 'sperez@yahoo.com.ar', './imgs/avatars/v_avatar.png', 3, 2, NULL);

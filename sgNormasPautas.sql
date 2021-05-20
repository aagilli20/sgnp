-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 11-08-2011 a las 15:21:48
-- Versión del servidor: 5.1.41
-- Versión de PHP: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `sgNormasPautas`
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


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `LogAbm`
--

CREATE TABLE IF NOT EXISTS `LogAbm` (
  `IdLog` bigint(20) NOT NULL,
  `FeLog` date NOT NULL,
  `IdRegistro` varchar(20) NOT NULL,
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
(1, '2011-08-08', '1', 'Test', 1, 1, 'agilli');

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
(1, 'Todos'),
(2, 'Inicial'),
(3, 'Primario'),
(4, 'Especial'),
(5, 'Secundario'),
(6, 'Superior');

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
(1, NULL, NULL),
(2, NULL, NULL),
(3, NULL, NULL),
(4, NULL, NULL),
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
(1);

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
(1);

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
(0);

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
(0);

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
(0);

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
(0);

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
  KEY `Tema` (`Tema`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Tema`
--


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
(2, 'Administrador', 'Pueden realizar altas, bajas y modificaciones sobre los documentos asociados.\r\nPueden realizar altas, bajas y modificaciones de usuarios.\r\nPueden modificar datos perosonales de su perfil.');

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
('agilli', 'c74a689eaf6e4b801ad29bf6b45ce05a2323b62b', 'Andrés', 'Gilli', '30532101', '1983-12-29', 'La Rioja 3764', '4831612', '154621793', 'aagilli20@gmail.com', './imgs/avatars/v_avatar.png', 3, 2, 3),
('root', '9cd826ebed70aafd0ce782a99dd4692ff26f2269', 'root', 'root', '00000000', NULL, NULL, NULL, NULL, 'sigae@santafe.gov.ar', './avatars/fireman_avatar.png', 3, 2, 3);

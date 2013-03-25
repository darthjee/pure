-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 04, 2010 at 05:23 PM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `favini`
--

-- --------------------------------------------------------

--
-- Table structure for table `pure_grupos`
--

DROP TABLE IF EXISTS `pure_grupos`;
CREATE TABLE IF NOT EXISTS `pure_grupos` (
  `id_grupo` tinyint(1) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id do grupo de usuario',
  `name` varchar(30) NOT NULL COMMENT 'nome do grupo',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'data da ultima modificacao',
  `editor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'id do ultimo modificador',
  PRIMARY KEY (`id_grupo`),
  UNIQUE KEY `name` (`name`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem todos os grupos de usuarios possiveis' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `pure_grupos`
--

INSERT INTO `pure_grupos` (`id_grupo`, `name`, `timestamp`, `editor`) VALUES
(1, 'root', '2008-12-31 04:37:37', 1),
(2, 'adm', '2008-12-31 04:37:37', 1),
(3, 'editor', '2008-12-31 04:37:37', 1),
(4, 'user', '2008-12-31 04:37:37', 1),
(5, 'public', '2009-08-18 12:12:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pure_grupos_rel`
--

DROP TABLE IF EXISTS `pure_grupos_rel`;
CREATE TABLE IF NOT EXISTS `pure_grupos_rel` (
  `id_grupo_rel` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` tinyint(1) unsigned NOT NULL,
  `id_grupo` tinyint(1) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `editor` int(11) NOT NULL,
  PRIMARY KEY (`id_grupo_rel`),
  UNIQUE KEY `rel` (`id_parent`,`id_grupo`),
  KEY `id_parent` (`id_parent`),
  KEY `id_grupo` (`id_grupo`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem todos as relacoes da arvore de grupos' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pure_grupos_rel`
--

INSERT INTO `pure_grupos_rel` (`id_grupo_rel`, `id_parent`, `id_grupo`, `timestamp`, `editor`) VALUES
(1, 1, 2, '2009-08-18 12:16:52', 1),
(2, 2, 3, '2009-08-18 12:16:52', 1),
(3, 3, 4, '2009-08-18 12:17:13', 1),
(4, 4, 5, '2009-08-18 12:17:13', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pure_languages`
--

DROP TABLE IF EXISTS `pure_languages`;
CREATE TABLE IF NOT EXISTS `pure_languages` (
  `id_lang` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT 'nome do idioma',
  `key` varchar(8) NOT NULL COMMENT 'nome curto',
  `order` smallint(6) NOT NULL COMMENT 'ordem de importancia',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'horario da ultima modificacao',
  `editor` int(11) NOT NULL COMMENT 'id do ultimo editor',
  PRIMARY KEY (`id_lang`),
  UNIQUE KEY `order` (`order`),
  UNIQUE KEY `key` (`key`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem a lista de todas os idiomas possiveis' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pure_languages`
--

INSERT INTO `pure_languages` (`id_lang`, `name`, `key`, `order`, `timestamp`, `editor`) VALUES
(1, 'portugues', 'pt_br', 1, '2008-12-28 13:25:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pure_logs_violations`
--

DROP TABLE IF EXISTS `pure_logs_violations`;
CREATE TABLE IF NOT EXISTS `pure_logs_violations` (
  `id_log` int(10) NOT NULL AUTO_INCREMENT,
  `tipo` enum('ACS','MOD') NOT NULL COMMENT 'tipo de violacao',
  `ip` char(16) NOT NULL COMMENT 'ip do usuario',
  `id_user` int(10) DEFAULT '0' COMMENT 'id do usuario que causou o log',
  `coment` text COMMENT 'comentarios sobre como aconteceu a vilacao',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'timestamp do instante da violacao',
  PRIMARY KEY (`id_log`),
  KEY `id_user` (`id_user`),
  KEY `tipo` (`tipo`),
  KEY `ip` (`ip`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem o registro de todas as violacoes registradas ^^' AUTO_INCREMENT=39 ;

--
-- Dumping data for table `pure_logs_violations`
--

INSERT INTO `pure_logs_violations` (`id_log`, `tipo`, `ip`, `id_user`, `coment`, `time`) VALUES
(31, 'MOD', '200.18.248.2', 0, '/rpg/index.php?ajax=true&mad=menu&id=1', '2009-03-25 17:24:10'),
(32, 'MOD', '127.0.0.1', 0, '/pure/index.php?ajax=true&mod=content', '2009-07-08 16:02:15'),
(33, 'MOD', '127.0.0.1', 0, '/pure/index.php?ajax=true&mod=content', '2009-07-08 16:03:33'),
(34, 'MOD', '127.0.0.1', 0, '/pure/index.php?ajax=true&mod=%27register%27', '2009-07-08 16:03:54'),
(35, 'MOD', '127.0.0.1', 0, '/pure/index.php?ajax=true&mod=content', '2009-07-08 16:04:41'),
(36, 'MOD', '127.0.0.1', 0, '/pure/index.php?ajax=true&mod=0', '2009-07-08 16:07:28'),
(37, 'MOD', '127.0.0.1', 0, '/~darthjee/pure/index.php?ajax=true&mod=login&action=login', '2010-01-21 00:53:38'),
(38, 'MOD', '127.0.0.1', 0, '/~darthjee/pure/index.php?ajax=true&amp;mod=login&amp;action=login', '2010-01-24 17:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `pure_menus`
--

DROP TABLE IF EXISTS `pure_menus`;
CREATE TABLE IF NOT EXISTS `pure_menus` (
  `id_menu` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT 'nome do modulo',
  `id_parent` smallint(5) NOT NULL DEFAULT '0' COMMENT 'id do menu acima',
  `order` int(1) NOT NULL DEFAULT '128' COMMENT 'ordem de disposicao',
  `id_modulo` int(11) unsigned NOT NULL DEFAULT '0',
  `param` varchar(128) DEFAULT NULL COMMENT 'parametros a serem passados para o modulo',
  `type` enum('menu','tpl') NOT NULL DEFAULT 'menu' COMMENT 'tipo de menu',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `editor` int(11) NOT NULL COMMENT 'id do ultimo editor',
  PRIMARY KEY (`id_menu`),
  UNIQUE KEY `name` (`name`),
  KEY `id_modulo` (`id_modulo`),
  KEY `id_parent` (`id_parent`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem a lista de itens do menu' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `pure_menus`
--

INSERT INTO `pure_menus` (`id_menu`, `name`, `id_parent`, `order`, `id_modulo`, `param`, `type`, `timestamp`, `editor`) VALUES
(1, 'config', 0, 1, 0, NULL, 'menu', '2009-08-18 12:41:16', 0),
(2, 'admin', 0, 1, 0, NULL, 'menu', '2009-08-18 12:41:58', 0),
(3, 'register', 0, 255, 2, 'action=form', 'menu', '2009-08-21 10:57:59', 0),
(4, 'login', 0, 0, 3, NULL, 'tpl', '2010-01-19 18:04:09', 0),
(5, 'logout', 0, 128, 3, 'action=logout', 'menu', '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pure_menus_grupos`
--

DROP TABLE IF EXISTS `pure_menus_grupos`;
CREATE TABLE IF NOT EXISTS `pure_menus_grupos` (
  `id_rel` int(11) NOT NULL AUTO_INCREMENT,
  `id_menu` tinyint(3) NOT NULL,
  `id_grupo` tinyint(1) NOT NULL DEFAULT '1',
  `auth` enum('denial','open','excl') NOT NULL COMMENT 'tipo de busca de autorizacao',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `editor` int(11) NOT NULL,
  PRIMARY KEY (`id_rel`),
  KEY `id_menu` (`id_menu`),
  KEY `id_grupo` (`id_grupo`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`),
  KEY `id_menu_2` (`id_menu`,`id_grupo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem a relacao dos menus com os grupos' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `pure_menus_grupos`
--

INSERT INTO `pure_menus_grupos` (`id_rel`, `id_menu`, `id_grupo`, `auth`, `timestamp`, `editor`) VALUES
(3, 1, 1, 'excl', '2010-02-04 15:19:43', 1),
(2, 2, 2, 'open', '2009-08-18 12:44:09', 1),
(1, 0, 5, 'open', '2010-02-04 15:19:43', 0),
(4, 3, 5, 'excl', '2010-02-04 15:19:43', 1),
(5, 4, 5, 'excl', '2010-02-04 15:19:43', 0),
(6, 5, 4, 'open', '2010-02-04 15:19:43', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pure_menus_titles`
--

DROP TABLE IF EXISTS `pure_menus_titles`;
CREATE TABLE IF NOT EXISTS `pure_menus_titles` (
  `id_title` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_menu` smallint(6) NOT NULL,
  `id_lang` smallint(6) NOT NULL,
  `title` varchar(30) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `editor` int(11) NOT NULL COMMENT 'id do ultimo editor',
  PRIMARY KEY (`id_title`),
  UNIQUE KEY `trans` (`id_lang`,`id_menu`),
  KEY `id_menu` (`id_menu`),
  KEY `id_lang` (`id_lang`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Contem as diversas traducoes para os menus' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pure_menus_titles`
--


-- --------------------------------------------------------

--
-- Table structure for table `pure_mods_grupos`
--

DROP TABLE IF EXISTS `pure_mods_grupos`;
CREATE TABLE IF NOT EXISTS `pure_mods_grupos` (
  `id_rel` int(11) NOT NULL AUTO_INCREMENT,
  `id_mod` tinyint(6) NOT NULL,
  `id_grupo` tinyint(1) NOT NULL DEFAULT '1',
  `auth` enum('denial','open','excl') NOT NULL COMMENT 'tipo de busca de autorizacao',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `editor` int(11) NOT NULL,
  PRIMARY KEY (`id_rel`),
  KEY `id_menu` (`id_mod`),
  KEY `id_grupo` (`id_grupo`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`),
  KEY `id_menu_2` (`id_mod`,`id_grupo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem a relacao dos menus com os grupos' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pure_mods_grupos`
--

INSERT INTO `pure_mods_grupos` (`id_rel`, `id_mod`, `id_grupo`, `auth`, `timestamp`, `editor`) VALUES
(1, 1, 5, 'open', '2009-08-20 20:39:20', 0),
(2, 2, 5, 'excl', '2010-02-04 15:21:45', 0),
(3, 3, 5, 'open', '2010-02-04 15:22:28', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pure_modulos`
--

DROP TABLE IF EXISTS `pure_modulos`;
CREATE TABLE IF NOT EXISTS `pure_modulos` (
  `id_modulo` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `ajax` enum('S','N') NOT NULL DEFAULT 'S',
  `page` varchar(256) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `editor` int(11) NOT NULL COMMENT 'id do ultimo editor',
  PRIMARY KEY (`id_modulo`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `page` (`page`),
  KEY `ajax` (`ajax`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem a lista de todos os modulos' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pure_modulos`
--

INSERT INTO `pure_modulos` (`id_modulo`, `name`, `ajax`, `page`, `timestamp`, `editor`) VALUES
(1, 'menu', 'S', 'ajax/menu.php', '2010-01-19 17:49:34', 0),
(2, 'register', 'S', 'modules/register.php', '2009-08-21 13:11:25', 0),
(3, 'login', 'S', 'modules/login.php', '2010-01-21 00:54:42', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pure_tables`
--

DROP TABLE IF EXISTS `pure_tables`;
CREATE TABLE IF NOT EXISTS `pure_tables` (
  `id_tabela` int(4) NOT NULL AUTO_INCREMENT,
  `global` varchar(32) NOT NULL,
  `local` varchar(32) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `editor` int(11) NOT NULL COMMENT 'id do ultimo editor',
  PRIMARY KEY (`id_tabela`),
  UNIQUE KEY `global` (`global`,`local`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Contem a lista de todas as tabelas com os nomes universais' AUTO_INCREMENT=13 ;

--
-- Dumping data for table `pure_tables`
--

INSERT INTO `pure_tables` (`id_tabela`, `global`, `local`, `timestamp`, `editor`) VALUES
(1, 'users', 'pure_users', '2010-01-19 15:31:43', 0),
(2, 'logs_violations', 'pure_logs_violations', '2010-01-19 15:31:43', 0),
(3, 'menus', 'pure_menus', '2010-01-19 15:31:43', 0),
(4, 'languages', 'pure_languages', '2010-01-19 15:31:43', 0),
(5, 'menus_titles', 'pure_menus_titles', '2010-01-19 15:31:43', 0),
(6, 'modulos', 'pure_modulos', '2010-01-19 15:31:43', 0),
(7, 'grupos', 'pure_grupos', '2010-01-19 15:32:01', 0),
(8, 'grupos_rel', 'pure_grupos_rel', '2010-01-19 15:31:43', 0),
(9, 'menus_grupos', 'pure_menus_grupos', '2010-01-19 15:31:43', 0),
(10, 'users_grupos', 'pure_users_grupos', '2010-01-19 15:32:01', 0),
(11, 'mods_grupos', 'pure_mods_grupos', '2010-01-19 15:31:43', 0),
(12, 'menus_mod', 'pure_menus_mod', '2010-01-19 15:31:43', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pure_users`
--

DROP TABLE IF EXISTS `pure_users`;
CREATE TABLE IF NOT EXISTS `pure_users` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id do usuario',
  `nick` varchar(256) NOT NULL COMMENT 'nick do usuario',
  `rname` varchar(128) NOT NULL COMMENT 'nome real do usuario',
  `lname` varchar(128) NOT NULL COMMENT 'ultimo nome do usuario',
  `pass` varchar(33) NOT NULL COMMENT 'hash da senha de acesso',
  `email` varchar(256) NOT NULL,
  `birth` date DEFAULT NULL COMMENT 'nascimento',
  `ativo` enum('S','N') NOT NULL DEFAULT 'N',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `editor` int(11) NOT NULL COMMENT 'id do ultimo editor',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `nick` (`nick`),
  KEY `rname` (`rname`),
  KEY `lname` (`lname`),
  KEY `ativo` (`ativo`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Usuarios do sistema' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pure_users`
--

INSERT INTO `pure_users` (`id_user`, `nick`, `rname`, `lname`, `pass`, `email`, `birth`, `ativo`, `timestamp`, `editor`) VALUES
(1, 'root', '', '', '63a9f0ea7bb98050796b649e85481845', '', NULL, 'S', '2010-02-03 10:29:35', 0),
(2, 'adm', '', '', 'b09c600fddc573f117449b3723f23d64', '', NULL, 'S', '2010-02-03 10:30:46', 0),
(3, 'model', '', '', '', '', NULL, 'N', '2009-08-14 22:01:47', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pure_users_grupos`
--

DROP TABLE IF EXISTS `pure_users_grupos`;
CREATE TABLE IF NOT EXISTS `pure_users_grupos` (
  `id_user_grupo` int(13) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) NOT NULL COMMENT 'id do usuario',
  `id_grupo` tinyint(1) unsigned NOT NULL COMMENT 'id do grupo',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'data da ultima modificacao',
  `editor` int(10) NOT NULL COMMENT 'id do ultimo editor',
  PRIMARY KEY (`id_user_grupo`),
  UNIQUE KEY `id_user` (`id_user`,`id_grupo`),
  KEY `timestamp` (`timestamp`),
  KEY `editor` (`editor`),
  KEY `id_grupo` (`id_grupo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `pure_users_grupos`
--

INSERT INTO `pure_users_grupos` (`id_user_grupo`, `id_user`, `id_grupo`, `timestamp`, `editor`) VALUES
(1, 1, 1, '2009-08-11 23:08:00', 0),
(2, 0, 5, '2009-08-20 20:02:26', 0),
(3, 2, 2, '2009-08-18 12:38:59', 0),
(4, 2, 4, '2009-08-20 20:02:46', 0),
(5, 3, 4, '2009-08-20 20:03:06', 0);

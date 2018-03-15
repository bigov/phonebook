-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 15 2018 г., 14:25
-- Версия сервера: 5.5.59-0ubuntu0.14.04.1
-- Версия PHP: 5.5.9-1ubuntu4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Структура таблицы `anons`
--

DROP TABLE IF EXISTS `anons`;
CREATE TABLE IF NOT EXISTS `anons` (
  `anonid` int(8) unsigned NOT NULL COMMENT 'key',
  `anon` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  PRIMARY KEY (`anonid`),
  FULLTEXT KEY `persona` (`anon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='employers names';

-- --------------------------------------------------------

--
-- Структура таблицы `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `jobid` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ключ',
  `unitid` int(8) NOT NULL DEFAULT '0' COMMENT 'unit',
  `kab` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'кабинет',
  `job` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'рабочее место' COMMENT 'должность',
  `phone` varchar(24) COLLATE utf8_unicode_ci NOT NULL DEFAULT '-' COMMENT 'номер телефона',
  `fax` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(4) NOT NULL DEFAULT '99',
  `anonid` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`jobid`),
  FULLTEXT KEY `persona` (`job`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='jobs of employers' AUTO_INCREMENT=2272 ;

-- --------------------------------------------------------

--
-- Структура таблицы `jobs_history`
--

DROP TABLE IF EXISTS `jobs_history`;
CREATE TABLE IF NOT EXISTS `jobs_history` (
  `jhid` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `jobid` int(8) unsigned NOT NULL COMMENT 'jobs id',
  `unitid` int(8) NOT NULL COMMENT 'unit',
  `kab` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'кабинет',
  `job` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'должность',
  `phone` varchar(24) COLLATE utf8_unicode_ci NOT NULL COMMENT 'номер телефона',
  `fax` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int(4) NOT NULL,
  `anonid` int(8) DEFAULT NULL,
  `opid` int(8) unsigned NOT NULL COMMENT 'id of operator',
  `ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'IP address',
  `date` datetime NOT NULL,
  PRIMARY KEY (`jhid`),
  FULLTEXT KEY `persona` (`job`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in jobs' AUTO_INCREMENT=4673 ;

-- --------------------------------------------------------

--
-- Структура таблицы `jobs_mod`
--

DROP TABLE IF EXISTS `jobs_mod`;
CREATE TABLE IF NOT EXISTS `jobs_mod` (
  `jmid` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `jobid` int(8) unsigned NOT NULL COMMENT 'jobs id',
  `new_unitid` int(8) NOT NULL DEFAULT '0' COMMENT 'отдел',
  `new_kab` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'кабинет',
  `new_job` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'рабочее место' COMMENT 'должность',
  `new_phone` varchar(24) COLLATE utf8_unicode_ci NOT NULL DEFAULT '-' COMMENT 'номер телефона',
  `new_fax` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'факс',
  `new_order` int(4) NOT NULL DEFAULT '99' COMMENT 'сортировка',
  `new_anonid` int(8) DEFAULT NULL COMMENT 'дежурные',
  `mod` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int(8) unsigned NOT NULL COMMENT 'id of operator',
  `date` datetime NOT NULL,
  PRIMARY KEY (`jmid`),
  FULLTEXT KEY `persona` (`new_job`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in jobs' AUTO_INCREMENT=402 ;

-- --------------------------------------------------------

--
-- Структура таблицы `names`
--

DROP TABLE IF EXISTS `names`;
CREATE TABLE IF NOT EXISTS `names` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ключ',
  `jobid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT 'должность',
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `phones` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'телефоны',
  `email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `persona` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='employers names' AUTO_INCREMENT=2459 ;

-- --------------------------------------------------------

--
-- Структура таблицы `names_history`
--

DROP TABLE IF EXISTS `names_history`;
CREATE TABLE IF NOT EXISTS `names_history` (
  `nhid` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `id` int(8) unsigned NOT NULL COMMENT 'names id',
  `jobid` int(8) unsigned NOT NULL COMMENT 'должность',
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `phones` varchar(124) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'телефоны',
  `email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `opid` int(8) unsigned NOT NULL,
  `ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'IP address',
  `date` datetime NOT NULL,
  PRIMARY KEY (`nhid`),
  FULLTEXT KEY `persona` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in names' AUTO_INCREMENT=4297 ;

-- --------------------------------------------------------

--
-- Структура таблицы `names_mod`
--

DROP TABLE IF EXISTS `names_mod`;
CREATE TABLE IF NOT EXISTS `names_mod` (
  `nmid` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `id` int(8) unsigned NOT NULL COMMENT 'names id',
  `new_jobid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT 'должность',
  `new_name` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `new_phones` varchar(124) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'телефоны',
  `new_email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'email',
  `mod` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int(8) unsigned NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`nmid`),
  FULLTEXT KEY `persona` (`new_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in names' AUTO_INCREMENT=573 ;

-- --------------------------------------------------------

--
-- Структура таблицы `operators`
--

DROP TABLE IF EXISTS `operators`;
CREATE TABLE IF NOT EXISTS `operators` (
  `opid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mpd` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` int(4) NOT NULL DEFAULT '0',
  `visit` datetime NOT NULL,
  `ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`opid`),
  KEY `mpd` (`mpd`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=68332 ;

-- --------------------------------------------------------

--
-- Структура таблицы `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `unitid` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'key',
  `unit` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '-' COMMENT 'unit name',
  `parent` int(8) NOT NULL DEFAULT '0',
  `order` int(4) NOT NULL DEFAULT '99',
  PRIMARY KEY (`unitid`),
  FULLTEXT KEY `podr` (`unit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Names of units tree' AUTO_INCREMENT=929 ;

-- --------------------------------------------------------

--
-- Структура таблицы `units_history`
--

DROP TABLE IF EXISTS `units_history`;
CREATE TABLE IF NOT EXISTS `units_history` (
  `umid` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `unitid` int(8) unsigned NOT NULL COMMENT 'names id',
  `unit` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unit name',
  `parent` int(8) NOT NULL DEFAULT '0' COMMENT 'parent',
  `order` int(4) NOT NULL DEFAULT '99' COMMENT 'order',
  `opid` int(8) unsigned NOT NULL,
  `ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`umid`),
  FULLTEXT KEY `persona` (`unit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in names' AUTO_INCREMENT=524 ;

-- --------------------------------------------------------

--
-- Структура таблицы `units_mod`
--

DROP TABLE IF EXISTS `units_mod`;
CREATE TABLE IF NOT EXISTS `units_mod` (
  `umid` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `unitid` int(8) unsigned NOT NULL COMMENT 'names id',
  `new_unit` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'unit name',
  `new_parent` int(8) NOT NULL DEFAULT '0' COMMENT 'parent',
  `new_order` int(4) NOT NULL DEFAULT '99' COMMENT 'order',
  `mod` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int(8) unsigned NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`umid`),
  FULLTEXT KEY `persona` (`new_unit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in names' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

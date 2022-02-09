-- phpMyAdmin SQL Dump
-- version 4.2.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 09 2022 г., 11:42
-- Версия сервера: 5.0.83-community-nt
-- Версия PHP: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `phones`
--

-- --------------------------------------------------------

--
-- Структура таблицы `anons`
--

CREATE TABLE IF NOT EXISTS `anons` (
  `anonid` int(8) unsigned NOT NULL COMMENT 'key',
  `anon` varchar(80) collate utf8_unicode_ci NOT NULL COMMENT 'ФИО'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='employers names';

-- --------------------------------------------------------

--
-- Структура таблицы `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `jobid` int(8) unsigned NOT NULL auto_increment COMMENT 'ключ',
  `unitid` int(8) NOT NULL default '0' COMMENT 'unit',
  `kab` varchar(8) collate utf8_unicode_ci default NULL COMMENT 'кабинет',
  `job` varchar(256) collate utf8_unicode_ci NOT NULL default 'рабочее место' COMMENT 'должность',
  `phone` varchar(24) collate utf8_unicode_ci NOT NULL default '-' COMMENT 'номер телефона',
  `fax` varchar(128) collate utf8_unicode_ci default NULL,
  `order` int(4) NOT NULL default '99',
  `anonid` int(8) NOT NULL default '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='jobs of employers' AUTO_INCREMENT=2274 ;

--
-- Дамп данных таблицы `jobs`
--

INSERT INTO `jobs` (`jobid`, `unitid`, `kab`, `job`, `phone`, `fax`, `order`, `anonid`) VALUES
(2272, 930, '', 'ООО МММ', '00-00-00', '', 99, 0),
(2273, 931, '', 'Начальник отдела', '211', '', 99, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `jobs_history`
--

CREATE TABLE IF NOT EXISTS `jobs_history` (
  `jhid` int(8) unsigned NOT NULL auto_increment COMMENT 'primary key',
  `jobid` int(8) unsigned NOT NULL COMMENT 'jobs id',
  `unitid` int(8) NOT NULL COMMENT 'unit',
  `kab` varchar(8) collate utf8_unicode_ci default NULL COMMENT 'кабинет',
  `job` varchar(80) collate utf8_unicode_ci NOT NULL COMMENT 'должность',
  `phone` varchar(24) collate utf8_unicode_ci NOT NULL COMMENT 'номер телефона',
  `fax` varchar(128) collate utf8_unicode_ci default NULL,
  `order` int(4) NOT NULL,
  `anonid` int(8) default NULL,
  `opid` int(8) unsigned NOT NULL COMMENT 'id of operator',
  `ip` varchar(32) collate utf8_unicode_ci NOT NULL COMMENT 'IP address',
  `date` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in jobs' AUTO_INCREMENT=4673 ;

-- --------------------------------------------------------

--
-- Структура таблицы `jobs_mod`
--

CREATE TABLE IF NOT EXISTS `jobs_mod` (
  `jmid` int(8) unsigned NOT NULL auto_increment COMMENT 'primary key',
  `jobid` int(8) unsigned NOT NULL COMMENT 'jobs id',
  `new_unitid` int(8) NOT NULL default '0' COMMENT 'отдел',
  `new_kab` varchar(8) collate utf8_unicode_ci default NULL COMMENT 'кабинет',
  `new_job` varchar(80) collate utf8_unicode_ci NOT NULL default 'рабочее место' COMMENT 'должность',
  `new_phone` varchar(24) collate utf8_unicode_ci NOT NULL default '-' COMMENT 'номер телефона',
  `new_fax` varchar(128) collate utf8_unicode_ci default NULL COMMENT 'факс',
  `new_order` int(4) NOT NULL default '99' COMMENT 'сортировка',
  `new_anonid` int(8) default NULL COMMENT 'дежурные',
  `mod` varchar(36) collate utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int(8) unsigned NOT NULL COMMENT 'id of operator',
  `date` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in jobs' AUTO_INCREMENT=402 ;

-- --------------------------------------------------------

--
-- Структура таблицы `names`
--

CREATE TABLE IF NOT EXISTS `names` (
  `id` int(8) unsigned NOT NULL auto_increment COMMENT 'ключ',
  `jobid` int(8) unsigned NOT NULL default '0' COMMENT 'должность',
  `name` varchar(80) collate utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `phones` varchar(128) collate utf8_unicode_ci default NULL COMMENT 'телефоны',
  `email` varchar(64) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='employers names' AUTO_INCREMENT=2461 ;

--
-- Дамп данных таблицы `names`
--

INSERT INTO `names` (`id`, `jobid`, `name`, `phones`, `email`) VALUES
(2459, 2272, 'Петушков Геннадий Петрович', '22-22-22', ''),
(2460, 2273, 'Иванов Иван Иванович', '22-22-11', '');

-- --------------------------------------------------------

--
-- Структура таблицы `names_history`
--

CREATE TABLE IF NOT EXISTS `names_history` (
  `nhid` int(8) unsigned NOT NULL auto_increment COMMENT 'primary key',
  `id` int(8) unsigned NOT NULL COMMENT 'names id',
  `jobid` int(8) unsigned NOT NULL COMMENT 'должность',
  `name` varchar(80) collate utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `phones` varchar(124) collate utf8_unicode_ci default NULL COMMENT 'телефоны',
  `email` varchar(64) collate utf8_unicode_ci default NULL,
  `opid` int(8) unsigned NOT NULL,
  `ip` varchar(32) collate utf8_unicode_ci NOT NULL COMMENT 'IP address',
  `date` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in names' AUTO_INCREMENT=4299 ;

--
-- Дамп данных таблицы `names_history`
--

INSERT INTO `names_history` (`nhid`, `id`, `jobid`, `name`, `phones`, `email`, `opid`, `ip`, `date`) VALUES
(4297, 2459, 2272, 'Петушков Геннадий Петрович', '22-22-22', '', 0, '127.0.0.1', '2022-02-09 20:15:44'),
(4298, 2460, 2273, 'Иванов Иван Иванович', '22-22-11', '', 0, '127.0.0.1', '2022-02-09 20:33:14');

-- --------------------------------------------------------

--
-- Структура таблицы `names_mod`
--

CREATE TABLE IF NOT EXISTS `names_mod` (
  `nmid` int(8) unsigned NOT NULL auto_increment COMMENT 'primary key',
  `id` int(8) unsigned NOT NULL COMMENT 'names id',
  `new_jobid` int(8) unsigned NOT NULL default '0' COMMENT 'должность',
  `new_name` varchar(80) collate utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `new_phones` varchar(124) collate utf8_unicode_ci default NULL COMMENT 'телефоны',
  `new_email` varchar(64) collate utf8_unicode_ci default NULL COMMENT 'email',
  `mod` varchar(36) collate utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int(8) unsigned NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in names' AUTO_INCREMENT=573 ;

-- --------------------------------------------------------

--
-- Структура таблицы `operators`
--

CREATE TABLE IF NOT EXISTS `operators` (
  `opid` int(8) unsigned NOT NULL auto_increment,
  `mpd` varchar(32) collate utf8_unicode_ci NOT NULL,
  `email` varchar(64) collate utf8_unicode_ci default NULL,
  `level` int(4) NOT NULL default '0',
  `visit` datetime NOT NULL,
  `ip` varchar(32) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=68332 ;

-- --------------------------------------------------------

--
-- Структура таблицы `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `unitid` int(8) unsigned NOT NULL auto_increment COMMENT 'key',
  `unit` varchar(64) collate utf8_unicode_ci NOT NULL default '-' COMMENT 'unit name',
  `parent` int(8) NOT NULL default '0',
  `order` int(4) NOT NULL default '99'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Names of units tree' AUTO_INCREMENT=932 ;

--
-- Дамп данных таблицы `units`
--

INSERT INTO `units` (`unitid`, `unit`, `parent`, `order`) VALUES
(929, 'Внутренние телефоны', 0, 1),
(930, 'Поставщики', 0, 99),
(931, 'Технический отдел', 929, 99);

-- --------------------------------------------------------

--
-- Структура таблицы `units_history`
--

CREATE TABLE IF NOT EXISTS `units_history` (
  `umid` int(8) unsigned NOT NULL auto_increment COMMENT 'primary key',
  `unitid` int(8) unsigned NOT NULL COMMENT 'names id',
  `unit` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'unit name',
  `parent` int(8) NOT NULL default '0' COMMENT 'parent',
  `order` int(4) NOT NULL default '99' COMMENT 'order',
  `opid` int(8) unsigned NOT NULL,
  `ip` varchar(32) collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in names' AUTO_INCREMENT=524 ;

-- --------------------------------------------------------

--
-- Структура таблицы `units_mod`
--

CREATE TABLE IF NOT EXISTS `units_mod` (
  `umid` int(8) unsigned NOT NULL auto_increment COMMENT 'primary key',
  `unitid` int(8) unsigned NOT NULL COMMENT 'names id',
  `new_unit` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'unit name',
  `new_parent` int(8) NOT NULL default '0' COMMENT 'parent',
  `new_order` int(4) NOT NULL default '99' COMMENT 'order',
  `mod` varchar(36) collate utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int(8) unsigned NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='modifications in names' AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anons`
--
ALTER TABLE `anons`
 ADD PRIMARY KEY  (`anonid`), ADD FULLTEXT KEY `persona` (`anon`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
 ADD PRIMARY KEY  (`jobid`), ADD FULLTEXT KEY `persona` (`job`);

--
-- Indexes for table `jobs_history`
--
ALTER TABLE `jobs_history`
 ADD PRIMARY KEY  (`jhid`), ADD FULLTEXT KEY `persona` (`job`);

--
-- Indexes for table `jobs_mod`
--
ALTER TABLE `jobs_mod`
 ADD PRIMARY KEY  (`jmid`), ADD FULLTEXT KEY `persona` (`new_job`);

--
-- Indexes for table `names`
--
ALTER TABLE `names`
 ADD PRIMARY KEY  (`id`), ADD FULLTEXT KEY `persona` (`name`);

--
-- Indexes for table `names_history`
--
ALTER TABLE `names_history`
 ADD PRIMARY KEY  (`nhid`), ADD FULLTEXT KEY `persona` (`name`);

--
-- Indexes for table `names_mod`
--
ALTER TABLE `names_mod`
 ADD PRIMARY KEY  (`nmid`), ADD FULLTEXT KEY `persona` (`new_name`);

--
-- Indexes for table `operators`
--
ALTER TABLE `operators`
 ADD PRIMARY KEY  (`opid`), ADD KEY `mpd` (`mpd`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
 ADD PRIMARY KEY  (`unitid`), ADD FULLTEXT KEY `podr` (`unit`);

--
-- Indexes for table `units_history`
--
ALTER TABLE `units_history`
 ADD PRIMARY KEY  (`umid`), ADD FULLTEXT KEY `persona` (`unit`);

--
-- Indexes for table `units_mod`
--
ALTER TABLE `units_mod`
 ADD PRIMARY KEY  (`umid`), ADD FULLTEXT KEY `persona` (`new_unit`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
AUTO_INCREMENT=2274;
--
-- AUTO_INCREMENT for table `jobs_history`
--
ALTER TABLE `jobs_history`
AUTO_INCREMENT=4673;
--
-- AUTO_INCREMENT for table `jobs_mod`
--
ALTER TABLE `jobs_mod`
AUTO_INCREMENT=402;
--
-- AUTO_INCREMENT for table `names`
--
ALTER TABLE `names`
AUTO_INCREMENT=2461;
--
-- AUTO_INCREMENT for table `names_history`
--
ALTER TABLE `names_history`
AUTO_INCREMENT=4299;
--
-- AUTO_INCREMENT for table `names_mod`
--
ALTER TABLE `names_mod`
AUTO_INCREMENT=573;
--
-- AUTO_INCREMENT for table `operators`
--
ALTER TABLE `operators`
AUTO_INCREMENT=68332;
--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
AUTO_INCREMENT=932;
--
-- AUTO_INCREMENT for table `units_history`
--
ALTER TABLE `units_history`
AUTO_INCREMENT=524;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

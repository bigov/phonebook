-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Фев 12 2022 г., 18:56
-- Версия сервера: 8.0.28
-- Версия PHP: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `phones`
--

-- --------------------------------------------------------

--
-- Структура таблицы `anons`
--

CREATE TABLE `anons` (
  `anonid` int UNSIGNED NOT NULL COMMENT 'key',
  `anon` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='employers names';

-- --------------------------------------------------------

--
-- Структура таблицы `jobs`
--

CREATE TABLE `jobs` (
  `jobid` int UNSIGNED NOT NULL COMMENT 'ключ',
  `unitid` int NOT NULL DEFAULT '0' COMMENT 'unit',
  `kab` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'кабинет',
  `job` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'рабочее место' COMMENT 'должность',
  `phone` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '-' COMMENT 'номер телефона',
  `fax` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '99',
  `anonid` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='jobs of employers';

--
-- Дамп данных таблицы `jobs`
--

INSERT INTO `jobs` (`jobid`, `unitid`, `kab`, `job`, `phone`, `fax`, `order`, `anonid`) VALUES
(2273, 2, '1', 'директор', '211', '', 10, 0),
(2274, 3, '', 'Проходная', '1010', '', 99, 1),
(2275, 3, '1', 'начальник', '1212', '', 99, 0),
(2276, 933, '2', 'начальник', '4444', '', 99, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `jobs_history`
--

CREATE TABLE `jobs_history` (
  `jhid` int UNSIGNED NOT NULL COMMENT 'primary key',
  `jobid` int UNSIGNED NOT NULL COMMENT 'jobs id',
  `unitid` int NOT NULL COMMENT 'unit',
  `kab` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'кабинет',
  `job` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'должность',
  `phone` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'номер телефона',
  `fax` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` int NOT NULL,
  `anonid` int DEFAULT NULL,
  `opid` int UNSIGNED NOT NULL COMMENT 'id of operator',
  `ip` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'IP address',
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='modifications in jobs';

--
-- Дамп данных таблицы `jobs_history`
--

INSERT INTO `jobs_history` (`jhid`, `jobid`, `unitid`, `kab`, `job`, `phone`, `fax`, `order`, `anonid`, `opid`, `ip`, `date`) VALUES
(4673, 2272, 930, '', 'ООО МММ', '00-00-00', '', 99, 0, 0, '127.0.0.1', '2022-02-11 22:05:11'),
(4674, 2272, 930, '', 'ООО ', '00-00-00', '', 99, 0, 0, '127.0.0.1', '2022-02-11 22:06:25'),
(4675, 2272, 930, '', 'ООО Рога и Копыта', '00-00-00', '', 99, 0, 0, '127.0.0.1', '2022-02-12 09:00:43'),
(4676, 2273, 931, '', 'Начальник отдела', '211', '', 99, 0, 0, '127.0.0.1', '2022-02-12 09:09:25');

-- --------------------------------------------------------

--
-- Структура таблицы `jobs_mod`
--

CREATE TABLE `jobs_mod` (
  `jmid` int UNSIGNED NOT NULL COMMENT 'primary key',
  `jobid` int UNSIGNED NOT NULL COMMENT 'jobs id',
  `new_unitid` int NOT NULL DEFAULT '0' COMMENT 'отдел',
  `new_kab` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'кабинет',
  `new_job` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'рабочее место' COMMENT 'должность',
  `new_phone` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '-' COMMENT 'номер телефона',
  `new_fax` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'факс',
  `new_order` int NOT NULL DEFAULT '99' COMMENT 'сортировка',
  `new_anonid` int DEFAULT NULL COMMENT 'дежурные',
  `mod` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int UNSIGNED NOT NULL COMMENT 'id of operator',
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='modifications in jobs';

-- --------------------------------------------------------

--
-- Структура таблицы `names`
--

CREATE TABLE `names` (
  `id` int UNSIGNED NOT NULL COMMENT 'ключ',
  `jobid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'должность',
  `name` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `phones` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'телефоны',
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='employers names';

--
-- Дамп данных таблицы `names`
--

INSERT INTO `names` (`id`, `jobid`, `name`, `phones`, `email`) VALUES
(2461, 2275, 'Кошкин Кирил Константинович', '+7(914)000-00-00', 'kisq@sample.org'),
(2460, 2273, 'Иванов Иван Иванович', '22-22-11', ''),
(2462, 2276, 'Кошкин Семен Петрович', '+7(914)000-00-00', '');

-- --------------------------------------------------------

--
-- Структура таблицы `names_history`
--

CREATE TABLE `names_history` (
  `nhid` int UNSIGNED NOT NULL COMMENT 'primary key',
  `id` int UNSIGNED NOT NULL COMMENT 'names id',
  `jobid` int UNSIGNED NOT NULL COMMENT 'должность',
  `name` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `phones` varchar(124) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'телефоны',
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `opid` int UNSIGNED NOT NULL,
  `ip` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'IP address',
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='modifications in names';

--
-- Дамп данных таблицы `names_history`
--

INSERT INTO `names_history` (`nhid`, `id`, `jobid`, `name`, `phones`, `email`, `opid`, `ip`, `date`) VALUES
(4297, 2459, 2272, 'Петушков Геннадий Петрович', '22-22-22', '', 0, '127.0.0.1', '2022-02-09 20:15:44'),
(4298, 2460, 2273, 'Иванов Иван Иванович', '22-22-11', '', 0, '127.0.0.1', '2022-02-09 20:33:14'),
(4299, 2459, 2272, 'Петушков Геннадий Петрович', '22-22-22', '', 0, '127.0.0.1', '2022-02-12 08:59:58'),
(4300, 2461, 2275, 'Кошкин Кирил Константинович', '+7(914)000-00-00', 'kisq@sample.org', 0, '127.0.0.1', '2022-02-12 09:37:17'),
(4301, 2462, 2276, 'Кошкин Семен Петрович', '+7(914)000-00-00', '', 0, '127.0.0.1', '2022-02-12 17:32:22');

-- --------------------------------------------------------

--
-- Структура таблицы `names_mod`
--

CREATE TABLE `names_mod` (
  `nmid` int UNSIGNED NOT NULL COMMENT 'primary key',
  `id` int UNSIGNED NOT NULL COMMENT 'names id',
  `new_jobid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'должность',
  `new_name` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'ФИО',
  `new_phones` varchar(124) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'телефоны',
  `new_email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'email',
  `mod` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int UNSIGNED NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='modifications in names';

-- --------------------------------------------------------

--
-- Структура таблицы `operators`
--

CREATE TABLE `operators` (
  `opid` int UNSIGNED NOT NULL,
  `mpd` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` int NOT NULL DEFAULT '0',
  `visit` datetime NOT NULL,
  `ip` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `units`
--

CREATE TABLE `units` (
  `unitid` int UNSIGNED NOT NULL COMMENT 'key',
  `unit` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '-' COMMENT 'unit name',
  `parent` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '99'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='Names of units tree';

--
-- Дамп данных таблицы `units`
--

INSERT INTO `units` (`unitid`, `unit`, `parent`, `order`) VALUES
(1, 'Основной список', 0, 0),
(3, 'База хранения', 2, 10),
(2, 'Наша Контора', 1, 10),
(933, 'Отдел закупок', 2, 99),
(934, 'Прочие', 0, 999),
(935, 'Управляющая дирекция', 1, 1),
(936, 'Дочернее предприятие', 1, 30);

-- --------------------------------------------------------

--
-- Структура таблицы `units_history`
--

CREATE TABLE `units_history` (
  `umid` int UNSIGNED NOT NULL COMMENT 'primary key',
  `unitid` int UNSIGNED NOT NULL COMMENT 'names id',
  `unit` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'unit name',
  `parent` int NOT NULL DEFAULT '0' COMMENT 'parent',
  `order` int NOT NULL DEFAULT '99' COMMENT 'order',
  `opid` int UNSIGNED NOT NULL,
  `ip` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='modifications in names';

--
-- Дамп данных таблицы `units_history`
--

INSERT INTO `units_history` (`umid`, `unitid`, `unit`, `parent`, `order`, `opid`, `ip`, `date`) VALUES
(524, 929, 'Внутренние телефоны', 0, 1, 0, '127.0.0.1', '2022-02-09 20:55:35'),
(525, 929, 'Внутренние телефоны', 0, 1, 0, '127.0.0.1', '2022-02-09 20:57:50'),
(526, 930, 'Поставщики', 0, 99, 0, '127.0.0.1', '2022-02-12 09:01:34'),
(527, 929, 'Маша и Медведь', 0, 1, 0, '127.0.0.1', '2022-02-12 09:04:15'),
(528, 931, 'Технический отдел', 929, 99, 0, '127.0.0.1', '2022-02-12 09:06:00'),
(529, 931, 'Наша контора', 929, 1, 0, '127.0.0.1', '2022-02-12 09:07:26'),
(530, 2, 'Наша Контора', 1, 1, 0, '127.0.0.1', '2022-02-12 09:46:01'),
(531, 934, 'прочие', 0, 999, 0, '127.0.0.1', '2022-02-12 17:29:01'),
(532, 933, 'отдел закупок', 2, 99, 0, '127.0.0.1', '2022-02-12 17:34:16');

-- --------------------------------------------------------

--
-- Структура таблицы `units_mod`
--

CREATE TABLE `units_mod` (
  `umid` int UNSIGNED NOT NULL COMMENT 'primary key',
  `unitid` int UNSIGNED NOT NULL COMMENT 'names id',
  `new_unit` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'unit name',
  `new_parent` int NOT NULL DEFAULT '0' COMMENT 'parent',
  `new_order` int NOT NULL DEFAULT '99' COMMENT 'order',
  `mod` varchar(36) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'func',
  `opid` int UNSIGNED NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='modifications in names';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `anons`
--
ALTER TABLE `anons`
  ADD PRIMARY KEY (`anonid`);
ALTER TABLE `anons` ADD FULLTEXT KEY `persona` (`anon`);

--
-- Индексы таблицы `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`jobid`);
ALTER TABLE `jobs` ADD FULLTEXT KEY `persona` (`job`);

--
-- Индексы таблицы `jobs_history`
--
ALTER TABLE `jobs_history`
  ADD PRIMARY KEY (`jhid`);
ALTER TABLE `jobs_history` ADD FULLTEXT KEY `persona` (`job`);

--
-- Индексы таблицы `jobs_mod`
--
ALTER TABLE `jobs_mod`
  ADD PRIMARY KEY (`jmid`);
ALTER TABLE `jobs_mod` ADD FULLTEXT KEY `persona` (`new_job`);

--
-- Индексы таблицы `names`
--
ALTER TABLE `names`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `names` ADD FULLTEXT KEY `persona` (`name`);

--
-- Индексы таблицы `names_history`
--
ALTER TABLE `names_history`
  ADD PRIMARY KEY (`nhid`);
ALTER TABLE `names_history` ADD FULLTEXT KEY `persona` (`name`);

--
-- Индексы таблицы `names_mod`
--
ALTER TABLE `names_mod`
  ADD PRIMARY KEY (`nmid`);
ALTER TABLE `names_mod` ADD FULLTEXT KEY `persona` (`new_name`);

--
-- Индексы таблицы `operators`
--
ALTER TABLE `operators`
  ADD PRIMARY KEY (`opid`),
  ADD KEY `mpd` (`mpd`);

--
-- Индексы таблицы `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unitid`);
ALTER TABLE `units` ADD FULLTEXT KEY `podr` (`unit`);

--
-- Индексы таблицы `units_history`
--
ALTER TABLE `units_history`
  ADD PRIMARY KEY (`umid`);
ALTER TABLE `units_history` ADD FULLTEXT KEY `persona` (`unit`);

--
-- Индексы таблицы `units_mod`
--
ALTER TABLE `units_mod`
  ADD PRIMARY KEY (`umid`);
ALTER TABLE `units_mod` ADD FULLTEXT KEY `persona` (`new_unit`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `anons`
--
ALTER TABLE `anons`
  MODIFY `anonid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'key';

--
-- AUTO_INCREMENT для таблицы `jobs`
--
ALTER TABLE `jobs`
  MODIFY `jobid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ключ', AUTO_INCREMENT=2277;

--
-- AUTO_INCREMENT для таблицы `jobs_history`
--
ALTER TABLE `jobs_history`
  MODIFY `jhid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'primary key', AUTO_INCREMENT=4677;

--
-- AUTO_INCREMENT для таблицы `jobs_mod`
--
ALTER TABLE `jobs_mod`
  MODIFY `jmid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'primary key';

--
-- AUTO_INCREMENT для таблицы `names`
--
ALTER TABLE `names`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ключ', AUTO_INCREMENT=2463;

--
-- AUTO_INCREMENT для таблицы `names_history`
--
ALTER TABLE `names_history`
  MODIFY `nhid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'primary key', AUTO_INCREMENT=4302;

--
-- AUTO_INCREMENT для таблицы `names_mod`
--
ALTER TABLE `names_mod`
  MODIFY `nmid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'primary key';

--
-- AUTO_INCREMENT для таблицы `operators`
--
ALTER TABLE `operators`
  MODIFY `opid` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `units`
--
ALTER TABLE `units`
  MODIFY `unitid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'key', AUTO_INCREMENT=937;

--
-- AUTO_INCREMENT для таблицы `units_history`
--
ALTER TABLE `units_history`
  MODIFY `umid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'primary key', AUTO_INCREMENT=533;

--
-- AUTO_INCREMENT для таблицы `units_mod`
--
ALTER TABLE `units_mod`
  MODIFY `umid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'primary key';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

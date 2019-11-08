-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Сен 04 2019 г., 16:17
-- Версия сервера: 5.7.22-0ubuntu0.17.10.1
-- Версия PHP: 7.1.17-0ubuntu0.17.10.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `signaltv`
--

-- --------------------------------------------------------

--
-- Структура таблицы `paysalary`
--

DROP TABLE IF EXISTS `paysalary`;
CREATE TABLE `paysalary` (
  `id` int(8) UNSIGNED NOT NULL COMMENT 'идентификатор записи',
  `eid` int(6) UNSIGNED NOT NULL COMMENT 'идентификатор сотрудника',
  `sldate` int(6) UNSIGNED NOT NULL COMMENT 'расчетный месяц',
  `payment` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'сумма выплаты',
  `award` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'премия',
  `block` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'блокировка записи'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='основная таблица зарплат';

--
-- Триггеры `paysalary`
--
DROP TRIGGER IF EXISTS `adClearPayout`;
DELIMITER $$
CREATE TRIGGER `adClearPayout` AFTER DELETE ON `paysalary` FOR EACH ROW BEGIN
delete from `payout` where `payout`.`salary_id` = OLD.id;
END
$$
DELIMITER ;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `paysalary`
--
ALTER TABLE `paysalary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idxSLDATE` (`sldate`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `paysalary`
--
ALTER TABLE `paysalary`
  MODIFY `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'идентификатор записи';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

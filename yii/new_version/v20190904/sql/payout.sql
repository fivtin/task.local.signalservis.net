-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Сен 04 2019 г., 16:15
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
-- Структура таблицы `payout`
--

DROP TABLE IF EXISTS `payout`;
CREATE TABLE `payout` (
  `id` int(8) UNSIGNED NOT NULL COMMENT 'идентификатор записи',
  `salary_id` int(8) UNSIGNED NOT NULL COMMENT 'идентификатор записи зарплаты',
  `info` varchar(256) NOT NULL COMMENT 'информация о доплате',
  `payment` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'сумма доплаты',
  `base` varchar(64) NOT NULL DEFAULT 'salary' COMMENT 'основа платежа (salary, award, summa=1000, total)',
  `depends` varchar(64) NOT NULL DEFAULT 'shiftcount' COMMENT 'зависимость платежа (shiftcount, overtime, taskcount, fixed)',
  `type` varchar(64) NOT NULL DEFAULT 'regular' COMMENT 'периодичность (regular, onetime)',
  `completed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'выплата завершена и не может быть изменена'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='таблица дополнительных выплат';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `payout`
--
ALTER TABLE `payout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idxCOMPLETED` (`completed`),
  ADD KEY `idxSALARYID` (`salary_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `payout`
--
ALTER TABLE `payout`
  MODIFY `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'идентификатор записи';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

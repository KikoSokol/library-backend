-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: St 05.Okt 2022, 23:34
-- Verzia serveru: 10.4.17-MariaDB
-- Verzia PHP: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `library`
--
CREATE DATABASE IF NOT EXISTS `library` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_slovak_ci;
USE `library`;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `author`
--

CREATE TABLE `author` (
  `id` bigint(20) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_slovak_ci NOT NULL,
  `surname` varchar(50) COLLATE utf8mb4_slovak_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `book`
--

CREATE TABLE `book` (
  `id` bigint(20) NOT NULL,
  `title` varchar(250) COLLATE utf8mb4_slovak_ci NOT NULL,
  `isbn` varchar(13) COLLATE utf8mb4_slovak_ci NOT NULL,
  `price` double NOT NULL,
  `category` bigint(20) NOT NULL,
  `author` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovak_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `category`
--

CREATE TABLE `category` (
  `id` bigint(20) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_slovak_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovak_ci;

--
-- Sťahujem dáta pre tabuľku `category`
--

INSERT INTO `category` (`id`, `title`) VALUES
(1, 'ACTION'),
(2, 'SCI-FI');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `author_foreign_key` (`author`),
  ADD KEY `category_foreign_key` (`category`);

--
-- Indexy pre tabuľku `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `author`
--
ALTER TABLE `author`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pre tabuľku `book`
--
ALTER TABLE `book`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT pre tabuľku `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `author_foreign_key` FOREIGN KEY (`author`) REFERENCES `author` (`id`),
  ADD CONSTRAINT `category_foreign_key` FOREIGN KEY (`category`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

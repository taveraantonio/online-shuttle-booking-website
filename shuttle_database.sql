-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creato il: Giu 27, 2018 alle 20:24
-- Versione del server: 5.7.22-0ubuntu0.16.04.1
-- Versione PHP: 7.0.30-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shuttle_database`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `booking`
--

DROP TABLE IF EXISTS `booking`;
CREATE TABLE `booking` (
  `user` varchar(250) NOT NULL,
  `source` varchar(250) NOT NULL,
  `destination` varchar(250) NOT NULL,
  `numpeople` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `booking`
--

INSERT INTO `booking` (`user`, `source`, `destination`, `numpeople`) VALUES
('u3@p.it', 'DD', 'EE', 1),
('u4@p.it', 'AL', 'BB', 1),
('u4@p.it', 'BB', 'DD', 1),
('u2@p.it', 'BB', 'DD', 1),
('u2@p.it', 'DD', 'EE', 1),
('u1@p.it', 'FF', 'KK', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `itinerary`
--

DROP TABLE IF EXISTS `itinerary`;
CREATE TABLE `itinerary` (
  `source` varchar(250) NOT NULL,
  `destination` varchar(250) NOT NULL,
  `passengers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `itinerary`
--

INSERT INTO `itinerary` (`source`, `destination`, `passengers`) VALUES
('DD', 'EE', 2),
('AL', 'BB', 1),
('BB', 'DD', 2),
('EE', 'FF', 0),
('FF', 'KK', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `user` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `booked` tinyint(1) NOT NULL,
  `source` varchar(250) NOT NULL,
  `destination` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `members`
--

INSERT INTO `members` (`user`, `password`, `booked`, `source`, `destination`) VALUES
('u1@p.it', 'ec6ef230f1828039ee794566b9c58adc', 1, 'FF', 'KK'),
('u2@p.it', '1d665b9b1467944c128a5575119d1cfd', 1, 'BB', 'EE'),
('u3@p.it', '7bc3ca68769437ce986455407dab2a1f', 1, 'DD', 'EE'),
('u4@p.it', '13207e3d5722030f6c97d69b4904d39d', 1, 'AL', 'DD');

-- --------------------------------------------------------

--
-- Struttura della tabella `routes`
--

DROP TABLE IF EXISTS `routes`;
CREATE TABLE `routes` (
  `route` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `routes`
--

INSERT INTO `routes` (`route`) VALUES
('DD'),
('EE'),
('AL'),
('BB'),
('FF'),
('KK');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

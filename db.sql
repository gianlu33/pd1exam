-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 26, 2019 alle 12:17
-- Versione del server: 10.1.40-MariaDB
-- Versione PHP: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `availability`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `users`;

--
-- Database: `exam`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `availability`
--

CREATE TABLE `availability` (
  `X` int(11) NOT NULL,
  `Y` int(11) NOT NULL,
  `NumBici` int(11) NOT NULL,
  `NumMoto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `availability`
--

INSERT INTO `availability` (`X`, `Y`, `NumBici`, `NumMoto`) VALUES
(10, 20, 2, 3),
(30, 60, 3, 4),
(30, 180, 2, 1),
(40, 134, 1, 0),
(130, 290, 3, 0),
(255, 360, 4, 2),
(440, 241, 0, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `reservations`
--

CREATE TABLE `reservations` (
  `ID` int(11) NOT NULL,
  `Username` varchar(30) NOT NULL,
  `X` int(11) NOT NULL,
  `Y` int(11) NOT NULL,
  `NumBici` int(11) NOT NULL,
  `NumMoto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `reservations`
--

INSERT INTO `reservations` (`ID`, `Username`, `X`, `Y`, `NumBici`, `NumMoto`) VALUES
(1, 'u1@p.it', 40, 134, 2, 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `Username` varchar(30) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`Username`, `Password`) VALUES
('u1@p.it', '$2y$10$D..hhXzODHEGac7zIcJzQua8ZjH/7GWxZNsGVFlo/VdWFrzLXEnY.'),
('u2@p.it', '$2y$10$Z21HJHF4Kid8a5Osk1x9QO9.DTnD39QqZWDNVG8UeOezpNCDkEGhK'),
('u3@p.it', '$2y$10$U/w8mNkPyqRdHyArAZ5bi.bKxSqu..aJKKjz3yMQPTcuunU3Vci82');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`X`,`Y`);

--
-- Indici per le tabelle `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Username`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `reservations`
--
ALTER TABLE `reservations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.4.8-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win64
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportiere Datenbank Struktur für kapazitätsplanungstool
CREATE DATABASE IF NOT EXISTS `kapazitätsplanungstool` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `kapazitätsplanungstool`;

-- Exportiere Struktur von Tabelle kapazitätsplanungstool.kt_aufgaben
CREATE TABLE IF NOT EXISTS `kt_aufgaben` (
  `AufgabenID` int(11) NOT NULL AUTO_INCREMENT,
  `AufgabenTitel` varchar(50) DEFAULT NULL,
  `AufgabenKapazität` int(11) DEFAULT NULL,
  `AufgabenEndtermin` date DEFAULT NULL,
  `AufgabenMitarbeiterID` int(11) DEFAULT NULL,
  PRIMARY KEY (`AufgabenID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Exportiere Struktur von Tabelle kapazitätsplanungstool.kt_mitarbeiter
CREATE TABLE IF NOT EXISTS `kt_mitarbeiter` (
  `MitarbeiterID` int(11) NOT NULL AUTO_INCREMENT,
  `MitarbeiterName` varchar(50) DEFAULT NULL,
  `MitarbeiterArbeitszeit` int(11) DEFAULT NULL,
  `MitarbeiterGrundauslastung` int(11) DEFAULT NULL,
  PRIMARY KEY (`MitarbeiterID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Exportiere Struktur von Tabelle kapazitätsplanungstool.kt_subaufgaben
CREATE TABLE IF NOT EXISTS `kt_subaufgaben` (
  `subaufgabenTitel` varchar(50) DEFAULT NULL,
  `subaufgabenKapazität` int(11) DEFAULT NULL,
  `subaufgabenBearbeitungstermin` date DEFAULT NULL,
  `subaufgabenMitarbeiterID` int(11) DEFAULT NULL,
  `subaufgabenStammaufgabe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
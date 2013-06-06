-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jun 06, 2013 at 06:49 AM
-- Server version: 5.5.21-log
-- PHP Version: 5.3.16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `noodles`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `displayName` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `name`, `displayName`) VALUES
(1, 'basic', 'Basic'),
(2, 'home', 'Home'),
(3, 'output', 'Output');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application` int(8) NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `copy` text NOT NULL,
  `menuTitle` varchar(255) NOT NULL,
  `menuShow` tinyint(1) NOT NULL,
  `permissionID` int(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `application`, `url`, `title`, `copy`, `menuTitle`, `menuShow`, `permissionID`) VALUES
(13, 1, 'pagenotfound', 'Page Not Found', '<h2>404 - Page Not Found</h2>\r\n\r\n<img src="http://i44.tinypic.com/f4kzev.gif" />\r\n\r\n<p>Unfortunatly the page you requested cannot be found, this is entirely your fault.</p>\r\n\r\n<a href="http://electronoodles.co.uk">home</a>', '', 0, 0),
(14, 3, 'output', 'Output', '', 'Output', 0, 0),
(15, 2, 'index', 'Tides', '', '', 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

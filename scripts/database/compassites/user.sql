-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2012 at 02:41 PM
-- Server version: 5.5.22
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `compassites`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `roleId` tinyint(2) unsigned DEFAULT '4',
  `openid` varchar(255) NOT NULL,
  `username` varchar(150) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `activatedDate` datetime NOT NULL,
  `userSource` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1=>REFER_A_FRIEND,2=>NON_APARTMENT_USERS,3=>ERP_SYNCHED_USERS',
  `createdDate` datetime NOT NULL,
  `suffix` varchar(4) NOT NULL DEFAULT '',
  `firstName` varchar(100) NOT NULL DEFAULT '',
  `lastName` varchar(100) NOT NULL DEFAULT '',
  `addressLine1` varchar(150) NOT NULL,
  `addressLine2` varchar(150) NOT NULL,
  `area` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(50) NOT NULL,
  `pincode` int(6) unsigned NOT NULL,
  `country` varchar(100) NOT NULL,
  `landLine` varchar(15) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `creationIP` varchar(25) NOT NULL DEFAULT '',
  `lastLoginIP` varchar(25) NOT NULL DEFAULT '',
  `lastLoginDate` datetime NOT NULL,
  `lastUpdatedDate` datetime NOT NULL,
  `activationKey` varchar(50) NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userId`, `roleId`, `openid`, `username`, `password`, `active`, `activatedDate`, `userSource`, `createdDate`, `suffix`, `firstName`, `lastName`, `addressLine1`, `addressLine2`, `area`, `city`, `state`, `pincode`, `country`, `landLine`, `mobile`, `creationIP`, `lastLoginIP`, `lastLoginDate`, `lastUpdatedDate`, `activationKey`) VALUES
(1, 2, '', 'admin@compassitesinc.com', '14aa1f59a146db014399d9e70ce23ed2', 1, '0000-00-00 00:00:00', 2, '2012-04-16 07:04:00', 'mr', 'Admin', 'AdminLast', 'Address', '', 'Indiranagar', '', '', 560008, '', '809809333', '9876543210', '', '', '2012-04-19 18:31:34', '2012-04-16 19:36:00', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

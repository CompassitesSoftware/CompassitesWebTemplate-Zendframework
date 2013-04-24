-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2012 at 02:39 PM
-- Server version: 5.5.22
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `compassites`
--

-- --------------------------------------------------------
--
-- Table structure for table `navigation`
--

CREATE TABLE IF NOT EXISTS `navigation` (
  `navigationId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(10) unsigned NOT NULL DEFAULT '0',
  `section` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`navigationId`),
  KEY `idx_navigation_section` (`section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `navigation`
--

INSERT INTO `navigation` (`navigationId`, `parentId`, `section`, `module`, `controller`, `action`, `name`, `order`) VALUES
(7, 0, 'main', 'admin', 'index', '', 'Admin', 0),
(6, 0, 'main', 'user', 'index', 'login', 'Login', 0),
(5, 0, 'main', 'default', 'index', 'aboutus', 'About Us', 0),
(4, 0, 'main', 'default', 'index', 'index', 'Home', 0),
(3, 0, 'admin', 'admin', 'navigation', 'index', 'NavigationOne', 0),
(1, 0, 'admin', 'admin', 'user', 'index', 'User', 0),
(2, 0, 'admin', 'admin', 'index', 'acl', 'ACL', 0);



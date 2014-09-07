-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2014 at 04:49 PM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bliss`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryId` int(10) unsigned DEFAULT NULL,
  `resourceName` varchar(64) NOT NULL,
  `resourceId` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `rawText` text,
  `formattedText` text,
  `created` int(10) unsigned DEFAULT NULL,
  `updated` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`userId`) USING BTREE,
  KEY `updated` (`updated`) USING BTREE,
  KEY `category_idx` (`categoryId`),
  KEY `resourceName` (`resourceName`),
  KEY `resourceId` (`resourceId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `article_categories`
--

CREATE TABLE IF NOT EXISTS `article_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resourceName` varchar(64) NOT NULL,
  `resourceId` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `calendar_events`
--

CREATE TABLE IF NOT EXISTS `calendar_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `resourceName` varchar(64) NOT NULL,
  `resourceId` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `startTime` int(10) unsigned NOT NULL,
  `endTime` int(10) unsigned NOT NULL,
  `isAllDay` tinyint(1) NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `isActive` (`isActive`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lists`
--

CREATE TABLE IF NOT EXISTS `lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resourceName` varchar(64) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `order` int(11) NOT NULL DEFAULT '99',
  PRIMARY KEY (`id`),
  KEY `resource` (`resourceName`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_targets`
--

CREATE TABLE IF NOT EXISTS `list_targets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listId` int(10) unsigned NOT NULL,
  `resourceId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `listId` (`listId`),
  KEY `resourceId` (`resourceId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `system_params`
--

CREATE TABLE IF NOT EXISTS `system_params` (
  `resourceName` varchar(64) NOT NULL,
  `resourceId` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `isPrivate` tinyint(1) DEFAULT '0',
  UNIQUE KEY `resourceName` (`resourceName`,`resourceId`,`name`),
  KEY `is_private` (`isPrivate`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `system_sessions`
--

CREATE TABLE IF NOT EXISTS `system_sessions` (
  `session_id` varchar(32) NOT NULL,
  `data` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `timestamp` (`timestamp`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL DEFAULT 'guest',
  `email` varchar(100) NOT NULL,
  `password` varchar(128) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `firstName` varchar(64) DEFAULT NULL,
  `lastName` varchar(64) DEFAULT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING BTREE,
  KEY `role` (`role`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `email`, `password`, `username`, `nickname`, `firstName`, `lastName`, `created`, `updated`, `isActive`) VALUES
(1, 'admin', 'admin@test.com', '$2y$11$ZrYWzfqSIXD8ryX3a.sriuid665vFs9hppf8ZeDMcycmRCF4htUWq', 'admin', 'Admin', 'Admin', NULL, 1391377041, 1391377041, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` varchar(32) NOT NULL,
  `userId` int(11) NOT NULL,
  `created` int(10) unsigned DEFAULT NULL,
  `isValid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`userId`),
  KEY `timestamp` (`created`) USING BTREE,
  KEY `isValid` (`isValid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `userId`, `created`, `isValid`) VALUES
('d379e5fc5e1ee4aac337b68460249e22', 1, 1394034540, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

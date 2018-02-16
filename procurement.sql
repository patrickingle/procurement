-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2018 at 01:02 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `procurement`
--
CREATE DATABASE IF NOT EXISTS `procurement` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `procurement`;

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE IF NOT EXISTS `bids` (
  `seq` tinyint(4) NOT NULL AUTO_INCREMENT,
  `solnum` varchar(13) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL,
  `amount` decimal(10,0) NOT NULL DEFAULT '0',
  `comments` longtext NOT NULL,
  `bidtime` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `echeck`
--

CREATE TABLE IF NOT EXISTS `echeck` (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(30) NOT NULL,
  `rtnaba` varchar(9) NOT NULL,
  `acctno` varchar(20) NOT NULL,
  `bankinfo` longtext NOT NULL,
  `holderinfo` longtext NOT NULL,
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `seq` tinyint(4) NOT NULL AUTO_INCREMENT,
  `solnum` varchar(13) NOT NULL,
  `question` longtext NOT NULL,
  `answer` longtext NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `solicitations`
--

CREATE TABLE IF NOT EXISTS `solicitations` (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(13) NOT NULL,
  `title` varchar(80) NOT NULL,
  `duedate` date NOT NULL DEFAULT '0000-00-00',
  `budget` decimal(15,2) NOT NULL DEFAULT '0.00',
  `synopsis` longtext NOT NULL,
  `description` longtext NOT NULL,
  `filename` varchar(30) NOT NULL,
  `onlinebid` char(1) NOT NULL,
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE IF NOT EXISTS `vendor` (
  `seq` tinyint(4) NOT NULL AUTO_INCREMENT,
  `appdate` date NOT NULL DEFAULT '0000-00-00',
  `signature` varchar(30) NOT NULL,
  `visa_accepted` char(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `address` varchar(60) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` char(2) NOT NULL,
  `zipcode` varchar(9) NOT NULL,
  `contact` varchar(30) NOT NULL,
  `title` varchar(20) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `fax` varchar(10) NOT NULL,
  `fein` varchar(9) NOT NULL,
  `email` varchar(50) NOT NULL,
  `website` varchar(60) NOT NULL,
  `payment_mail` char(1) NOT NULL,
  `mailing_address` varchar(50) NOT NULL,
  `mailing_city` varchar(30) NOT NULL,
  `mailing_state` char(2) NOT NULL,
  `mailing_zipcode` varchar(9) NOT NULL,
  `payment_terms` varchar(20) NOT NULL,
  `shipping_terms` varchar(20) NOT NULL,
  `business_type` varchar(30) NOT NULL,
  `business_structure` varchar(30) NOT NULL,
  `specialty` longtext NOT NULL,
  `small_business` char(1) NOT NULL,
  `minority_business` char(1) NOT NULL,
  `minority_type` varchar(30) NOT NULL,
  `certified_business` char(1) NOT NULL,
  `certification_authority` varchar(30) NOT NULL,
  `fee` decimal(6,2) NOT NULL DEFAULT '0.00',
  `solemail` char(1) NOT NULL DEFAULT 'N',
  `validated` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

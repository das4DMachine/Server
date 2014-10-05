-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: custsql-pow08
-- Generation Time: Mar 11, 2014 at 11:19 AM
-- Server version: 5.5.32
-- PHP Version: 4.4.9
-- 
-- Database: `phpgang_demo`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `restAPI`
-- 

CREATE TABLE `restAPI` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(240) NOT NULL,
  `email` varchar(240) NOT NULL,
  `password` varchar(240) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `restAPI`
-- 

INSERT INTO `restAPI` VALUES (2, 'AAAAAA', 'aa', '', '2014-03-11 09:54:21', '54.195.50.137');
INSERT INTO `restAPI` VALUES (4, 'Huzoor Bux', 'huzoorbux@gmail.com', '', '2014-03-11 10:56:15', '54.198.220.70');

-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 04, 2011 at 05:07 AM
-- Server version: 5.1.44
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jayonexpress_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) NOT NULL,
  `merchant_id` bigint(20) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `application_name` varchar(255) NOT NULL,
  `key` varchar(128) NOT NULL,
  `callback_url` varchar(255) NOT NULL,
  `application_description` varchar(255) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `signature` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `applications`
--


-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(255) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('533621fd90f1fdff23e332725c443af6', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2', 1322820928, 'a:7:{s:9:"user_data";s:0:"";s:8:"username";s:9:"superuser";s:5:"email";s:23:"andy.awidarto@gmail.com";s:8:"group_id";s:1:"1";s:5:"token";s:0:"";s:10:"identifier";s:0:"";s:9:"logged_in";b:1;}'),
('05954bbe8292344d2499ffe01d6c51fb', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2', 1322848571, '');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_log`
--

CREATE TABLE IF NOT EXISTS `delivery_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `timestamp` varchar(40) NOT NULL,
  `delivery_id` varchar(40) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `status` varchar(40) NOT NULL,
  `notes` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `delivery_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `delivery_order_assigned`
--

CREATE TABLE IF NOT EXISTS `delivery_order_assigned` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `delivery_id` varchar(40) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `buyer_id` bigint(20) NOT NULL,
  `merchant_id` bigint(20) NOT NULL,
  `merchant_trans_id` varchar(128) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `phone` varchar(40) NOT NULL,
  `status` varchar(40) NOT NULL,
  `reschedule_ref` varchar(40) NOT NULL,
  `revoke_ref` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `delivery_order_assigned`
--


-- --------------------------------------------------------

--
-- Table structure for table `delivery_order_delivered`
--

CREATE TABLE IF NOT EXISTS `delivery_order_delivered` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `delivery_id` varchar(40) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `buyer_id` bigint(20) NOT NULL,
  `merchant_id` bigint(20) NOT NULL,
  `merchant_trans_id` varchar(128) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `phone` varchar(40) NOT NULL,
  `status` varchar(40) NOT NULL,
  `reschedule_ref` varchar(40) NOT NULL,
  `revoke_ref` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `delivery_order_delivered`
--


-- --------------------------------------------------------

--
-- Table structure for table `delivery_order_details`
--

CREATE TABLE IF NOT EXISTS `delivery_order_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `delivery_id` varchar(40) NOT NULL,
  `sequence` mediumint(9) NOT NULL,
  `item_description` varchar(254) NOT NULL,
  `unit_price` double NOT NULL,
  `order_quantity` int(11) NOT NULL,
  `unit_total` double NOT NULL,
  `discount` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `delivery_order_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `delivery_order_incoming`
--

CREATE TABLE IF NOT EXISTS `delivery_order_incoming` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `delivery_id` varchar(40) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `buyer_id` bigint(20) NOT NULL,
  `merchant_id` bigint(20) NOT NULL,
  `merchant_trans_id` varchar(128) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `phone` varchar(40) NOT NULL,
  `status` varchar(40) NOT NULL,
  `reschedule_ref` varchar(40) NOT NULL,
  `revoke_ref` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `delivery_order_incoming`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL,
  `title` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `title`, `description`) VALUES
(1, 'administrator', 'Super Administrator'),
(2, 'official', 'Jayon Official Employee'),
(3, 'courier', 'Jayon Courier'),
(4, 'merchant', 'Merchant Group'),
(5, 'buyer', 'Buyer Only Users');

-- --------------------------------------------------------

--
-- Table structure for table `location_log`
--

CREATE TABLE IF NOT EXISTS `location_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `timestamp` varchar(40) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `status` varchar(40) NOT NULL,
  `notes` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `location_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '100',
  `token` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `username`, `email`, `password`, `group_id`, `token`, `identifier`) VALUES
(1, 'superuser', 'andy.awidarto@gmail.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 1, '', ''),
(2, 'administrator', 'andy.awidarto@kickstartlab.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 1, '', ''),
(15, 'userone', 'kurier@jayon.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 3, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '100',
  `token` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `group_id`, `token`, `identifier`) VALUES
(1, 'superuser', 'andy.awidarto@gmail.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 1, '', ''),
(2, 'administrator', 'andy.awidarto@kickstartlab.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 1, '', ''),
(15, 'userone', 'kurier@jayon.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 3, '', '');

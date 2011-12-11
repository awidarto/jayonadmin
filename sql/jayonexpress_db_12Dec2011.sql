-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 11, 2011 at 05:28 PM
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
  `created` datetime NOT NULL,
  `owner_id` bigint(20) NOT NULL,
  `merchant_id` bigint(20) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `application_name` varchar(255) NOT NULL,
  `key` varchar(128) NOT NULL,
  `callback_url` varchar(255) NOT NULL,
  `trx_count` bigint(20) NOT NULL,
  `application_description` varchar(255) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `signature` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `created`, `owner_id`, `merchant_id`, `domain`, `application_name`, `key`, `callback_url`, `trx_count`, `application_description`, `logo_url`, `signature`) VALUES
(1, '0000-00-00 00:00:00', 0, 17, 'http://localhost/myshop/', 'Baru Mencoba', '23c33397a9b1ecb579c53fe200e26c12709ee379', 'http://localhost/myshop/jayon/', 0, 'Test Ajah', 'http://localhost/myshop/images/logo.png', 'Om Senang Pernah Disini'),
(2, '0000-00-00 00:00:00', 0, 17, 'http://localhost/myshop2/', 'Aplikasiku Yang Lain Lagi', '3fbd594b698c25c0572e46d2fc2106c3033895c8', 'http://localhost/myshop2/jayon/get/', 0, 'ada ajah', 'http://localhost/myshop2/images/logo.png', 'tanda tanganku');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_log`
--

CREATE TABLE IF NOT EXISTS `assignment_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `delivery_id` varchar(128) NOT NULL,
  `device_id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `assignment_log`
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
('ccf3ec0f70fda129493242894b06eb66', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2', 1323623533, 'a:9:{s:9:"user_data";s:0:"";s:8:"username";s:9:"superuser";s:5:"email";s:23:"andy.awidarto@gmail.com";s:8:"fullname";s:13:"Andy Awidarto";s:6:"mobile";s:11:"08123456789";s:8:"group_id";s:1:"1";s:5:"token";s:0:"";s:10:"identifier";s:0:"";s:9:"logged_in";b:1;}');

-- --------------------------------------------------------

--
-- Table structure for table `couriers`
--

CREATE TABLE IF NOT EXISTS `couriers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `photo` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '100',
  `token` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `couriers`
--

INSERT INTO `couriers` (`id`, `username`, `email`, `fullname`, `address`, `phone`, `mobile`, `photo`, `password`, `group_id`, `token`, `identifier`) VALUES
(16, 'memberbarulagi', 'membernewlg@merchantsite.com', 'member rangkap kurir', 'dimana mana', '0809798789', '7986878757', '', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 3, '', ''),
(17, 'omsenang', 'om@senang.com', 'senang bekerja', 'pengkolan ojek pasar gemblong', '353446456', '46456456456', '', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 3, '', '');

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
  `assigntime` datetime NOT NULL,
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
  `device_id` int(11) NOT NULL,
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
  `ordertime` datetime NOT NULL,
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
  `ordertime` datetime NOT NULL,
  `delivery_id` varchar(40) NOT NULL,
  `unit_sequence` mediumint(9) NOT NULL,
  `unit_description` varchar(254) NOT NULL,
  `unit_price` double NOT NULL,
  `unit_quantity` int(11) NOT NULL,
  `unit_total` double NOT NULL,
  `unit_discount` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `delivery_order_details`
--

INSERT INTO `delivery_order_details` (`id`, `ordertime`, `delivery_id`, `unit_sequence`, `unit_description`, `unit_price`, `unit_quantity`, `unit_total`, `unit_discount`) VALUES
(1, '2011-12-10 06:13:06', '00000017-10-122011-0000000004', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(2, '2011-12-10 06:13:06', '00000017-10-122011-0000000004', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(3, '2011-12-10 06:13:06', '00000017-10-122011-0000000004', 2, 'kaos kutung', 15000, 10, 150000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_order_incoming`
--

CREATE TABLE IF NOT EXISTS `delivery_order_incoming` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ordertime` datetime NOT NULL,
  `delivery_id` varchar(40) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `application_key` varchar(128) NOT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `delivery_order_incoming`
--

INSERT INTO `delivery_order_incoming` (`id`, `ordertime`, `delivery_id`, `application_id`, `application_key`, `buyer_id`, `merchant_id`, `merchant_trans_id`, `courier_id`, `shipping_address`, `phone`, `status`, `reschedule_ref`, `revoke_ref`) VALUES
(1, '0000-00-00 00:00:00', '00000017-10-122011-0000000001', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(2, '0000-00-00 00:00:00', '00000017-10-122011-0000000002', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(3, '0000-00-00 00:00:00', '00000017-10-122011-0000000003', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(4, '2011-12-10 06:13:06', '00000017-10-122011-0000000004', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE IF NOT EXISTS `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(50) NOT NULL,
  `descriptor` varchar(255) NOT NULL,
  `devname` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `identifier`, `descriptor`, `devname`, `password`, `mobile`) VALUES
(16, 'JY-001', 'Samsung Galaxy Y murmer', 'Jayon 001', '0809798789', '7986878757'),
(18, 'JY-002', 'HTC Wildfire XE', 'Jayon 002', '', '0856453534242');

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
(1, 'admin', 'Super Administrator'),
(2, 'official', 'Jayon Official Employee'),
(3, 'courier', 'Jayon Courier'),
(4, 'merchant', 'Merchant'),
(5, 'buyer', 'Buyer');

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
  `merchantname` varchar(128) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `city` varchar(128) NOT NULL,
  `country` varchar(128) NOT NULL,
  `zip` varchar(25) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '100',
  `token` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `username`, `email`, `password`, `merchantname`, `fullname`, `street`, `district`, `city`, `country`, `zip`, `phone`, `mobile`, `group_id`, `token`, `identifier`) VALUES
(16, 'memberbarulagi', 'membernewlg@merchantsite.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', '', 'Member Paling Baru Deh Ih', 'Jalan H', 'Rawa Belong', 'Jakarta Barat', 'United States', '11640', '21 5841281', '21 5841281', 4, '', ''),
(17, 'omsenang', 'om@senang.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 'PT Ogah Rugi', 'Oom Senang', 'Salah Jalan', 'Kecamatan Ndiwek', 'Sidoarjo', 'Indonesia', '90210', '024109797', '0830303030', 4, '', ''),
(18, 'tegetop', 'tege@mail.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 'Salsa Cafe', 'Teges Prita Baraya', 'Kemang', 'Kebayoran Baru', 'Jakarta Selatan', 'Indonesia', '11808080', '089786757', '78675674', 5, '', ''),
(19, 'zztopzz', 'zz@topzz.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', '', 'ZZ', 'Route 66', 'Orange County', 'Phoenix', 'USA', '9797987', '23435345', '345345345', 4, '', ''),
(20, 'gantibajucom', 'gantibaju@dot.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', '', 'Admin GantiBaju', 'Senayan Pintu Satu', 'Senayan', 'Jakarta Selatan', 'Indonesia', '119797', '09089786', '907878676', 4, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sequences`
--

CREATE TABLE IF NOT EXISTS `sequences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` year(4) NOT NULL,
  `sequence` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sequences`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '100',
  `token` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `fullname`, `mobile`, `group_id`, `token`, `identifier`) VALUES
(1, 'superuser', 'andy.awidarto@gmail.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 'Andy Awidarto', '08123456789', 1, '', ''),
(2, 'administrator', 'andy.awidarto@kickstartlab.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 'Joni', 'Iskandar', 1, '', '');

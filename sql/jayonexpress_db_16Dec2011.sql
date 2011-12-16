-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 16, 2011 at 06:24 PM
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
  `fetch_detail_url` varchar(255) NOT NULL,
  `fetch_method` varchar(5) NOT NULL,
  `trx_count` bigint(20) NOT NULL,
  `application_description` varchar(255) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `signature` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `created`, `owner_id`, `merchant_id`, `domain`, `application_name`, `key`, `callback_url`, `fetch_detail_url`, `fetch_method`, `trx_count`, `application_description`, `logo_url`, `signature`) VALUES
(7, '0000-00-00 00:00:00', 0, 21, 'http://localhost/myshop/gantibaju', 'Ganti Baju Main Site', '2bf7ec5f0f3f1b5664f34e58bc7ea33cf6eb9860', 'http://localhost/myshop/gantibaju/jayon/', 'http://localhost/myshop/gantibaju/jayonfetch/', 'URL', 0, 'Ganti Baju Main Site', 'http://localhost/myshop/images/gantibaju.png', 'Ganti Baju Online Shop');

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
('6e773470d5654a7b0b87f2c5aad083d3', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.63 Safari/535.7', 1324054906, 'a:9:{s:9:"user_data";s:0:"";s:8:"username";s:13:"administrator";s:5:"email";s:30:"andy.awidarto@kickstartlab.com";s:8:"fullname";s:4:"Joni";s:6:"mobile";s:8:"Iskandar";s:8:"group_id";s:1:"1";s:5:"token";s:0:"";s:10:"identifier";s:0:"";s:9:"logged_in";b:1;}'),
('977d92a6a8387cc7d059b756e3ec96de', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.63 Safari/535.7', 1324058397, 'a:21:{s:9:"user_data";s:0:"";s:8:"username";s:9:"gantibaju";s:5:"email";s:20:"admin@ganti.baju.com";s:12:"merchantname";s:10:"Ganti Baju";s:8:"fullname";s:16:"Admin Ganti Baju";s:6:"street";s:6:"Kemang";s:8:"district";s:6:"Kemang";s:8:"province";s:0:"";s:4:"city";s:15:"Jakarta Selatan";s:7:"country";s:9:"Indonesia";s:3:"zip";s:5:"90210";s:5:"phone";s:10:"0219768675";s:6:"mobile";s:13:"0811123456789";s:4:"bank";s:3:"BCA";s:14:"account_number";s:12:"097867756646";s:12:"account_name";s:21:"Yang Punya Ganti Baju";s:8:"group_id";s:1:"4";s:5:"token";s:0:"";s:10:"identifier";s:0:"";s:6:"userid";s:2:"21";s:9:"logged_in";b:1;}');

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
  `device_id` int(11) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `latitude` decimal(18,12) NOT NULL,
  `longitude` decimal(18,12) NOT NULL,
  `status` varchar(40) NOT NULL,
  `notes` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `delivery_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `delivery_order_active`
--

CREATE TABLE IF NOT EXISTS `delivery_order_active` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ordertime` datetime NOT NULL,
  `assigntime` datetime NOT NULL,
  `deliverytime` datetime NOT NULL,
  `assignment_date` date NOT NULL,
  `delivery_id` varchar(40) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `application_key` varchar(128) NOT NULL,
  `buyer_id` bigint(20) NOT NULL,
  `merchant_id` bigint(20) NOT NULL,
  `merchant_trans_id` varchar(128) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `device_id` bigint(11) NOT NULL,
  `shipping_address` text NOT NULL,
  `phone` varchar(40) NOT NULL,
  `status` varchar(40) NOT NULL,
  `delivery_note` text NOT NULL,
  `undersign` varchar(255) NOT NULL,
  `latitude` decimal(18,12) NOT NULL,
  `longitude` decimal(18,12) NOT NULL,
  `reschedule_ref` varchar(40) NOT NULL,
  `revoke_ref` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `delivery_order_active`
--

INSERT INTO `delivery_order_active` (`id`, `ordertime`, `assigntime`, `deliverytime`, `assignment_date`, `delivery_id`, `application_id`, `application_key`, `buyer_id`, `merchant_id`, `merchant_trans_id`, `courier_id`, `device_id`, `shipping_address`, `phone`, `status`, `delivery_note`, `undersign`, `latitude`, `longitude`, `reschedule_ref`, `revoke_ref`) VALUES
(1, '0000-00-00 00:00:00', '2011-12-12 10:02:41', '0000-00-00 00:00:00', '2011-12-14', '00000017-10-122011-0000000001', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 16, 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', '', 0.000000000000, 0.000000000000, '', ''),
(2, '0000-00-00 00:00:00', '2011-12-12 10:34:08', '0000-00-00 00:00:00', '2011-12-24', '00000017-10-122011-0000000003', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 16, 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', '', 0.000000000000, 0.000000000000, '', ''),
(3, '0000-00-00 00:00:00', '2011-12-12 10:47:42', '0000-00-00 00:00:00', '2011-12-17', '00000017-10-122011-0000000002', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 16, 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', '', 0.000000000000, 0.000000000000, '', ''),
(4, '2011-12-12 11:49:37', '2011-12-12 11:49:51', '0000-00-00 00:00:00', '2011-12-24', '00000017-12-122011-0000000005', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 18, 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', '', 0.000000000000, 0.000000000000, '', ''),
(5, '2011-12-13 08:05:06', '2011-12-13 08:05:34', '0000-00-00 00:00:00', '2011-12-17', '00000017-13-122011-0000000007', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '278947128941', 0, 16, 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', '', 0.000000000000, 0.000000000000, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_order_archive`
--

CREATE TABLE IF NOT EXISTS `delivery_order_archive` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ordertime` datetime NOT NULL,
  `assigntime` datetime NOT NULL,
  `deliverytime` datetime NOT NULL,
  `delivery_id` varchar(40) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `application_key` varchar(128) NOT NULL,
  `buyer_id` bigint(20) NOT NULL,
  `merchant_id` bigint(20) NOT NULL,
  `merchant_trans_id` varchar(128) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `device_id` bigint(11) NOT NULL,
  `shipping_address` text NOT NULL,
  `phone` varchar(40) NOT NULL,
  `status` varchar(40) NOT NULL,
  `delivery_note` text NOT NULL,
  `undersign` varchar(255) NOT NULL,
  `latitude` decimal(18,12) NOT NULL,
  `longitude` decimal(18,12) NOT NULL,
  `reschedule_ref` varchar(40) NOT NULL,
  `revoke_ref` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `delivery_order_archive`
--

INSERT INTO `delivery_order_archive` (`id`, `ordertime`, `assigntime`, `deliverytime`, `delivery_id`, `application_id`, `application_key`, `buyer_id`, `merchant_id`, `merchant_trans_id`, `courier_id`, `device_id`, `shipping_address`, `phone`, `status`, `delivery_note`, `undersign`, `latitude`, `longitude`, `reschedule_ref`, `revoke_ref`) VALUES
(1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '00000017-10-122011-0000000001', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 0, 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', '', 0.000000000000, 0.000000000000, '', ''),
(2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '00000017-10-122011-0000000002', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 0, 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', '', 0.000000000000, 0.000000000000, '', ''),
(3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '00000017-10-122011-0000000003', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 0, 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', '', 0.000000000000, 0.000000000000, '', ''),
(4, '2011-12-10 06:13:06', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '00000017-10-122011-0000000004', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 0, 0, 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', '', 0.000000000000, 0.000000000000, '', '');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=124 ;

--
-- Dumping data for table `delivery_order_details`
--

INSERT INTO `delivery_order_details` (`id`, `ordertime`, `delivery_id`, `unit_sequence`, `unit_description`, `unit_price`, `unit_quantity`, `unit_total`, `unit_discount`) VALUES
(1, '2011-12-10 06:13:06', '00000017-10-122011-0000000004', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(2, '2011-12-10 06:13:06', '00000017-10-122011-0000000004', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(3, '2011-12-10 06:13:06', '00000017-10-122011-0000000004', 2, 'kaos kutung', 15000, 10, 150000, 0),
(4, '2011-12-12 11:49:37', '00000017-12-122011-0000000005', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(5, '2011-12-12 11:49:37', '00000017-12-122011-0000000005', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(6, '2011-12-12 11:49:37', '00000017-12-122011-0000000005', 2, 'kaos kutung', 15000, 10, 150000, 0),
(7, '2011-12-13 08:00:46', '00000017-13-122011-0000000006', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(8, '2011-12-13 08:00:46', '00000017-13-122011-0000000006', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(9, '2011-12-13 08:00:46', '00000017-13-122011-0000000006', 2, 'kaos kutung', 15000, 10, 150000, 0),
(10, '2011-12-13 08:05:06', '00000017-13-122011-0000000007', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(11, '2011-12-13 08:05:06', '00000017-13-122011-0000000007', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(12, '2011-12-13 08:05:06', '00000017-13-122011-0000000007', 2, 'kaos kutung', 15000, 10, 150000, 0),
(13, '2011-12-16 11:05:20', '00000021-16-122011-0000000008', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(14, '2011-12-16 11:05:20', '00000021-16-122011-0000000008', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(15, '2011-12-16 11:05:20', '00000021-16-122011-0000000008', 2, 'kaos kutung', 15000, 10, 150000, 0),
(16, '2011-12-16 11:06:50', '00000021-16-122011-0000000009', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(17, '2011-12-16 11:06:50', '00000021-16-122011-0000000009', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(18, '2011-12-16 11:06:50', '00000021-16-122011-0000000009', 2, 'kaos kutung', 15000, 10, 150000, 0),
(19, '2011-12-16 11:18:14', '00000021-16-122011-0000000010', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(20, '2011-12-16 11:18:14', '00000021-16-122011-0000000010', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(21, '2011-12-16 11:18:14', '00000021-16-122011-0000000010', 2, 'kaos kutung', 15000, 10, 150000, 0),
(22, '2011-12-16 11:18:34', '00000021-16-122011-0000000011', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(23, '2011-12-16 11:18:34', '00000021-16-122011-0000000011', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(24, '2011-12-16 11:18:34', '00000021-16-122011-0000000011', 2, 'kaos kutung', 15000, 10, 150000, 0),
(25, '2011-12-16 11:19:34', '00000021-16-122011-0000000012', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(26, '2011-12-16 11:19:34', '00000021-16-122011-0000000012', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(27, '2011-12-16 11:19:34', '00000021-16-122011-0000000012', 2, 'kaos kutung', 15000, 10, 150000, 0),
(28, '2011-12-16 11:32:55', '00000021-16-122011-0000000013', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(29, '2011-12-16 11:32:55', '00000021-16-122011-0000000013', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(30, '2011-12-16 11:32:55', '00000021-16-122011-0000000013', 2, 'kaos kutung', 15000, 10, 150000, 0),
(31, '2011-12-16 11:33:16', '00000021-16-122011-0000000014', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(32, '2011-12-16 11:33:16', '00000021-16-122011-0000000014', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(33, '2011-12-16 11:33:16', '00000021-16-122011-0000000014', 2, 'kaos kutung', 15000, 10, 150000, 0),
(34, '2011-12-16 11:44:45', '00000021-16-122011-0000000015', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(35, '2011-12-16 11:44:45', '00000021-16-122011-0000000015', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(36, '2011-12-16 11:44:45', '00000021-16-122011-0000000015', 2, 'kaos kutung', 15000, 10, 150000, 0),
(37, '2011-12-16 11:45:54', '00000021-16-122011-0000000016', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(38, '2011-12-16 11:45:54', '00000021-16-122011-0000000016', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(39, '2011-12-16 11:45:54', '00000021-16-122011-0000000016', 2, 'kaos kutung', 15000, 10, 150000, 0),
(40, '2011-12-16 11:55:42', '00000021-16-122011-0000000017', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(41, '2011-12-16 11:55:42', '00000021-16-122011-0000000017', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(42, '2011-12-16 11:55:42', '00000021-16-122011-0000000017', 2, 'kaos kutung', 15000, 10, 150000, 0),
(43, '2011-12-16 11:56:32', '00000021-16-122011-0000000018', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(44, '2011-12-16 11:56:32', '00000021-16-122011-0000000018', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(45, '2011-12-16 11:56:32', '00000021-16-122011-0000000018', 2, 'kaos kutung', 15000, 10, 150000, 0),
(46, '2011-12-16 11:57:18', '00000021-16-122011-0000000019', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(47, '2011-12-16 11:57:18', '00000021-16-122011-0000000019', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(48, '2011-12-16 11:57:18', '00000021-16-122011-0000000019', 2, 'kaos kutung', 15000, 10, 150000, 0),
(49, '2011-12-16 11:57:48', '00000021-16-122011-0000000020', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(50, '2011-12-16 11:57:48', '00000021-16-122011-0000000020', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(51, '2011-12-16 11:57:48', '00000021-16-122011-0000000020', 2, 'kaos kutung', 15000, 10, 150000, 0),
(52, '2011-12-16 11:58:48', '00000021-16-122011-0000000021', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(53, '2011-12-16 11:58:48', '00000021-16-122011-0000000021', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(54, '2011-12-16 11:58:48', '00000021-16-122011-0000000021', 2, 'kaos kutung', 15000, 10, 150000, 0),
(55, '2011-12-16 12:00:10', '00000021-16-122011-0000000022', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(56, '2011-12-16 12:00:10', '00000021-16-122011-0000000022', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(57, '2011-12-16 12:00:10', '00000021-16-122011-0000000022', 2, 'kaos kutung', 15000, 10, 150000, 0),
(58, '2011-12-16 12:01:47', '00000021-16-122011-0000000023', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(59, '2011-12-16 12:01:47', '00000021-16-122011-0000000023', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(60, '2011-12-16 12:01:47', '00000021-16-122011-0000000023', 2, 'kaos kutung', 15000, 10, 150000, 0),
(61, '2011-12-16 12:02:31', '00000021-16-122011-0000000024', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(62, '2011-12-16 12:02:31', '00000021-16-122011-0000000024', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(63, '2011-12-16 12:02:31', '00000021-16-122011-0000000024', 2, 'kaos kutung', 15000, 10, 150000, 0),
(64, '2011-12-16 12:21:01', '00000021-16-122011-0000000025', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(65, '2011-12-16 12:21:01', '00000021-16-122011-0000000025', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(66, '2011-12-16 12:21:01', '00000021-16-122011-0000000025', 2, 'kaos kutung', 15000, 10, 150000, 0),
(67, '2011-12-16 12:31:41', '00000021-16-122011-0000000026', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(68, '2011-12-16 12:31:41', '00000021-16-122011-0000000026', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(69, '2011-12-16 12:31:41', '00000021-16-122011-0000000026', 2, 'kaos kutung', 15000, 10, 150000, 0),
(70, '2011-12-16 12:54:43', '00000021-16-122011-0000000027', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(71, '2011-12-16 12:54:43', '00000021-16-122011-0000000027', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(72, '2011-12-16 12:54:43', '00000021-16-122011-0000000027', 2, 'kaos kutung', 15000, 10, 150000, 0),
(73, '2011-12-16 12:57:40', '00000021-16-122011-0000000028', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(74, '2011-12-16 12:57:40', '00000021-16-122011-0000000028', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(75, '2011-12-16 12:57:40', '00000021-16-122011-0000000028', 2, 'kaos kutung', 15000, 10, 150000, 0),
(76, '2011-12-16 01:00:00', '00000021-16-122011-0000000029', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(77, '2011-12-16 01:00:00', '00000021-16-122011-0000000029', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(78, '2011-12-16 01:00:00', '00000021-16-122011-0000000029', 2, 'kaos kutung', 15000, 10, 150000, 0),
(79, '2011-12-16 01:00:47', '00000021-16-122011-0000000030', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(80, '2011-12-16 01:00:47', '00000021-16-122011-0000000030', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(81, '2011-12-16 01:00:47', '00000021-16-122011-0000000030', 2, 'kaos kutung', 15000, 10, 150000, 0),
(82, '2011-12-16 01:02:13', '00000021-16-122011-0000000031', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(83, '2011-12-16 01:02:13', '00000021-16-122011-0000000031', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(84, '2011-12-16 01:02:13', '00000021-16-122011-0000000031', 2, 'kaos kutung', 15000, 10, 150000, 0),
(85, '2011-12-16 01:02:47', '00000021-16-122011-0000000032', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(86, '2011-12-16 01:02:47', '00000021-16-122011-0000000032', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(87, '2011-12-16 01:02:47', '00000021-16-122011-0000000032', 2, 'kaos kutung', 15000, 10, 150000, 0),
(88, '2011-12-16 01:03:31', '00000021-16-122011-0000000033', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(89, '2011-12-16 01:03:31', '00000021-16-122011-0000000033', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(90, '2011-12-16 01:03:31', '00000021-16-122011-0000000033', 2, 'kaos kutung', 15000, 10, 150000, 0),
(91, '2011-12-16 01:18:49', '00000021-16-122011-0000000034', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(92, '2011-12-16 01:18:49', '00000021-16-122011-0000000034', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(93, '2011-12-16 01:18:49', '00000021-16-122011-0000000034', 2, 'kaos kutung', 15000, 10, 150000, 0),
(94, '2011-12-16 01:20:47', '00000021-16-122011-0000000035', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(95, '2011-12-16 01:20:47', '00000021-16-122011-0000000035', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(96, '2011-12-16 01:20:47', '00000021-16-122011-0000000035', 2, 'kaos kutung', 15000, 10, 150000, 0),
(97, '2011-12-16 01:21:49', '00000021-16-122011-0000000036', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(98, '2011-12-16 01:21:49', '00000021-16-122011-0000000036', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(99, '2011-12-16 01:21:49', '00000021-16-122011-0000000036', 2, 'kaos kutung', 15000, 10, 150000, 0),
(100, '2011-12-16 01:24:42', '00000021-16-122011-0000000037', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(101, '2011-12-16 01:24:42', '00000021-16-122011-0000000037', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(102, '2011-12-16 01:24:42', '00000021-16-122011-0000000037', 2, 'kaos kutung', 15000, 10, 150000, 0),
(103, '2011-12-16 01:26:23', '00000021-16-122011-0000000038', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(104, '2011-12-16 01:26:23', '00000021-16-122011-0000000038', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(105, '2011-12-16 01:26:23', '00000021-16-122011-0000000038', 2, 'kaos kutung', 15000, 10, 150000, 0),
(106, '2011-12-16 01:27:13', '00000021-16-122011-0000000039', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(107, '2011-12-16 01:27:13', '00000021-16-122011-0000000039', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(108, '2011-12-16 01:27:13', '00000021-16-122011-0000000039', 2, 'kaos kutung', 15000, 10, 150000, 0),
(109, '2011-12-16 04:08:23', '00000021-16-122011-0000000040', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(110, '2011-12-16 04:08:23', '00000021-16-122011-0000000040', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(111, '2011-12-16 04:08:23', '00000021-16-122011-0000000040', 2, 'kaos kutung', 15000, 10, 150000, 0),
(112, '2011-12-16 04:24:08', '00000021-16-122011-0000000041', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(113, '2011-12-16 04:24:08', '00000021-16-122011-0000000041', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(114, '2011-12-16 04:24:08', '00000021-16-122011-0000000041', 2, 'kaos kutung', 15000, 10, 150000, 0),
(115, '2011-12-16 04:26:02', '00000021-16-122011-0000000042', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(116, '2011-12-16 04:26:02', '00000021-16-122011-0000000042', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(117, '2011-12-16 04:26:02', '00000021-16-122011-0000000042', 2, 'kaos kutung', 15000, 10, 150000, 0),
(118, '2011-12-16 04:54:26', '00000021-16-122011-0000000043', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(119, '2011-12-16 04:54:26', '00000021-16-122011-0000000043', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(120, '2011-12-16 04:54:26', '00000021-16-122011-0000000043', 2, 'kaos kutung', 15000, 10, 150000, 0),
(121, '2011-12-16 04:57:53', '00000021-16-122011-0000000044', 0, 'kaos oblong swan', 3000, 100, 280000, 20000),
(122, '2011-12-16 04:57:53', '00000021-16-122011-0000000044', 1, 'kaos turtle neck', 35000, 2, 70000, 0),
(123, '2011-12-16 04:57:53', '00000021-16-122011-0000000044', 2, 'kaos kutung', 15000, 10, 150000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_order_incoming`
--

CREATE TABLE IF NOT EXISTS `delivery_order_incoming` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ordertime` datetime NOT NULL,
  `buyerdeliverytime` datetime NOT NULL,
  `buyerdeliveryzone` varchar(128) NOT NULL,
  `delivery_id` varchar(40) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `application_key` varchar(128) NOT NULL,
  `buyer_id` bigint(20) NOT NULL,
  `merchant_id` bigint(20) NOT NULL,
  `merchant_trans_id` varchar(128) NOT NULL,
  `shipping_address` text NOT NULL,
  `phone` varchar(40) NOT NULL,
  `status` varchar(40) NOT NULL,
  `reschedule_ref` varchar(40) NOT NULL,
  `revoke_ref` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `delivery_order_incoming`
--

INSERT INTO `delivery_order_incoming` (`id`, `ordertime`, `buyerdeliverytime`, `buyerdeliveryzone`, `delivery_id`, `application_id`, `application_key`, `buyer_id`, `merchant_id`, `merchant_trans_id`, `shipping_address`, `phone`, `status`, `reschedule_ref`, `revoke_ref`) VALUES
(1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '00000017-10-122011-0000000001', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', ''),
(2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '00000017-10-122011-0000000002', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', ''),
(3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '00000017-10-122011-0000000003', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', ''),
(4, '2011-12-10 06:13:06', '0000-00-00 00:00:00', '', '00000017-10-122011-0000000004', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(5, '2011-12-12 11:49:37', '0000-00-00 00:00:00', '', '00000017-12-122011-0000000005', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', ''),
(6, '2011-12-13 08:00:46', '0000-00-00 00:00:00', '', '00000017-13-122011-0000000006', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '123456789', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(7, '2011-12-13 08:05:06', '0000-00-00 00:00:00', '', '00000017-13-122011-0000000007', 1, '23c33397a9b1ecb579c53fe200e26c12709ee379', 1, 17, '278947128941', 'Kompleks DKI D3 Joglo', '02112345678', 'assigned', '', ''),
(8, '2011-12-16 11:05:20', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000008', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 1, 21, '2396152898548266', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(9, '2011-12-16 11:06:50', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000009', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 17, 21, '8107713891485275', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(10, '2011-12-16 11:18:14', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000010', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 18, 21, '5715609627132687', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(11, '2011-12-16 11:18:34', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000011', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 18, 21, '3587370589898276', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(12, '2011-12-16 11:19:34', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000012', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 18, 21, '6107856197366919', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(13, '2011-12-16 11:32:55', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000013', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 0, 21, '9343165125432436', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(14, '2011-12-16 11:33:16', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000014', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '0736666522929835', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(15, '2011-12-16 11:44:45', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000015', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '6694260706672868', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(16, '2011-12-16 11:45:54', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000016', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '0387746980479488', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(17, '2011-12-16 11:55:42', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000017', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '0734489595847198', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(18, '2011-12-16 11:56:32', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000018', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '5409545649571156', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(19, '2011-12-16 11:57:18', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000019', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '9552477858263773', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(20, '2011-12-16 11:57:48', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000020', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '4342947779793056', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(21, '2011-12-16 11:58:48', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000021', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '6667983161567244', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(22, '2011-12-16 12:00:10', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000022', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '9139711818650902', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(23, '2011-12-16 12:01:47', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000023', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '8829345277453916', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(24, '2011-12-16 12:02:31', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000024', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '1933656290651042', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(25, '2011-12-16 12:21:01', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000025', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '3524936322202316', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(26, '2011-12-16 12:31:41', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000026', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '7932217945101378', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(27, '2011-12-16 12:54:43', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000027', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '1049606342531010', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(28, '2011-12-16 12:57:40', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000028', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '9973398338487511', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(29, '2011-12-16 01:00:00', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000029', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '8654859646310570', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(30, '2011-12-16 01:00:47', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000030', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '6953873889720944', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(31, '2011-12-16 01:02:13', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000031', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '8828135992523388', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(32, '2011-12-16 01:02:47', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000032', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '7085545224916106', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(33, '2011-12-16 01:03:31', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000033', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '9510258009702009', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(34, '2011-12-16 01:18:49', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000034', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '5420748251796468', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(35, '2011-12-16 01:20:47', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000035', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '5005982654397125', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(36, '2011-12-16 01:21:49', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000036', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '2338532431326020', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(37, '2011-12-16 01:24:42', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000037', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '7029667086165207', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(38, '2011-12-16 01:26:23', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000038', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '3779489873701273', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(39, '2011-12-16 01:27:13', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000039', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '2552476164212173', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(40, '2011-12-16 04:08:23', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000040', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '4892998013263502', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(41, '2011-12-16 04:24:08', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000041', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '7906011048337030', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(42, '2011-12-16 04:26:02', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000042', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '1149848523371373', 'Kompleks DKI D3 Joglo', '02112345678', 'incoming', '', ''),
(43, '2011-12-16 04:54:26', '2011-12-17 12:30:00', 'Kembangan', '00000021-16-122011-0000000043', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '9114878518132828', 'Kompleks DKI D3 Joglo\nKembangan', '02112345678', 'confirm', '', ''),
(44, '2011-12-16 04:57:53', '0000-00-00 00:00:00', '', '00000021-16-122011-0000000044', 6, 'c07c4f4b23b770c2d4e8d6d108b5c304472df238', 22, 21, '1896490301741620', 'Kompleks DKI D3 Joglo', '02112345678', 'cancel', '', '');

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
-- Table structure for table `device_assignment`
--

CREATE TABLE IF NOT EXISTS `device_assignment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `assign_time` datetime NOT NULL,
  `return_time` datetime NOT NULL,
  `assigment_date` date NOT NULL,
  `admin_id` bigint(20) NOT NULL,
  `device_id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `device_assignment`
--


-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE IF NOT EXISTS `districts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `district` varchar(128) NOT NULL,
  `city` varchar(255) NOT NULL,
  `province` varchar(128) NOT NULL,
  `country` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=65 ;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `district`, `city`, `province`, `country`) VALUES
(1, 'Kepulauan Seribu Utara', 'Kepulauan Seribu', 'DKI Jakarta', 'Indonesia'),
(2, 'Kepulauan Seribu Selatan', 'Kepulauan Seribu', 'DKI Jakarta', 'Indonesia'),
(3, 'Gambir', 'Jakarta Pusat', 'DKI Jakarta', 'Indonesia'),
(4, 'Tanah Abang', 'Jakarta Pusat', 'DKI Jakarta', 'Indonesia'),
(5, 'Sawah Besar', 'Jakarta Pusat', 'DKI Jakarta', 'Indonesia'),
(6, 'Kemayoran', 'Jakarta Pusat', 'DKI Jakarta', 'Indonesia'),
(7, 'Senen', 'Jakarta Pusat', 'DKI Jakarta', 'Indonesia'),
(8, 'Cempaka Putih', 'Jakarta Pusat', 'DKI Jakarta', 'Indonesia'),
(9, 'Menteng', 'Jakarta Pusat', 'DKI Jakarta', 'Indonesia'),
(10, 'Johar Baru', 'Jakarta Pusat', 'DKI Jakarta', 'Indonesia'),
(11, 'Penjaringan', 'Jakarta Utara', 'DKI Jakarta', 'Indonesia'),
(12, 'Tanjung Priok', 'Jakarta Utara', 'DKI Jakarta', 'Indonesia'),
(13, 'Koja', 'Jakarta Utara', 'DKI Jakarta', 'Indonesia'),
(14, 'Cilincing', 'Jakarta Utara', 'DKI Jakarta', 'Indonesia'),
(15, 'Pademangan', 'Jakarta Utara', 'DKI Jakarta', 'Indonesia'),
(16, 'Kelapa Gading', 'Jakarta Utara', 'DKI Jakarta', 'Indonesia'),
(17, 'Cengkareng', 'Jakarta Barat', 'DKI Jakarta', 'Indonesia'),
(18, 'Grogol Petamburan', 'Jakarta Barat', 'DKI Jakarta', 'Indonesia'),
(19, 'Taman Sari', 'Jakarta Barat', 'DKI Jakarta', 'Indonesia'),
(20, 'Tambora', 'Jakarta Barat', 'DKI Jakarta', 'Indonesia'),
(21, 'Kebon Jeruk', 'Jakarta Barat', 'DKI Jakarta', 'Indonesia'),
(22, 'Kalideres', 'Jakarta Barat', 'DKI Jakarta', 'Indonesia'),
(23, 'Pal Merah', 'Jakarta Barat', 'DKI Jakarta', 'Indonesia'),
(24, 'Kembangan', 'Jakarta Barat', 'DKI Jakarta', 'Indonesia'),
(25, 'Kebayoran Baru', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(26, 'Tebet', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(27, 'Setiabudi', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(28, 'Mampang Prapatan', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(29, 'Pasar Minggu', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(30, 'Kebayoran Lama', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(31, 'Cilandak', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(32, 'Pancoran', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(33, 'Jagakarsa', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(34, 'Pesanggrahan', 'Jakarta Selatan', 'DKI Jakarta', 'Indonesia'),
(35, 'Cakung', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(36, 'Matraman', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(37, 'Pulogadung', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(38, 'Jatinegara', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(39, 'Kramatjati', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(40, 'Pasar Rebo', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(41, 'Duren Sawit', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(42, 'Makasar', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(43, 'Ciracas', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(44, 'Cipayung', 'Jakarta Timur', 'DKI Jakarta', 'Indonesia'),
(45, 'Tangerang', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(46, 'Jatiuwung', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(47, 'Batuceper', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(48, 'Benda', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(49, 'Cipondoh', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(50, 'Ciledug', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(51, 'Karawaci', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(52, 'Periuk', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(53, 'Cibodas', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(54, 'Neglasari', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(55, 'Pinang', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(56, 'Karang Tengah', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(57, 'Larangan', 'Tangerang', 'Jawa Barat', 'Indonesia'),
(58, 'Depok', 'Depok', 'Jawa Barat', 'Indonesia'),
(59, 'Pancoran Mas', 'Depok', 'Jawa Barat', 'Indonesia'),
(60, 'Cimanggis', 'Depok', 'Jawa Barat', 'Indonesia'),
(61, 'Sawangan', 'Depok', 'Jawa Barat', 'Indonesia'),
(62, 'Limo', 'Depok', 'Jawa Barat', 'Indonesia'),
(63, 'Sukmajaya', 'Depok', 'Jawa Barat', 'Indonesia'),
(64, 'Beji', 'Depok', 'Jawa Barat', 'Indonesia');

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
  `device_id` int(11) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `courier_id` bigint(20) NOT NULL,
  `latitude` decimal(18,12) NOT NULL,
  `longitude` decimal(18,12) NOT NULL,
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
  `province` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `country` varchar(128) NOT NULL,
  `zip` varchar(25) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `bank` varchar(128) NOT NULL,
  `account_number` varchar(128) NOT NULL,
  `account_name` varchar(128) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '100',
  `token` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `username`, `email`, `password`, `merchantname`, `fullname`, `street`, `district`, `province`, `city`, `country`, `zip`, `phone`, `mobile`, `bank`, `account_number`, `account_name`, `group_id`, `token`, `identifier`) VALUES
(16, 'memberbarulagi', 'membernewlg@merchantsite.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 'PT Maju Jaya', 'Member Paling Baru Deh Ih', 'Jalan H', 'Rawa Belong', '', 'Jakarta Barat', 'United States', '11640', '21 5841281', '21 5841281', 'BCA', '08-08-303030', 'Johnny Iskandar', 4, '', ''),
(17, 'omsenang', 'om@senang.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 'PT Ogah Rugi', 'Oom Senang', 'Salah Jalan', 'Kecamatan Ndiwek', '', 'Sidoarjo', 'Indonesia', '90210', '024109797', '0830303030', '', '', '', 4, '', ''),
(18, 'tegetop', 'tege@mail.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 'Salsa Cafe', 'Teges Prita Baraya', 'Kemang', 'Kebayoran Baru', '', 'Jakarta Selatan', 'Indonesia', '11808080', '089786757', '78675674', '', '', '', 5, '', ''),
(19, 'zztopzz', 'zz@topzz.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', '', 'ZZ', 'Route 66', 'Orange County', '', 'Phoenix', 'USA', '9797987', '23435345', '345345345', '', '', '', 4, '', ''),
(20, 'gantibajucom', 'gantibaju@dot.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', '', 'Admin GantiBaju', 'Senayan Pintu Satu', 'Senayan', '', 'Jakarta Selatan', 'Indonesia', '119797', '09089786', '907878676', '', '', '', 4, '', ''),
(21, 'gantibaju', 'admin@ganti.baju.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', 'Ganti Baju', 'Admin Ganti Baju', 'Kemang', 'Kemang', '', 'Jakarta Selatan', 'Indonesia', '90210', '0219768675', '0811123456789', 'BCA', '097867756646', 'Yang Punya Ganti Baju', 4, '', ''),
(22, 'mauganti', 'mauganti@baju.com', '28e7139ed1bc2ba360b7ce7e68f1891abb22a6c17c0ac1cf4d5358e6812ad643', '', 'Mau Ganti', '', '', '', '', '', '', '', '', '', '', '', 5, '', '');

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

-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 14, 2014 at 11:02 PM
-- Server version: 5.5.38-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `transx`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_codes`
--

CREATE TABLE IF NOT EXISTS `t_codes` (
  `code` varchar(6) NOT NULL,
  `shortdesc` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `field_link` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='t_audit codes';

--
-- Dumping data for table `t_codes`
--

INSERT INTO `t_codes` (`code`, `shortdesc`, `description`, `field_link`) VALUES
('wa0001', 'GCTopup', 'Topup of funds from a voucher to the pool_account', 't_walletauditaction'),
('wa0002', 'GTCRedeem', 'A G-Cash Voucher that was purchased has been redeemed', 't_walletaudit_action'),
('wa0003', 'Lay-ByVoucher', 'A Lay-By Voucher has been generated', 't_walletaudit_action'),
('wa0004', 'BalEnquiry', 'A Balance Enquiry has been done', 't_walletaudit_action'),
('wa0005', 'MiniStatement', 'A Mini-Statement has been requested', 't_walletaudit_action'),
('wa0006', 'FundsTransfer', 'Funds have been transferred between sub-wallets', 't_walletaudit_action'),
('wa0007', 'Airtime', 'Purchase Airtime from pool_balance account', 't_walletaudit_action'),
('wa0008', 'Lay-By Redeem', 'A Lay-By Voucher that was issued has been redeemed for a value', 't_walletaudit_action'),
('PGC010', 'R10GCash', 'Product Code: G-Cash Voucher - R10 Denomination', 't_voucher_product'),
('PGC020', 'R20GCash', 'Product Code: G-Cash Voucher - R20 Denomination', 't_voucher_product'),
('PGC050', 'R50GCash', 'Product Code: G-Cash Voucher - R50 Denomination', 't_voucher_product'),
('PGC0100', 'R100GCash', 'Product Code: G-Cash Voucher - R100 Denomination', 't_voucher_product'),
('PLBVAR', 'LayByeVoucher', 'Product Code: Lay-By Voucher - Variable Amount', 't_voucher_product');

-- --------------------------------------------------------

--
-- Table structure for table `t_customers`
--

CREATE TABLE IF NOT EXISTS `t_customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cust_id` varchar(20) NOT NULL,
  `acc_type` varchar(20) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `contact_email` varchar(50) NOT NULL,
  `contact_num` varchar(10) NOT NULL,
  `ip_add` varchar(15) NOT NULL,
  `sales_repate` int(2) NOT NULL COMMENT '%',
  `login_str` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `t_customers`
--

INSERT INTO `t_customers` (`id`, `cust_id`, `acc_type`, `company_name`, `branch_name`, `contact_person`, `contact_email`, `contact_num`, `ip_add`, `sales_repate`, `login_str`, `password`) VALUES
(1, 'tha001', 'retail', 'Thapelo Test Company', 'Randburg', 'Thapelo Afrika', 'thapeloa@gmail.com', '0798975549', '000.000.000.000', 1, 'tha001testcompany', 'thapelo1234');

-- --------------------------------------------------------

--
-- Table structure for table `t_customerslogin`
--

CREATE TABLE IF NOT EXISTS `t_customerslogin` (
  `trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `session_id` mediumtext NOT NULL,
  `cust_id` varchar(10) NOT NULL,
  `expire_date` datetime NOT NULL,
  `login_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `t_customerslogin`
--

INSERT INTO `t_customerslogin` (`trans_id`, `session_id`, `cust_id`, `expire_date`, `login_date`) VALUES
(1, 'dccbb01e63d802f1b5a2f42d132663b3', 'tha001', '2014-10-12 18:32:35', '2014-10-11 16:32:35'),
(2, 'baa97daa6df352c2345c3547f0d97f56', 'tha001', '2014-10-12 18:34:22', '2014-10-11 16:34:22'),
(3, '050d6eb395f63e94eec069f5a7a16267', 'tha001', '2014-10-12 18:35:42', '2014-10-11 16:35:42'),
(4, '33d7952f4ccdf3384deccec50b3c11ea', 'tha001', '2014-10-12 18:35:50', '2014-10-11 16:35:50'),
(5, '59ce136aa33f3d69f19624d20f7da7e1', 'tha001', '2014-10-12 18:36:02', '2014-10-11 16:36:02'),
(6, '5167152aadbe60363c36b3fae33166bb', 'tha001', '2014-10-12 18:36:44', '2014-10-11 16:36:44'),
(7, '56560a70cc4ff7471ea9f6d9e34dfa00', 'tha001', '2014-10-12 18:37:51', '2014-10-11 16:37:51'),
(8, 'e14df7dcf9a876c79394406c02e9a233', 'tha001', '2014-10-12 18:38:37', '2014-10-11 16:38:37'),
(9, 'fe62ce39290a1895567b667251d98674', 'tha001', '2014-10-12 18:42:28', '2014-10-11 16:42:28'),
(10, '05068a3763bf7d857e542be391a071d1', 'tha001', '2014-10-12 18:42:32', '2014-10-11 16:42:32'),
(11, 'd67fa31880739177346c57a59984a381', 'tha001', '2014-10-12 18:43:38', '2014-10-11 16:43:38'),
(12, '6775e518e84cc18703cd9e159a110de8', 'tha001', '2014-10-12 18:44:04', '2014-10-11 16:44:04'),
(13, '7c743c567718eef82a72ae40a0728c32', 'tha001', '2014-10-12 18:44:19', '2014-10-11 16:44:19'),
(14, '5cb96086a35ba3f393c82b9146bc6c74', 'tha001', '2014-10-12 18:45:10', '2014-10-11 16:45:10'),
(15, '95e6d6c93f91e502cf875154ae818fbf', 'tha001', '2014-10-12 18:46:07', '2014-10-11 16:46:07'),
(16, 'c83f437455c15dbcd92712e6324c6c47', 'tha001', '2014-10-12 18:48:52', '2014-10-11 16:48:52'),
(17, 'a70dfd6931be8f081ef001b9b3ad5c3b', 'tha001', '2014-10-12 18:49:46', '2014-10-11 16:49:46'),
(18, '357c6cb316dfb243b59881b5461f7b50', 'tha001', '2014-10-12 18:50:17', '2014-10-11 16:50:17'),
(19, '0fdd01a8d51efc17fa10d5f34a9f973c', 'tha001', '2014-10-12 18:50:38', '2014-10-11 16:50:38'),
(20, 'c2f46f33ffcd9a194779bb22828fbb48', 'tha001', '2014-10-12 18:51:32', '2014-10-11 16:51:32'),
(21, '773aebf2cc9701d37fa16884523cf443', 'tha001', '2014-10-12 18:53:12', '2014-10-11 16:53:12'),
(22, '8df727841fa359a0df1e90503e3e78b3', 'tha001', '2014-10-12 18:57:04', '2014-10-11 16:57:04'),
(23, '9598ae3fd62813b564dd9f0bc636f9b2', 'tha001', '2014-10-12 19:00:26', '2014-10-11 17:00:26'),
(24, 'd1e8a705f24739ca6fe52ae9e808caf3', 'tha001', '2014-10-12 19:02:34', '2014-10-11 17:02:34'),
(25, 'c2e1734250c7b5132e87f6ec9b6b0609', 'tha001', '2014-10-12 19:02:36', '2014-10-11 17:02:36'),
(26, 'ec08e425a8972d307e142423dfa3e9c1', 'tha001', '2014-10-12 19:03:36', '2014-10-11 17:03:36'),
(27, '5a1e18e1fa11e06bdff9fce9e681d9e2', 'tha001', '2014-10-12 19:04:02', '2014-10-11 17:04:02'),
(28, 'f14628c9de3ee14f00c65a93f64bf819', 'tha001', '2014-10-12 19:07:13', '2014-10-11 17:07:13'),
(29, '87fa1b58a31253e14c46b1ed6c2e9d26', 'tha001', '2014-10-12 19:08:45', '2014-10-11 17:08:45'),
(30, 'b55f8811df460428b26fc070da7f1901', 'tha001', '2014-10-12 19:10:02', '2014-10-11 17:10:02'),
(31, 'ad5db48d17de06848d6c8fce7de116e0', 'tha001', '2014-10-12 19:12:17', '2014-10-11 17:12:17'),
(32, 'a3c11fcc079523cf89e10248c090ab0b', 'tha001', '2014-10-12 19:14:54', '2014-10-11 17:14:54'),
(33, '39465c49c994c967e6b057c72368de3a', 'tha001', '2014-10-12 19:16:08', '2014-10-11 17:16:08'),
(34, 'f56d9df0541fdc4c8830d75fd59cb23b', 'tha001', '2014-10-12 19:20:34', '2014-10-11 17:20:34'),
(35, '99c42f4f6a16b8b47ea83a99a5e7d6f0', 'tha001', '2014-10-12 19:21:48', '2014-10-11 17:21:48'),
(36, '1d115fba2f427e8a4c33f5016e34495f', 'tha001', '2014-10-12 19:22:28', '2014-10-11 17:22:28'),
(37, '759d21c9dfab054571a4903bda0eed7d', 'tha001', '2014-10-12 19:29:01', '2014-10-11 17:29:01'),
(38, 'ece6ad5a9e922cf7a9cc590acc40808a', 'tha001', '2014-10-12 19:32:59', '2014-10-11 17:32:59'),
(39, '19c47ad7cb671115843e8abe1c43f66f', 'tha001', '2014-10-12 19:33:18', '2014-10-11 17:33:18'),
(40, 'ed7cab1d515bfe2ae35b612cf213c465', 'tha001', '2014-10-12 19:34:23', '2014-10-11 17:34:23'),
(41, '6767419a0a0ed46f2ca08be1b82ca4a1', 'tha001', '2014-10-12 19:36:40', '2014-10-11 17:36:40'),
(42, '91458eae139f412d99907affd7101948', 'tha001', '2014-10-12 19:37:28', '2014-10-11 17:37:28'),
(43, '58e73f5cd316d96253b936dcffde059e', 'tha001', '2014-10-12 19:38:27', '2014-10-11 17:38:27'),
(44, '03541ed8472ba772e6f5c5ebe60d217e', 'tha001', '2014-10-12 19:40:23', '2014-10-11 17:40:23'),
(45, '2f4fec2e5d1a1f3e22d4db0c6735e70d', 'tha001', '2014-10-12 19:41:08', '2014-10-11 17:41:08'),
(46, '87d10a0b685bafc729f7118cdc9465df', 'tha001', '2014-10-12 19:47:30', '2014-10-11 17:47:30'),
(47, '18a6ab8285794d946913cdd8bc49b343', 'tha001', '2014-10-12 19:57:37', '2014-10-11 17:57:37'),
(48, '909a662f196d454e741ee5d703c402ab', 'tha001', '2014-10-12 20:10:40', '2014-10-11 18:10:40'),
(49, 'e91da94b342b53be1581abacb0790890', 'tha001', '2014-10-12 20:11:09', '2014-10-11 18:11:09'),
(50, '70d1fd79b4271e61a987f60405444d8c', 'tha001', '2014-10-12 20:12:18', '2014-10-11 18:12:18'),
(51, '529a2f15d29414ec93a366cf5d063b15', 'tha001', '2014-10-12 20:14:06', '2014-10-11 18:14:06'),
(52, '4029ca110615c46f049d81f559fcd1fa', 'tha001', '2014-10-12 20:21:25', '2014-10-11 18:21:25'),
(53, 'f2e8bd6100ebd360f9a528f562e34b85', 'tha001', '2014-10-12 20:21:46', '2014-10-11 18:21:46'),
(54, '76c8f167026432cbd52100f28d2edff3', 'tha001', '2014-10-12 20:22:17', '2014-10-11 18:22:17'),
(55, '15d4ef9d8b7e5468af53a3df4249611a', 'tha001', '2014-10-12 20:32:23', '2014-10-11 18:32:23'),
(56, 'd0b344674b5f47b2576527e363987735', 'tha001', '2014-10-12 20:33:49', '2014-10-11 18:33:49'),
(57, '49d50e230fc8c0d3cb9a11b66652e8b8', 'tha001', '2014-10-12 21:06:35', '2014-10-11 19:06:35'),
(58, '31893f56e9aaf772a9b016e962b4b352', 'tha001', '2014-10-12 21:07:03', '2014-10-11 19:07:03'),
(59, 'cb42f53a37bc059fbaab177d28a45dd8', 'tha001', '2014-10-12 21:08:43', '2014-10-11 19:08:43'),
(60, '3d9775e5f76b24e0ca59fe66f13353ef', 'tha001', '2014-10-12 21:20:12', '2014-10-11 19:20:12'),
(61, '63fa450746b3a8092a7ef40b59cf642e', 'tha001', '2014-10-12 21:57:49', '2014-10-11 19:57:49'),
(62, '24d1a97a447c9c2afac6415cc7142871', 'tha001', '2014-10-12 22:17:32', '2014-10-11 20:17:32'),
(63, '572275686d8451976d8f767d31332c11', 'tha001', '2014-10-12 22:17:36', '2014-10-11 20:17:36'),
(64, 'b11d49d0d50bcc55c5f4b6cb7b04a3ca', 'tha001', '2014-10-15 22:26:59', '2014-10-14 20:26:59'),
(65, '44b509dd13bb2cd7254055806726cfba', 'tha001', '2014-10-15 22:35:15', '2014-10-14 20:35:15'),
(66, '6cabfdd309f9f6d3ba310cd6aa245c58', 'tha001', '2014-10-15 22:50:17', '2014-10-14 20:50:17'),
(67, '324b1028cd427b5ee6933a2af4e59708', 'tha001', '2014-10-15 22:51:19', '2014-10-14 20:51:19'),
(68, '5339596879d04f4421d2f5e456f4814d', 'tha001', '2014-10-15 22:59:24', '2014-10-14 20:59:24');

-- --------------------------------------------------------

--
-- Table structure for table `t_voucher`
--

CREATE TABLE IF NOT EXISTS `t_voucher` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `voucher_number` int(9) NOT NULL,
  `serial_number` varchar(10) NOT NULL,
  `order_num` int(9) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `product` varchar(6) NOT NULL,
  `issue_date` datetime NOT NULL,
  `issue_cust_id` varchar(6) NOT NULL,
  `allocated_date` datetime NOT NULL,
  `w_reference_allocated` varchar(8) NOT NULL,
  `redeem_date` datetime NOT NULL,
  `w_reference_redeemed` varchar(8) NOT NULL,
  `allocated` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `voucher_number` (`voucher_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `t_voucher`
--

INSERT INTO `t_voucher` (`id`, `voucher_number`, `serial_number`, `order_num`, `value`, `product`, `issue_date`, `issue_cust_id`, `allocated_date`, `w_reference_allocated`, `redeem_date`, `w_reference_redeemed`, `allocated`) VALUES
(1, 123456789, '12345', 7738927, 100.00, 'PGC100', '2014-10-08 14:34:21', 'GLO001', '2014-10-08 14:34:21', '000031', '2014-10-11 22:03:11', '000031', 0),
(2, 0, 'WD7L7VNRB', 7738928, 0.00, 'wa0003', '2014-10-14 22:51:54', 'GLO001', '2014-10-14 22:51:54', '000034', '0000-00-00 00:00:00', '', 0),
(3, 0, 'NDV0J0PCZ', 7738928, 0.00, 'wa0003', '2014-10-14 22:52:17', 'GLO001', '2014-10-14 22:52:17', '000035', '0000-00-00 00:00:00', '', 0),
(4, 923056796, '2WFC83VM4', 7738929, 0.00, 'wa0003', '2014-10-14 22:54:28', 'GLO001', '2014-10-14 22:54:28', '000036', '0000-00-00 00:00:00', '', 0),
(5, 25756804, 'KRZHLDHW0', 7738930, 0.00, 'wa0003', '2014-10-14 22:58:08', 'GLO001', '2014-10-14 22:58:08', '000037', '0000-00-00 00:00:00', '', 0),
(6, 512656804, 'WDCJ9MQKE', 7738931, 0.00, 'wa0003', '2014-10-14 22:59:28', 'GLO001', '2014-10-14 22:59:28', '000038', '0000-00-00 00:00:00', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `t_voucherorders`
--

CREATE TABLE IF NOT EXISTS `t_voucherorders` (
  `customer_id` varchar(6) NOT NULL,
  `order_num` int(9) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `request_date` datetime NOT NULL,
  `PGC010` decimal(10,2) NOT NULL,
  `PGC020` decimal(10,2) NOT NULL,
  `PGC050` decimal(10,2) NOT NULL,
  `PGC0100` decimal(10,2) NOT NULL,
  `release_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_voucherorders`
--

INSERT INTO `t_voucherorders` (`customer_id`, `order_num`, `value`, `request_date`, `PGC010`, `PGC020`, `PGC050`, `PGC0100`, `release_date`) VALUES
('GLO001', 7738927, 1800.00, '2014-10-08 14:40:57', 10.00, 10.00, 10.00, 10.00, '2014-10-08 14:40:57'),
('GLO001', 7738928, 0.00, '2014-10-14 22:52:17', 0.00, 0.00, 0.00, 0.00, '0000-00-00 00:00:00'),
('GLO001', 7738929, 0.00, '2014-10-14 22:54:27', 0.00, 0.00, 0.00, 0.00, '0000-00-00 00:00:00'),
('GLO001', 7738930, 0.00, '2014-10-14 22:58:08', 0.00, 0.00, 0.00, 0.00, '0000-00-00 00:00:00'),
('GLO001', 7738931, 0.00, '2014-10-14 22:59:27', 0.00, 0.00, 0.00, 0.00, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_wallet`
--

CREATE TABLE IF NOT EXISTS `t_wallet` (
  `uid` int(11) NOT NULL,
  `pool_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `kcm_wallet` decimal(10,2) NOT NULL DEFAULT '0.00',
  `glo_wallet` decimal(10,2) NOT NULL DEFAULT '0.00',
  `last_transaction_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_wallet`
--

INSERT INTO `t_wallet` (`uid`, `pool_balance`, `kcm_wallet`, `glo_wallet`, `last_transaction_date`) VALUES
(123, 300.00, 40.00, 60.00, '2014-10-11 22:03:11');

-- --------------------------------------------------------

--
-- Table structure for table `t_walletaudit`
--

CREATE TABLE IF NOT EXISTS `t_walletaudit` (
  `Id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `w_reference` int(7) unsigned zerofill NOT NULL,
  `trans_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` varchar(6) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `source_pool` varchar(15) NOT NULL,
  `destination_pool` varchar(15) NOT NULL,
  `voucher_serial_number` varchar(9) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `t_walletaudit`
--

INSERT INTO `t_walletaudit` (`Id`, `uid`, `w_reference`, `trans_date`, `action`, `value`, `source_pool`, `destination_pool`, `voucher_serial_number`) VALUES
(1, 123, 0000001, '2014-10-08 12:26:14', 'wa0001', 100.00, '', '', '12345'),
(2, 123, 0000002, '2014-10-09 13:15:09', 'wa0004', 0.00, '', '', ''),
(3, 123, 0000003, '2014-10-09 13:25:54', 'wa0006', 100.00, '', '', ''),
(4, 123, 0000004, '2014-10-09 13:40:42', 'wa0005', 0.00, '', '', ''),
(5, 123, 0000005, '2014-10-09 13:41:34', 'wa0005', 0.00, '', '', ''),
(6, 123, 0000006, '2014-10-11 17:58:14', 'wa0006', 50.00, 'pool_balance', '', ''),
(7, 123, 0000007, '2014-10-11 17:59:38', 'wa0006', 50.00, 'pool_balance', '', ''),
(8, 123, 0000008, '2014-10-11 18:00:04', 'wa0006', 50.00, 'pool_balance', '', ''),
(9, 123, 0000009, '2014-10-11 18:01:22', 'wa0006', 50.00, 'pool_balance', 'glo_wallet', ''),
(10, 123, 0000010, '2014-10-11 18:08:10', 'wa0006', 50.00, 'pool_balance', 'glo_wallet', ''),
(11, 123, 0000011, '2014-10-11 18:14:12', 'wa0006', 50.00, 'glo_wallet', 'pool_balance', ''),
(12, 123, 0000012, '2014-10-11 18:14:29', 'wa0006', 20.00, 'kcm_wallet', 'pool_balance', ''),
(13, 123, 0000013, '2014-10-11 18:16:22', 'wa0006', 20.00, 'glo_wallet', 'glo_wallet', ''),
(14, 123, 0000014, '2014-10-11 18:21:25', 'wa0004', 0.00, '', '', ''),
(15, 123, 0000015, '2014-10-11 18:21:47', 'wa0004', 0.00, '', '', ''),
(16, 123, 0000016, '2014-10-11 18:21:51', 'wa0004', 0.00, '', '', ''),
(17, 123, 0000017, '2014-10-11 18:22:19', 'wa0004', 0.00, '', '', ''),
(18, 123, 0000018, '2014-10-11 18:22:21', 'wa0004', 0.00, '', '', ''),
(19, 123, 0000019, '2014-10-11 18:32:25', 'wa0005', 0.00, '', '', ''),
(20, 123, 0000020, '2014-10-11 18:33:50', 'wa0005', 0.00, '', '', ''),
(21, 123, 0000021, '2014-10-11 19:06:36', 'wa0005', 0.00, '', '', ''),
(22, 123, 0000022, '2014-10-11 19:07:05', 'wa0005', 0.00, '', '', ''),
(23, 123, 0000023, '2014-10-11 19:07:11', 'wa0004', 0.00, '', '', ''),
(24, 123, 0000024, '2014-10-11 19:08:48', 'wa0005', 0.00, '', '', ''),
(25, 123, 0000025, '2014-10-11 19:08:51', 'wa0004', 0.00, '', '', ''),
(26, 123, 0000026, '2014-10-11 19:08:53', 'wa0005', 0.00, '', '', ''),
(27, 123, 0000027, '2014-10-11 19:08:57', 'wa0004', 0.00, '', '', ''),
(28, 123, 0000028, '2014-10-11 19:08:57', 'wa0005', 0.00, '', '', ''),
(29, 123, 0000029, '2014-10-11 20:00:26', '', 100.00, '', '', ''),
(30, 123, 0000030, '2014-10-11 20:02:12', 'PGC100', 100.00, '', '', ''),
(31, 123, 0000031, '2014-10-11 20:03:11', 'PGC100', 100.00, '', '', '123456789'),
(32, 123, 0000032, '2014-10-11 20:17:37', 'wa0004', 0.00, '', '', ''),
(33, 123, 0000033, '2014-10-14 20:27:08', 'wa0004', 0.00, '', '', ''),
(34, 123, 0000034, '2014-10-14 20:51:54', 'wa0003', 0.00, 'pool', '', ''),
(35, 123, 0000035, '2014-10-14 20:52:17', 'wa0003', 0.00, 'pool', '', ''),
(36, 123, 0000036, '2014-10-14 20:54:27', 'wa0003', 0.00, 'pool', '', '923056796'),
(37, 123, 0000037, '2014-10-14 20:58:08', 'wa0003', 0.00, 'pool', '', '025756804'),
(38, 123, 0000038, '2014-10-14 20:59:27', 'wa0003', 0.00, 'glocell', '', '512656804');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2014 at 05:13 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `transx`
--
CREATE DATABASE IF NOT EXISTS `transx` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `transx`;

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
('PGC010', 'R100GCash', 'Product Code: G-Cash Voucher - R100 Denomination', 't_voucher_product'),
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

INSERT INTO `t_customers` (`cust_id`, `acc_type`, `company_name`, `branch_name`, `contact_person`, `contact_email`, `contact_num`, `ip_add`, `sales_repate`, `login_str`, `password`) VALUES
('tha001', 'retail', 'Thapelo Test Company', 'Randburg', 'Thapelo Afrika', 'thapeloa@gmail.com', '0798975549', '000.000.000.000', 1, 'tha001testcompany', 'thapelo1234');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Dumping data for table `t_customerslogin`
--

INSERT INTO `t_customerslogin` (`trans_id`, `session_id`, `cust_id`, `expire_date`, `login_date`) VALUES
(1, 'dce9497ccddea57ab782d1e77e6f1e80', 'tha001', '0000-00-00 00:00:00', '2014-10-08 12:41:15'),
(2, '8eba58e4ed4477b417d776f7caf23fe6', 'tha001', '0000-00-00 00:00:00', '2014-10-08 12:42:23'),
(3, 'df0fc245361a51e5c8d6164cf4a72cc1', 'tha001', '2014-10-09 12:50:33', '2014-10-08 12:50:33'),
(4, '61c0f39115eb8032bd43e3aef44d86be', 'tha001', '2014-10-10 09:49:58', '2014-10-09 09:49:58'),
(5, '7e11e14f7bd3736bfa05624da696c198', 'tha001', '2014-10-10 09:50:06', '2014-10-09 09:50:06'),
(6, '2eeed8fc5d250562432c75749770e3c5', 'tha001', '2014-10-10 09:50:07', '2014-10-09 09:50:07'),
(7, 'c87517b3a40dd5e9fff5de5e83361df4', 'tha001', '2014-10-10 09:50:41', '2014-10-09 09:50:41'),
(8, '9e64998719b49a4bc0be0adc37742d8d', 'tha001', '2014-10-10 09:51:27', '2014-10-09 09:51:27'),
(9, '516409c98361ae1658b3d5b94152f38e', 'tha001', '2014-10-10 09:51:41', '2014-10-09 09:51:41'),
(10, '0e1fd59b9f5282a6ebc4614341a29248', 'tha001', '2014-10-10 09:51:55', '2014-10-09 09:51:55'),
(11, '0f27270be4b92a977edec8aa225b6fd8', 'tha001', '2014-10-10 09:54:10', '2014-10-09 09:54:10'),
(12, '6887cbd5e4a097c1f988c28cb8fe9775', 'tha001', '2014-10-10 09:54:58', '2014-10-09 09:54:58'),
(13, 'd7734d16eef4f23facc02a8732060ad9', 'tha001', '2014-10-10 09:55:38', '2014-10-09 09:55:38'),
(14, '996c478d18c2ebd1499fdf57c824b100', 'tha001', '2014-10-10 09:57:18', '2014-10-09 09:57:18'),
(15, 'aa559ce1693ef6a4398190cf4d7a9917', 'tha001', '2014-10-10 09:57:33', '2014-10-09 09:57:33'),
(16, '62f1434e8a09b1fd18f7376e1d234586', 'tha001', '2014-10-10 09:58:11', '2014-10-09 09:58:11'),
(17, '31ae3c98f5043183886e99b56543d8bb', 'tha001', '2014-10-10 09:58:42', '2014-10-09 09:58:42'),
(18, 'b68c4b1a5e1b99b68cf17be1567eab4f', 'tha001', '2014-10-10 09:58:59', '2014-10-09 09:58:59'),
(19, '11078367ed3be5f3859257a8d8213bb4', 'tha001', '2014-10-10 10:00:43', '2014-10-09 10:00:43'),
(20, 'b300f34a18add195d68482489bd7a281', 'tha001', '2014-10-10 10:01:22', '2014-10-09 10:01:22'),
(21, 'b55b3e296958009f0cc859616b42f697', 'tha001', '2014-10-10 10:02:01', '2014-10-09 10:02:01'),
(22, '1c5343f6c676ed8d05b9fd89ae91b2f0', 'tha001', '2014-10-10 10:02:02', '2014-10-09 10:02:02'),
(23, '5b187b71031e753027227838adb1068f', 'tha001', '2014-10-10 10:02:21', '2014-10-09 10:02:21'),
(24, '7b84c0e928b113debbe6c6f8bf420669', 'tha001', '2014-10-10 10:03:01', '2014-10-09 10:03:01'),
(25, '47109c4f4ccad8ae3ac162c6d5921f1d', 'tha001', '2014-10-10 10:04:42', '2014-10-09 10:04:42'),
(26, 'f6f83bd3a640bbc0fa22c092d2f22672', 'tha001', '2014-10-10 10:04:59', '2014-10-09 10:04:59'),
(27, '32c4897772763dfbc27fcce6f619f285', 'tha001', '2014-10-10 15:11:10', '2014-10-09 15:11:10'),
(28, '08e39bcf66e78557d11276036d98269f', 'tha001', '2014-10-10 15:40:40', '2014-10-09 15:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `t_voucher`
--

CREATE TABLE IF NOT EXISTS `t_voucher` (
  `voucher_number` int(8) NOT NULL,
  `serial_number` varchar(9) NOT NULL,
  `order_num` int(9) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `product` varchar(6) NOT NULL,
  `issue_date` datetime NOT NULL,
  `issue_cust_id` varchar(6) NOT NULL,
  `allocated_date` datetime NOT NULL,
  `w_reference_allocated` varchar(8) NOT NULL,
  `redeem_date` datetime NOT NULL,
  `w_reference_redeemed` varchar(8) NOT NULL,
  `redeemed_cust_id6` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_voucher`
--

INSERT INTO `t_voucher` (`voucher_number`, `serial_number`, `order_num`, `value`, `product`, `issue_date`, `issue_cust_id`, `allocated_date`, `w_reference_allocated`, `redeem_date`, `w_reference_redeemed`, `redeemed_cust_id6`) VALUES
(54321, '12345', 7738927, '100.00', 'PGC100', '2014-10-08 14:34:21', 'GLO001', '2014-10-08 14:34:21', 'W0000002', '2014-10-08 14:34:21', 'W0000002', 'IntSys');

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
('GLO001', 7738927, '1800.00', '2014-10-08 14:40:57', '10.00', '10.00', '10.00', '10.00', '2014-10-08 14:40:57');

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
(123, '100.00', '60.00', '10.00', '2014-10-08 14:24:07');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `t_walletaudit`
--

INSERT INTO `t_walletaudit` (`Id`, `uid`, `w_reference`, `trans_date`, `action`, `value`, `source_pool`, `destination_pool`, `voucher_serial_number`) VALUES
(1, 123, 0000001, '2014-10-08 12:26:14', 'wa0001', '100.00', '', '', '12345'),
(2, 123, 0000002, '2014-10-09 13:15:09', 'wa0004', '0.00', '', '', ''),
(3, 123, 0000003, '2014-10-09 13:25:54', 'wa0006', '100.00', '', '', ''),
(4, 123, 0000004, '2014-10-09 13:40:42', 'wa0005', '0.00', '', '', ''),
(5, 123, 0000005, '2014-10-09 13:41:34', 'wa0005', '0.00', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

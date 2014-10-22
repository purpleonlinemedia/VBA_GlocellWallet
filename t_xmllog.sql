-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2014 at 03:10 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `t_xmllog`
--

CREATE TABLE IF NOT EXISTS `t_xmllog` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `xmlIn` longtext NOT NULL,
  `xmlOut` longtext NOT NULL,
  `hostAddr` varchar(20) NOT NULL,
  `dateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `t_xmllog`
--

INSERT INTO `t_xmllog` (`id`, `xmlIn`, `xmlOut`, `hostAddr`, `dateTime`) VALUES
(1, '<?xml version=''1.0''?><serviceRequest><action>get_balance</action><session_id>12ec542a3cf6222550b0f18b26015a73</session_id><uid>123</uid></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Fail</status>\r\n <error>Invalid request type</error> \r\n</serviceResponse>', '127.0.0.1', '2014-10-22 09:21:06'),
(2, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>5a7dbb6bf997644b5c7b09146ba7d0e6</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:02:36'),
(3, '<?xml version=''1.0''?><serviceRequest><action>get_statement</action><session_id>5a7dbb6bf997644b5c7b09146ba7d0e6</session_id><uid>123</uid></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>\r\n <status>Success</status>\r\n <error></error>\r\n <statement>2014-10-09 15:41:34,MiniStatement,0.00|2014-10-09 15:40:42,MiniStatement,0.00|2014-10-09 15:25:54,FundsTransfer,100.00|2014-10-09 15:15:09,BalEnquiry,0.00|2014-10-08 14:26:14,GCTopup,100.00</statement>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:02:39'),
(4, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>787cd03b7a1d581330884617b40a2e0b</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:05:24'),
(5, '<?xml version=''1.0''?><serviceRequest><action>get_statements</action><session_id>787cd03b7a1d581330884617b40a2e0b</session_id><uid>123</uid></serviceRequest>', '', '127.0.0.1', '2014-10-22 10:05:26'),
(6, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>39f325a0775d21034484d3933f9f8253</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:07:56'),
(7, '<?xml version=''1.0''?><serviceRequest><action>get_statement</action><session_id>39f325a0775d21034484d3933f9f8253123</session_id><uid>123</uid></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>\r\n <status>Success</status>\r\n <error></error>\r\n <statement>2014-10-22 12:02:39,MiniStatement,0.00|2014-10-09 15:41:34,MiniStatement,0.00|2014-10-09 15:40:42,MiniStatement,0.00|2014-10-09 15:25:54,FundsTransfer,100.00|2014-10-09 15:15:09,BalEnquiry,0.00|2014-10-08 14:26:14,GCTopup,100.00</statement>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:07:58'),
(8, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>0a26d2fb3e968dcc1a42d6e459c2a33a</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:13:42'),
(9, '<?xml version=''1.0''?><serviceRequest><action>get_statement</action><session_id>0a26d2fb3e968dcc1a42d6e459c2a33a123</session_id><uid>123</uid></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>\r\n <status>Success</status>\r\n <error></error>\r\n <statement>2014-10-22 12:07:58,MiniStatement,0.00|2014-10-22 12:02:39,MiniStatement,0.00|2014-10-09 15:41:34,MiniStatement,0.00|2014-10-09 15:40:42,MiniStatement,0.00|2014-10-09 15:25:54,FundsTransfer,100.00|2014-10-09 15:15:09,BalEnquiry,0.00|2014-10-08 14:26:14,GCTopup,100.00</statement>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:13:45'),
(10, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>eadc815cfeb4f9213c8221cde2bfc824</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:14:32'),
(11, '<?xml version=''1.0''?><serviceRequest><action>get_statement</action><session_id>eadc815cfeb4f9213c8221cde2bfc824123</session_id><uid>123</uid></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>\r\n <status>Success</status>\r\n <error></error>\r\n <statement>2014-10-22 12:13:45,MiniStatement,0.00|2014-10-22 12:07:58,MiniStatement,0.00|2014-10-22 12:02:39,MiniStatement,0.00|2014-10-09 15:41:34,MiniStatement,0.00|2014-10-09 15:40:42,MiniStatement,0.00|2014-10-09 15:25:54,FundsTransfer,100.00|2014-10-09 15:15:09,BalEnquiry,0.00|2014-10-08 14:26:14,GCTopup,100.00</statement>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:14:34'),
(12, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>b93d6393993b0f8626ff20a8b64d2530</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:14:51'),
(13, '<?xml version=''1.0''?><serviceRequest><action>get_statement</action><session_id>b93d6393993b0f8626ff20a8b64d2530123</session_id><uid>123</uid></serviceRequest>', '', '127.0.0.1', '2014-10-22 10:14:52'),
(14, '', '', '127.0.0.1', '2014-10-22 10:17:05'),
(15, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>a79e4a3e023710221d69842c37230be6</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:17:09'),
(16, '<?xml version=''1.0''?><serviceRequest><action>funds_transfer</action><session_id>a79e4a3e023710221d69842c37230be6</session_id><uid>123</uid><value>50S</value><sourceacc>pool</sourceacc><destacc>glocell</destacc></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Fail</status>\r\n <error>Invalid value</error> \r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:17:23'),
(17, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>c61b6a7ddea88f0e49bf45757c486e31</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:17:40'),
(18, '<?xml version=''1.0''?><serviceRequest><action>funds_transfer</action><session_id>c61b6a7ddea88f0e49bf45757c486e31</session_id><uid>123</uid><value>50.00</value><sourceacc>poolS</sourceacc><destacc>glocell</destacc></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Fail</status>\r\n <error>Invalid source account</error> \r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:17:51'),
(19, '<?xml version=''1.0''?><serviceRequest><action>login</action><uname>tha001testcompany</uname><password>thapelo1234</password></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error>\r\n <session_id>4302424fc9aeaa9960f7469ec2837311</session_id>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:18:09'),
(20, '<?xml version=''1.0''?><serviceRequest><action>funds_transfer</action><session_id>4302424fc9aeaa9960f7469ec2837311</session_id><uid>123</uid><value>50.00</value><sourceacc>pool</sourceacc><destacc>glocell</destacc></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>\r\n <status>Success</status>\r\n <error></error>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:18:22'),
(21, '<?xml version=''1.0''?><serviceRequest><action>get_balance</action><session_id>4302424fc9aeaa9960f7469ec2837311</session_id><uid>123</uid></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error> \r\n <pool_balance>50.00</pool_balance> \r\n <kcm_wallet_balance>60.00</kcm_wallet_balance>                 \r\n <glo_wallet_balance>60.00</glo_wallet_balance>                 \r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:18:26'),
(22, '<?xml version=''1.0''?><serviceRequest><action>funds_transfer</action><session_id>4302424fc9aeaa9960f7469ec2837311</session_id><uid>123</uid><value>10.00</value><sourceacc>glocell</sourceacc><destacc>pool</destacc></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>\r\n <status>Success</status>\r\n <error></error>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:18:37'),
(23, '<?xml version=''1.0''?><serviceRequest><action>get_balance</action><session_id>4302424fc9aeaa9960f7469ec2837311</session_id><uid>123</uid></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error> \r\n <pool_balance>60.00</pool_balance> \r\n <kcm_wallet_balance>60.00</kcm_wallet_balance>                 \r\n <glo_wallet_balance>50.00</glo_wallet_balance>                 \r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:18:41'),
(24, '<?xml version=''1.0''?><serviceRequest><action>funds_transfer</action><session_id>4302424fc9aeaa9960f7469ec2837311</session_id><uid>123</uid><value>10.00</value><sourceacc>glocell</sourceacc><destacc>pool</destacc></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>\r\n <status>Success</status>\r\n <error></error>\r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:18:54'),
(25, '<?xml version=''1.0''?><serviceRequest><action>get_balance</action><session_id>4302424fc9aeaa9960f7469ec2837311</session_id><uid>123</uid></serviceRequest>', '<?xml version=''1.0''?>\r\n<serviceResponse>                 \r\n <status>Success</status>\r\n <error></error> \r\n <pool_balance>70.00</pool_balance> \r\n <kcm_wallet_balance>60.00</kcm_wallet_balance>                 \r\n <glo_wallet_balance>40.00</glo_wallet_balance>                 \r\n</serviceResponse>', '127.0.0.1', '2014-10-22 10:18:56');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

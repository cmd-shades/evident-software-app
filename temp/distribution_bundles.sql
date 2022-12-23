-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2021 at 01:03 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techlive`
--

-- --------------------------------------------------------

--
-- Table structure for table `distribution_bundles`
--

CREATE TABLE `distribution_bundles` (
  `distribution_bundle_id` int(11) UNSIGNED NOT NULL,
  `distribution_bundle` varchar(125) DEFAULT NULL,
  `distribution_bundle_desc` varchar(255) DEFAULT NULL,
  `distribution_bundle_ref` varchar(255) DEFAULT NULL,
  `distribution_group_id` int(11) DEFAULT NULL,
  `license_start_date` date DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `last_modified_by` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `base_line` tinyint(1) DEFAULT 0,
  `schedule_date_time` datetime DEFAULT NULL,
  `send_status` varchar(255) DEFAULT 'Planned',
  `coggins__id` varchar(255) NOT NULL,
  `coggins_name` varchar(255) DEFAULT NULL,
  `coggins_uid` varchar(255) NOT NULL,
  `coggins_state` varchar(255) NOT NULL,
  `coggins_progress` varchar(255) DEFAULT NULL,
  `coggins_errors` varchar(255) DEFAULT NULL,
  `send_status_timestamp` datetime DEFAULT NULL,
  `cds_folder_name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `distribution_bundles`
--

INSERT INTO `distribution_bundles` (`distribution_bundle_id`, `distribution_bundle`, `distribution_bundle_desc`, `distribution_bundle_ref`, `distribution_group_id`, `license_start_date`, `date_created`, `created_by`, `last_modified`, `last_modified_by`, `account_id`, `base_line`, `schedule_date_time`, `send_status`, `coggins__id`, `coggins_name`, `coggins_uid`, `coggins_state`, `coggins_progress`, `coggins_errors`, `send_status_timestamp`, `cds_folder_name`, `is_active`, `archived`) VALUES
(28, 'Evident Test - Cancel Test', NULL, 'EVIDENTTEST-CANCELTEST15', 5, '2021-07-06', '2021-07-06 15:34:33', 4, '2021-07-07 13:19:21', 4, 1, 0, '2021-07-06 17:35:00', 'sent', '60e4783836ed92dcc15be15b', 'Coggins-RichLTP-210706-173519', '5H6YIGqsUl', 'finished', '68.540345947272', '36', '2021-07-06 16:35:19', NULL, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `distribution_bundles`
--
ALTER TABLE `distribution_bundles`
  ADD PRIMARY KEY (`distribution_bundle_id`),
  ADD KEY `DistroGrpId` (`distribution_group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `distribution_bundles`
--
ALTER TABLE `distribution_bundles`
  MODIFY `distribution_bundle_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

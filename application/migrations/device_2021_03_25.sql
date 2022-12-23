-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2021 at 09:22 AM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
-- Table structure for table `device`
--


CREATE TABLE `device` (
  `device_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `device_unique_id` varchar(255) NOT NULL COMMENT 'Serial Number',
  `external_reference_id` varchar(255) NOT NULL,
  `platform_id` int(11) UNSIGNED DEFAULT NULL,
  `airtime_status` enum('connected','disconnected') NOT NULL DEFAULT 'disconnected',
  `easel_segment_id` varchar(255) NOT NULL,
  `external_platform` varchar(255) NOT NULL,
  `external_firstConnected` varchar(255) NOT NULL,
  `external_lastConnected` varchar(255) NOT NULL,
  `create_error` varchar(255) DEFAULT NULL,
  `connect_error` varchar(255) DEFAULT NULL,
  `disconnect_error` varchar(255) DEFAULT NULL,
  `link_error` varchar(255) DEFAULT NULL,
  `unlink_error` varchar(255) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`device_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `device_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

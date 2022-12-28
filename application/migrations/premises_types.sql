-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 22, 2022 at 09:21 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `evident_core_tess`
--

-- --------------------------------------------------------

--
-- Table structure for table `premises_types`
--

CREATE TABLE `premises_types` (
  `premises_type_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED DEFAULT NULL,
  `premises_type` varchar(450) DEFAULT NULL,
  `premises_type_ref` varchar(450) DEFAULT NULL,
  `premises_group` varchar(75) DEFAULT NULL,
  `premises_type_desc` varchar(255) DEFAULT NULL,
  `is_subaddress_required` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `primary_attribute_id` int(11) UNSIGNED DEFAULT NULL,
  `discipline_id` int(11) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_modified` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `last_modified_by` int(11) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `premises_types`
--

INSERT INTO `premises_types` (`premises_type_id`, `account_id`, `premises_type`, `premises_type_ref`, `premises_group`, `premises_type_desc`, `is_subaddress_required`, `primary_attribute_id`, `discipline_id`, `created_by`, `date_created`, `last_modified`, `last_modified_by`, `is_active`, `archived`) VALUES
(1, 21, 'Dwelling', 'ARCH_21DWELLING1', NULL, 'The apartment contains only one bedroom or without a bedroom.', 1, 1, NULL, 1, '2022-03-21 21:13:16', '2022-03-22 21:14:57', 1, 0, 0),
(2, 21, 'Room', 'room', NULL, 'Singular ensuite room', 0, 1, NULL, 1, '2022-03-21 21:13:16', '2022-03-22 11:46:14', NULL, 1, 0),
(3, 21, 'Pole', 'ARCH_ARCH_21DWELLING1', NULL, 'Communication pole', 0, 2, NULL, 1, '2022-03-21 21:18:04', '2022-03-22 21:18:58', 1, 0, 0),
(4, 21, 'Chamber', 'chamber', NULL, 'Fibre chamber', 0, 3, NULL, 1, '2022-03-21 21:18:04', '2022-03-22 11:46:17', NULL, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `premises_types`
--
ALTER TABLE `premises_types`
  ADD PRIMARY KEY (`premises_type_id`),
  ADD KEY `accountId` (`account_id`),
  ADD KEY `isActive` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `premises_types`
--
ALTER TABLE `premises_types`
  MODIFY `premises_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

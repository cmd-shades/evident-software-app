-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2021 at 05:47 PM
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
-- Database: `techlive`
--

-- --------------------------------------------------------

--
-- Table structure for table `coggins_distributed_content`
--

CREATE TABLE `coggins_distributed_content` (
  `distributed_content_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `distribution_group_id` int(11) UNSIGNED NOT NULL,
  `distribution_bundle_id` int(11) UNSIGNED NOT NULL,
  `content_id` int(11) UNSIGNED NOT NULL,
  `server_ids` varchar(255) NOT NULL,
  `coggins_output` text DEFAULT NULL,
  `coggins__id` varchar(255) NOT NULL,
  `coggins_uid` varchar(255) NOT NULL,
  `coggins_state` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coggins_distributed_content`
--
ALTER TABLE `coggins_distributed_content`
  ADD PRIMARY KEY (`distributed_content_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coggins_distributed_content`
--
ALTER TABLE `coggins_distributed_content`
  MODIFY `distributed_content_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

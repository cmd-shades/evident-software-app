-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2021 at 05:48 PM
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
-- Table structure for table `coggins_debugging`
--

CREATE TABLE `coggins_debugging` (
  `request_id` int(11) NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `request_name` varchar(255) NOT NULL,
  `request_data` text NOT NULL,
  `full_response` text NOT NULL,
  `api-status` varchar(255) NOT NULL,
  `api-code` varchar(255) NOT NULL,
  `api-message` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `requested_by` int(11) UNSIGNED NOT NULL,
  `requested_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coggins_debugging`
--
ALTER TABLE `coggins_debugging`
  ADD PRIMARY KEY (`request_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coggins_debugging`
--
ALTER TABLE `coggins_debugging`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

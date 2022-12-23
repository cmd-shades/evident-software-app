-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2021 at 12:45 PM
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
-- Table structure for table `devices_tmp_upload`
--

CREATE TABLE `devices_tmp_upload` (
  `tmp_device_id` int(11) UNSIGNED NOT NULL,
  `site_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `device_unique_id` varchar(255) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `devices_tmp_upload`
--

INSERT INTO `devices_tmp_upload` (`tmp_device_id`, `site_id`, `product_id`, `device_unique_id`, `platform`, `created_date`, `created_by`) VALUES
(1, 141, 205, '834y7r8y7e1', 'tvos', '2021-03-18 12:29:31', 4),
(2, 141, 122, 'i219iw0e1', 'ios', '2021-03-18 12:29:31', 4),
(3, 141, 205, '283u9*&e3e1', 'amazonfiretvone', '2021-03-18 12:29:31', 4),
(4, 141, 220, '98wu4/3r1', 'freesat', '2021-03-18 12:29:31', 4),
(5, 141, 205, '98wqu39u291', 'tivo', '2021-03-18 12:29:31', 4),
(6, 141, 220, '5096iy0961', 'lgfr', '2021-03-18 12:29:31', 4),
(7, 141, 205, '9dfi0b1', 'androidtv', '2021-03-18 12:29:31', 4),
(8, 141, 220, '093403ri41', 'androidaaa', '2021-03-18 12:29:31', 4),
(9, 141, 205, '2-03oe-owqld01', 'tvos', '2021-03-18 12:29:31', 4),
(10, 141, 220, 'r3-4r0oefrw8u8u1', 'ios', '2021-03-18 12:29:31', 4),
(11, 141, 205, '023or2u9u-3ouu1', 'samsungorsay', '2021-03-18 12:29:31', 4),
(12, 141, 220, '20-3o-23or-01', 'samsungtizen', '2021-03-18 12:29:31', 4),
(13, 141, 205, '-283u9e3e1', 'lg', '2021-03-18 12:29:31', 4),
(14, 141, 220, '98wu43r_', 'androidtv', '2021-03-18 12:29:31', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `devices_tmp_upload`
--
ALTER TABLE `devices_tmp_upload`
  ADD PRIMARY KEY (`tmp_device_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `devices_tmp_upload`
--
ALTER TABLE `devices_tmp_upload`
  MODIFY `tmp_device_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

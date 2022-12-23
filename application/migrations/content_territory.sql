-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2021 at 03:21 PM
-- Server version: 8.0.12
-- PHP Version: 7.2.10

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
-- Table structure for table `content_territory`
--

CREATE TABLE `content_territory` (
  `territory_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `VAT` float(5,2) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `archived` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `content_territory`
--

INSERT INTO `content_territory` (`territory_id`, `account_id`, `country`, `code`, `VAT`, `created_by`, `date_created`, `modified_by`, `date_modified`, `active`, `archived`) VALUES
(1, 1, 'Albania', 'AL', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(2, 1, 'Argentina', 'AR', 1.21, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(3, 1, 'Australia', 'AU', 1.10, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(4, 1, 'Austria', 'AT', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(5, 1, 'Bahrain', 'BH', 1.05, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(6, 1, 'Belarus', 'BY', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(7, 1, 'Belgium', 'BE', 1.21, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(8, 1, 'Bolivia', 'BO', 1.13, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(9, 1, 'Bosnia and Herzegovina', 'BA', 1.17, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(10, 1, 'Brazil', 'BR', 1.25, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(11, 1, 'Bulgaria', 'BG', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(12, 1, 'Canada', 'CA', 1.13, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(13, 1, 'Chile', 'CL', 1.19, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(14, 1, 'China', 'CN', 1.13, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(15, 1, 'Colombia', 'CO', 1.19, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(16, 1, 'Croatia', 'HR', 1.25, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(17, 1, 'Cyprus', 'CY', 1.19, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(18, 1, 'Czech Republic', 'CZ', 1.21, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(19, 1, 'Denmark', 'DK', 1.25, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(20, 1, 'Dubai', 'DU', 1.05, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(21, 1, 'Ecuador', 'EC', 1.12, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(22, 1, 'Egypt', 'EG', 1.14, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(23, 1, 'Estonia', 'EE', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(24, 1, 'Finland', 'FI', 1.24, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(25, 1, 'France', 'FR', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(26, 1, 'Germany', 'DE', 1.19, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(27, 1, 'Greece', 'GR', 1.23, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(28, 1, 'Guatamala', 'GT', 1.12, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(29, 1, 'Hong Kong', 'HK', 1.15, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(30, 1, 'Hungary', 'HU', 1.27, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(31, 1, 'Iceland', 'IS', 1.24, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(32, 1, 'India', 'IN', 1.18, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(33, 1, 'Indonesia', 'ID', 1.10, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(34, 1, 'Ireland', 'IE', 1.23, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(36, 1, 'Italy', 'IT', 1.22, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(37, 1, 'Japan', 'JP', 1.10, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(38, 1, 'Jordan', 'JO', 1.16, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(39, 1, 'Kazakhstan', 'KZ', 1.12, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(40, 1, 'Kenya', 'KE', 1.16, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(41, 1, 'Korea North', 'KP', 1.00, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(42, 1, 'South Korea', 'KR', 1.10, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(44, 1, 'Latvia', 'LV', 1.21, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(45, 1, 'Lebanon', 'LB', 1.11, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(46, 1, 'Lithuania', 'LT', 1.21, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(48, 1, 'Malaysia', 'MY', 1.06, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(49, 1, 'Mexico', 'MX', 1.16, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(50, 1, 'Montenegro', 'ME', 1.19, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(51, 1, 'Netherlands', 'NL', 1.21, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(52, 1, 'New Zealand', 'NZ', 1.15, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(53, 1, 'Norway', 'NO', 1.25, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(55, 1, 'Pakistan', 'PK', 1.17, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(56, 1, 'Panama', 'PA', 1.07, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(57, 1, 'Paraguay', 'PY', 1.10, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(58, 1, 'Peru', 'PE', 1.18, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(59, 1, 'Philippines', 'PH', 1.12, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(60, 1, 'Poland', 'PL', 1.23, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(61, 1, 'Portugal', 'PT', 1.23, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(62, 1, 'Qatar', 'QA', 1.05, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(63, 1, 'Romania', 'RO', 1.19, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(64, 1, 'Russia', 'RU', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(65, 1, 'Saudi Arabia', 'SA', 1.15, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(66, 1, 'Serbia', 'CS', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(67, 1, 'Singapore', 'SG', 1.09, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(68, 1, 'Slovakia', 'SK', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(69, 1, 'Slovenia', 'SI', 1.22, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(70, 1, 'South Africa KR', 'KR', 1.15, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(71, 1, 'South Africa', 'ZA', 1.15, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(72, 1, 'Spain', 'ES', 1.21, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(74, 1, 'Switzerland, French', 'CH', 1.08, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(75, 1, 'Switzerland, German', 'CH', 1.08, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(76, 1, 'Switzerland, Italian', 'CH', 1.08, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(77, 1, 'Taiwan', 'TW', 1.05, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(78, 1, 'Thailand', 'TH', 1.10, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(79, 1, 'Trinidad', 'TT', 1.13, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(80, 1, 'Turkey', 'TR', 1.18, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(81, 1, 'UAE', 'AE', 1.05, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(82, 1, 'Ukraine', 'UA', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(83, 1, 'United Kingdom', 'UK', 1.20, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(84, 1, 'Uruguay', 'UY', 1.22, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(85, 1, 'USA', 'US', 1.10, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(86, 1, 'Venezuela', 'VE', 1.16, 1, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(87, 1, 'Vietnam', 'VN', 1.10, 1, '2021-03-11 11:11:11', NULL, NULL, 0, 1),
(89, 1, 'Belize', 'BZ', 1.13, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(90, 1, 'Anguilla', 'AI', 1.00, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(91, 1, 'Seychelles', 'SC', 1.15, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(92, 1, 'Kuwait', 'KW', 1.05, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(93, 1, 'Malawi', 'MW', 1.17, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(94, 1, 'Ghana', 'GH', 1.13, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(95, 1, 'Israel', 'IL', 1.17, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(96, 1, 'British Virgin Islands', 'VG', 1.00, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(97, 1, 'Oman', 'OM', 1.05, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(98, 1, 'Macau', 'MO', 1.00, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(99, 1, 'Monaco', 'MC', 1.20, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(100, 1, 'Sweden', 'SE', 1.25, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(101, 1, 'Maldives', 'MV', 1.06, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(102, 1, 'French Polynesia', 'PF', 1.16, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(103, 1, 'Luxembourg', 'LU', 1.17, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0),
(104, 1, 'Mauritius', 'MU', 1.15, 4, '2021-03-11 11:11:11', NULL, NULL, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `content_territory`
--
ALTER TABLE `content_territory`
  ADD PRIMARY KEY (`territory_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `content_territory`
--
ALTER TABLE `content_territory`
  MODIFY `territory_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

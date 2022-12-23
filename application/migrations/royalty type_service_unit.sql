CREATE TABLE `royalty_type` (
  `type_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `type_name` varchar(256) NOT NULL,
  `type_group` varchar(256) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT = COMPACT;

INSERT INTO `royalty_type` (`type_id`, `account_id`, `type_name`, `type_group`, `created_by`, `created_date`, `active`, `archived`) VALUES
(1, 1, 'Minimum Guarantee', 'minimum_guarantee', 1, '2020-12-09 15:47:49', 1, NULL),
(2, 1, 'Revenue Share', 'revenue_share', 1, '2020-12-09 15:47:49', 1, NULL);

ALTER TABLE `royalty_type`
  ADD PRIMARY KEY (`type_id`);

ALTER TABLE `royalty_type`
  MODIFY `type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
 


CREATE TABLE `royalty_service` (
  `service_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `service_name` varchar(256) NOT NULL,
  `service_group` varchar(256) NOT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT = COMPACT;

INSERT INTO `royalty_service` (`service_id`, `account_id`, `service_name`, `service_group`, `created_by`, `created_date`, `active`, `archived`) VALUES
(1, 1, 'Airtime', 'airtime', 1, '2020-12-09 15:49:28', 1, NULL),
(2, 1, 'Regular Server', 'regular_server', 1, '2020-12-09 15:49:28', 1, NULL),
(3, 1, 'Regular Server (Nominated)', 'regular_server_nominated', 1, '2020-12-09 15:49:28', NULL, 1),
(4, 1, 'Other', 'other', 1, '2020-12-09 15:50:16', 1, NULL);

ALTER TABLE `royalty_service`
  ADD PRIMARY KEY (`service_id`);

ALTER TABLE `royalty_service`
  MODIFY `service_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
  
  
CREATE TABLE `royalty_unit` (
  `unit_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `unit_name` varchar(256) NOT NULL,
  `unit_group` varchar(256) NOT NULL,
  `unit_type` varchar(256) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `archived` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT = COMPACT;


INSERT INTO `royalty_unit` (`unit_id`, `account_id`, `unit_name`, `unit_group`, `unit_type`, `created_by`, `created_date`, `active`, `archived`) VALUES
(1, 1, 'p.p.r.p.d.', 'pprpd', 'currency', 1, NULL, 1, NULL),
(2, 1, '%', 'percentage', 'percentage', 1, NULL, 1, NULL);

ALTER TABLE `royalty_unit`
  ADD PRIMARY KEY (`unit_id`);

ALTER TABLE `royalty_unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
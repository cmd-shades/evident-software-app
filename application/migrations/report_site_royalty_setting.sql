CREATE TABLE `report_site_royalty_setting` (
  `setting_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `site_id` int(11) UNSIGNED NOT NULL,
  `provider_id` int(11) UNSIGNED DEFAULT NULL,
  `royalty_type_id` int(11) UNSIGNED DEFAULT NULL,
  `royalty_service_id` int(11) UNSIGNED DEFAULT NULL,
  `royalty_unit_id` int(11) UNSIGNED DEFAULT NULL,
  `report_setting_id` int(11) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `last_modified_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT = COMPACT;

ALTER TABLE `report_site_royalty_setting`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `Account, Site and Provider` (`account_id`,`site_id`,`provider_id`);
  
ALTER TABLE `report_site_royalty_setting`
  MODIFY `setting_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  
  
  
  CREATE TABLE `royalty_setting_combined` (
  `combination_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `provider_id` int(11) UNSIGNED NOT NULL,
  `royalty_type_id` int(11) UNSIGNED NOT NULL,
  `royalty_service_id` int(11) UNSIGNED NOT NULL,
  `royalty_unit_id` int(11) UNSIGNED NOT NULL,
  `setting_royalty_id` int(11) UNSIGNED NOT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT = COMPACT;


INSERT INTO `royalty_setting_combined` (`combination_id`, `account_id`, `provider_id`, `royalty_type_id`, `royalty_service_id`, `royalty_unit_id`, `setting_royalty_id`, `created_by`, `created_date`, `active`, `archived`) VALUES
(1, 1, 2, 1, 2, 1, 1, 1, '2020-12-11 11:16:59', 1, NULL),
(2, 1, 2, 2, 2, 2, 2, 1, '2020-12-13 20:32:10', 1, NULL),
(3, 1, 2, 2, 3, 2, 3, 1, '2020-12-13 20:36:27', NULL, 1),
(4, 1, 2, 2, 1, 2, 4, 1, '2020-12-13 20:36:27', 1, NULL),
(5, 1, 2, 1, 1, 1, 5, 1, '2020-12-13 20:40:19', 1, NULL),
(6, 1, 2, 2, 4, 2, 24, 1, '2020-12-13 20:40:19', 1, NULL),
(7, 1, 2, 1, 4, 1, 25, 1, '2020-12-13 20:40:48', 1, NULL);

ALTER TABLE `royalty_setting_combined`
  ADD PRIMARY KEY (`combination_id`);
  
ALTER TABLE `royalty_setting_combined`
  MODIFY `combination_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

CREATE TABLE `report_type` (
  `type_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_alt_title` varchar(255) DEFAULT NULL,
  `type_group` varchar(255) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `last_modified_by` int(11) UNSIGNED DEFAULT NULL,
  `last_modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_visible` tinyint(1) UNSIGNED DEFAULT 1,
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `report_type` (`type_id`, `account_id`, `category_id`, `type_name`, `type_alt_title`, `type_group`, `created_by`, `created_date`, `last_modified_by`, `last_modified_date`, `is_visible`, `active`, `archived`) VALUES
(1, 1, 1, 'Royalty Report by the provider', 'Royalty Report  by the provider type', 'royalty_report_by_the_provider', 2, '2020-08-13 13:14:26', 0, '2020-08-13 13:16:40', 1, 1, NULL),
(2, 1, 2, 'Provider Asset Clearance', 'Provider Asset Clearance', 'provider_asset_clearance', 1, '2020-08-13 14:09:30', 0, '2020-12-14 19:47:46', 1, 1, NULL),
(3, 1, 2, 'Last 12 months Releases by Territory', 'Last 12 months Releases by Territory', 'last_12_months_releases_by_territory', 1, '2020-08-13 14:11:30', 0, NULL, 1, 1, NULL),
(4, 1, 2, 'All Releases by Territory', 'All Releases by Territory', 'all_releases_by_territory', 1, '2020-12-14 19:26:14', 0, NULL, 1, 1, NULL),
(5, 1, 2, 'Assets Currently In use', 'Assets Currently In use', 'assets_currently_in_use', 1, '2020-08-13 14:16:03', 0, NULL, 1, 1, NULL),
(6, 1, 2, 'Assets Not in use', 'Assets Not in use', 'assets_not_in_use', 1, '2020-08-13 14:17:18', 0, NULL, 1, 1, NULL),
(7, 1, 2, 'Contract Status Integrator', 'Contract Status Integrator', 'contract_status_integrator', 1, '2020-08-13 14:44:30', 1, '2020-12-14 19:48:18', 1, 1, NULL),
(8, 1, 2, 'Contract Status Site', 'Contract Status Site', 'contract_status_site', 1, '2020-08-13 14:44:30', 2, NULL, 1, 1, NULL),
(9, 1, 2, 'Live products', 'Live Products', 'live_products', 1, '2020-08-13 14:44:30', 3, NULL, 1, 1, NULL),
(10, 1, 2, 'No of Expired Products', 'No of Expired Products', 'no_of_expired_products', 1, '2020-12-14 19:44:31', 0, '2020-12-14 19:47:22', 1, 1, NULL),
(11, 1, 2, 'Invoicing', 'Invoicing', 'invoicing', 1, '2020-08-13 14:44:30', 9, '2020-12-14 19:47:18', 1, 1, NULL),
(12, 1, 2, 'Stalled Distributions', 'Stalled Distributions', 'stalled_distributions', 1, '2020-08-13 14:44:30', 5, NULL, 1, NULL, 1),
(13, 1, 2, 'Active Distributions', 'Active Distributions', 'active_distributions', 1, '2020-08-13 14:44:30', 6, NULL, 1, NULL, 1),
(14, 1, 2, 'Completed Distributions', 'Completed Distributions', 'completed_distributions', 1, '2020-08-13 14:44:30', 7, NULL, 1, NULL, 1),
(15, 1, 2, 'Airtime Stats', 'Airtime Stats', 'airtime_stats', 1, '2020-08-13 14:44:30', 8, NULL, 1, NULL, 1),
(16, 1, 2, 'Closed Sites', 'Closed Sites', 'closed_sites', 1, '2020-08-13 14:44:30', 4, NULL, 1, NULL, 1),
(17, 1, 2, 'Language support', 'Language support', 'language_support', 1, '2020-08-13 14:14:38', 0, NULL, 1, NULL, 1),
(18, 1, 2, 'UIP Nominated Films', 'UIP Nominated Films', 'uip_nominated_films', 1, '2020-08-13 14:10:16', 0, '2020-12-14 19:47:04', 1, NULL, 1);

ALTER TABLE `report_type`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_group` (`account_id`,`type_group`) USING BTREE;

ALTER TABLE `report_type`
  MODIFY `type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
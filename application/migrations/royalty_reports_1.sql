/*

## !!!!  CHECK IF NEEDED !!!!

CREATE TABLE `report_viewing_stats_uploads` (
  `document_id` bigint(14) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `provider_id` int(11) UNSIGNED DEFAULT NULL,
  `report_category_id` int(11) UNSIGNED DEFAULT NULL,
  `report_type_id` int(11) UNSIGNED DEFAULT NULL,
  `month` int(4) UNSIGNED DEFAULT NULL,
  `year` int(11) UNSIGNED DEFAULT NULL,
  `doc_type` varchar(255) DEFAULT NULL,
  `doc_reference` varchar(255) DEFAULT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `document_location` varchar(255) DEFAULT NULL,
  `document_link` varchar(255) DEFAULT NULL,
  `document_extension` varchar(25) DEFAULT NULL,
  `upload_segment` varchar(50) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `last_modified_by` int(11) UNSIGNED DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `report_viewing_stats_uploads`
  ADD PRIMARY KEY (`document_id`);

ALTER TABLE `report_viewing_stats_uploads`
  MODIFY `document_id` bigint(14) UNSIGNED NOT NULL AUTO_INCREMENT;


## OR JUST DO THE UPDATE OF THE TABLE:

*/

ALTER TABLE `report_viewing_stats_uploads` ADD `report_category_id` INT(11) UNSIGNED NULL AFTER `provider_id`, ADD `report_type_id` INT(11) UNSIGNED NULL AFTER `report_category_id`, ADD `month` INT(4) UNSIGNED NULL AFTER `report_type_id`, ADD `year` INT(11) UNSIGNED NULL AFTER `month`;

/* ##  `site_reporting_window_month` */

CREATE TABLE `site_reporting_window_month` (
  `window_month_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `site_id` int(11) UNSIGNED DEFAULT NULL,
  `month_id` int(11) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `site_reporting_window_month`
  ADD PRIMARY KEY (`window_month_id`);

ALTER TABLE `site_reporting_window_month`
  MODIFY `window_month_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

/* ##  `site_reporting_window_month` - END */

/* ##  `report_category` */
CREATE TABLE `report_category` (
  `category_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_group` varchar(255) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `last_modified_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `report_category` (`category_id`, `account_id`, `category_name`, `category_group`, `created_by`, `created_date`, `last_modified_by`, `modified_date`, `active`, `archived`) VALUES
(1, 1, 'Royalty Report', 'royalty_report', 1, '2020-08-13 13:35:30', NULL, NULL, 1, NULL),
(2, 1, 'Basic Report', 'basic_report', 1, '2020-08-13 13:35:30', NULL, NULL, 1, NULL);

ALTER TABLE `report_category`
  ADD PRIMARY KEY (`category_id`);

ALTER TABLE `report_category`
  MODIFY `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

/* ##  `report_category` - END */



/* ##  `report_type` */
CREATE TABLE `report_type` (
  `type_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_alt_title` varchar(255) DEFAULT NULL,
  `type_group` varchar(255) DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT 0,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `last_modified_by` int(11) UNSIGNED DEFAULT 0,
  `last_modified` timestamp NULL DEFAULT NULL,
  `is_visible` tinyint(1) UNSIGNED DEFAULT 1,
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `report_type` (`type_id`, `account_id`, `category_id`, `type_name`, `type_alt_title`, `type_group`, `created_by`, `created_date`, `last_modified_by`, `last_modified`, `is_visible`, `active`, `archived`) VALUES
(1, 1, 1, 'Royalty Report by the provider', 'Royalty Report  by the provider type', 'royalty_report_by_the_provider', 2, '2020-08-13 13:14:26', 0, '2020-08-13 13:16:40', 1, 1, 0),
(2, 1, 2, 'Provider Asset Clearances', 'Provider Asset Clearances', 'provider_asset_clearances', 1, '2020-08-13 14:09:30', 0, NULL, 1, 1, 0),
(3, 1, 2, 'UIP Nominated Films', 'UIP Nominated Films', 'uip_nominated_films', 1, '2020-08-13 14:10:16', 0, NULL, 1, 1, 0),
(4, 1, 2, 'Current Release by Territory', 'Current Release by Territory', 'current_release_by_territory', 1, '2020-08-13 14:11:30', 0, NULL, 1, 1, 0),
(5, 1, 2, 'Language support', 'Language support', 'language_support', 1, '2020-08-13 14:14:38', 0, NULL, 1, 1, 0),
(6, 1, 2, 'Assets Currently In use', 'Assets Currently In use', 'assets_currently_in_use', 1, '2020-08-13 14:16:03', 0, NULL, 1, 1, 0),
(7, 1, 2, 'Assets Not in use', 'Assets Not in use', 'assets_not_in_use', 1, '2020-08-13 14:17:18', 0, NULL, 1, 1, 0),
(8, 1, 2, 'Contract Status Partner', 'Contract Status Partner', 'contract_status_partner', 1, '2020-08-13 14:44:30', 1, NULL, 1, 1, 0),
(9, 1, 2, 'Contract Status Site', 'Contract Status Site', 'contract_status_site', 1, '2020-08-13 14:44:30', 2, NULL, 1, 1, 0),
(10, 1, 2, 'Live Sites', 'Live Sites', 'live_sites', 1, '2020-08-13 14:44:30', 3, NULL, 1, 1, 0),
(11, 1, 2, 'Closed Sites', 'Closed Sites', 'closed_sites', 1, '2020-08-13 14:44:30', 4, NULL, 1, 1, 0),
(12, 1, 2, 'Stalled Distributions', 'Stalled Distributions', 'stalled_distributions', 1, '2020-08-13 14:44:30', 5, NULL, 1, 1, 0),
(13, 1, 2, 'Active Distributions', 'Active Distributions', 'active_distributions', 1, '2020-08-13 14:44:30', 6, NULL, 1, 1, 0),
(14, 1, 2, 'Completed Distributions', 'Completed Distributions', 'completed_distributions', 1, '2020-08-13 14:44:30', 7, NULL, 1, 1, 0),
(15, 1, 2, 'Airtime Stats', 'Airtime Stats', 'airtime_stats', 1, '2020-08-13 14:44:30', 8, NULL, 1, 1, 0),
(16, 1, 2, 'Invoicing', 'Invoicing', 'invoicing', 1, '2020-08-13 14:44:30', 9, NULL, 1, 1, 0);

ALTER TABLE `report_type`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_group` (`account_id`,`type_group`) USING BTREE;

ALTER TABLE `report_type`
  MODIFY `type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
  
  /* ##  `report_type` - END */
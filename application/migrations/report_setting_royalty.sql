CREATE TABLE `report_setting_royalty` (
  `setting_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `report_category_id` int(11) UNSIGNED NOT NULL,
  `report_type_id` int(11) UNSIGNED NOT NULL,
  `provider_id` int(11) UNSIGNED NOT NULL,
  `setting_name` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `setting_name_group` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NULL',
  `currency` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit` enum('percentage','currency','advanced') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `other_info` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) UNSIGNED DEFAULT '1',
  `archived` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

INSERT INTO `report_setting_royalty` (`setting_id`, `account_id`, `report_category_id`, `report_type_id`, `provider_id`, `setting_name`, `setting_name_group`, `setting_value`, `currency`, `unit`, `other_info`, `created_by`, `created_date`, `modified_by`, `modified_date`, `active`, `archived`) VALUES
(1, 1, 1, 1, 2, 'Minimum Guarantee Regular Server', '1_1_uip_minimum_guarantee_regular_server', '3.20', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-08 14:20:54', NULL, '2020-12-08 10:34:19', 1, NULL),
(2, 1, 1, 1, 2, 'Revenue Share Regular Server', '1_1_uip_revenue_share_regular_server', '40', '%', 'percentage', '%', 4, '2020-09-08 14:20:54', NULL, '2020-12-07 17:50:04', 1, NULL),
(3, 1, 1, 1, 2, 'Revenue Share Regular Server (Nominated)', '1_1_uip_revenue_share_regular_server_nominated', '50', '%', 'percentage', '%', 4, '2020-09-08 14:20:54', NULL, '2020-12-07 17:50:04', 1, NULL),
(4, 1, 1, 1, 2, 'Revenue Share Airtime', '1_1_uip_revenue_share_airtime', '50', '%', 'percentage', '%', 4, '2020-09-08 14:23:22', NULL, '2020-12-07 17:50:04', 1, NULL),
(5, 1, 1, 1, 2, 'Minimum Guarantee Airtime ', '1_1_uip_minimum_guarantee_airtime', '4.17', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-08 14:24:38', NULL, '2020-12-08 10:34:19', 1, NULL),
(6, 1, 1, 1, 1, 'PPV Regular Server', '1_1_disney_ppv_regular_server', '100', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-08 14:24:38', NULL, '2020-12-08 10:36:27', 1, NULL),
(7, 1, 1, 1, 1, 'FTG Regular Server', '1_1_disney_ftg_regular_server', '200', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-08 14:24:38', NULL, '2020-12-08 10:36:43', 1, NULL),
(8, 1, 1, 1, 1, 'FTG Airtime', '1_1_disney_ftg_airtime', '100', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 07:58:13', NULL, '2020-12-08 10:36:52', 1, NULL),
(9, 1, 1, 1, 1, 'Revenue Share Airtime', '1_1_disney_revenue_share_airtime', '50', '%', 'percentage', '%', 4, '2020-09-09 07:58:13', NULL, '2020-12-04 16:55:19', 1, NULL),
(10, 1, 1, 1, 13, 'Revenue Share Airtime', '1_1_fox_revenue_share_airtime', '50', '%', 'percentage', '%', 4, '2020-09-09 07:58:01', NULL, '2020-12-04 17:06:03', 1, NULL),
(11, 1, 1, 1, 13, 'FTG Airtime', '1_1_fox_ftg_airtime', '100', 'USD', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 07:58:13', NULL, '2020-12-08 10:39:13', 1, NULL),
(12, 1, 1, 1, 13, 'FTG Regular Server', '1_1_fox_ftg_regular_server', '100', 'USD', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:23:41', NULL, '2020-12-08 10:39:10', 1, NULL),
(13, 1, 1, 1, 13, 'PPV Regular Server', '1_1_fox_ppv_regular_server', '40', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 07:58:10', NULL, '2020-12-08 10:39:05', 1, NULL),
(14, 1, 1, 1, 4, 'Minimum Guarantee 1-1000', '1_1_mindgeek_minimum_guarantee_1_1000', '40', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:37:55', 1, NULL),
(15, 1, 1, 1, 4, 'Minimum Guarantee 1001-2000', '1_1_mindgeek_minimum_guarantee_1001_2000', '35', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:37:58', 1, NULL),
(16, 1, 1, 1, 4, 'Minimum Guarantee 2001-5000', '1_1_mindgeek_minimum_guarantee_2001_5000', '30', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:38:01', 1, NULL),
(17, 1, 1, 1, 4, 'Minimum Guarantee 5001-10000', '1_1_mindgeek_minimum_guarantee_5001_10000', '27', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:38:03', 1, NULL),
(18, 1, 1, 1, 4, 'Minimum Guarantee 10001-20000', '1_1_mindgeek_minimum_guarantee_10001_20000', '23', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:38:05', 1, NULL),
(19, 1, 1, 1, 4, 'Minimum Guarantee 20000>', '1_1_mindgeek_minimum_guarantee_20000_above', '20', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:38:08', 1, NULL),
(20, 1, 1, 1, 4, 'Airtime Rev Share ', '1_1_mindgeek_airtime_revenue_share', '70%', '%', 'advanced', '70% 0f (Net-25%)', 4, '2020-09-09 08:25:35', NULL, '2020-12-04 16:59:31', 1, NULL),
(21, 1, 1, 1, 9, 'Minimum Guarantee', '1_1_cnn_minimum_guarantee', '110', 'USD', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:38:27', 1, NULL),
(22, 1, 1, 1, 12, 'Minimum Guarantee Al Jazeera (Arabic)', '1_1_al_jazeera_minimum_guarantee_al_jazeera_arabic', '39.00', 'USD', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:34:19', 1, NULL),
(23, 1, 1, 1, 12, 'Minimum Guarantee Al Jazeera (English)', '1_1_al_jazeera_minimum_guarantee_al_jazeera_english', '22.75', 'USD', 'currency', 'p.p.r.p.m.', 4, '2020-09-09 08:25:35', NULL, '2020-12-08 10:34:19', 1, NULL),
(24, 1, 1, 1, 2, 'Revenue Share Other', '1_1_uip_revenue_share_other', '', '%', 'percentage', '%', 4, '2020-12-04 16:16:02', NULL, '2020-12-07 17:50:04', 1, NULL),
(25, 1, 1, 1, 2, 'Minimum Guarantee Other', '1_1_uip_minimum_guarantee_other', '', 'GBP', 'currency', 'p.p.r.p.m.', 4, '2020-12-04 16:16:02', NULL, '2020-12-08 10:34:19', 1, NULL);

ALTER TABLE `report_setting_royalty`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_name_group` (`setting_name_group`);

ALTER TABLE `report_setting_royalty`
  MODIFY `setting_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
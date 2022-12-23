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
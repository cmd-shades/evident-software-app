CREATE TABLE `availability_window` (
  `window_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `easel_id` varchar(255) DEFAULT NULL,
  `easel_productId` varchar(255) DEFAULT NULL,
  `easel_visibleFrom` varchar(255) DEFAULT NULL,
  `easel_visibleTo` varchar(255) DEFAULT NULL,
  `easel_priceBandId` varchar(255) DEFAULT NULL,
  `easel_billing_category` varchar(255) DEFAULT NULL,
  `easel_billing_revenueShare` int(11) UNSIGNED DEFAULT NULL,
  `easel_billing_wholesalePrice` int(11) UNSIGNED DEFAULT NULL,
  `site_id` int(11) UNSIGNED DEFAULT NULL,
  `product_id` int(11) UNSIGNED DEFAULT NULL,
  `product_price_plan_id` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Indexes for table `availability_window`
--
ALTER TABLE `availability_window`
  ADD PRIMARY KEY (`window_id`);


--
-- AUTO_INCREMENT for table `availability_window`
--
ALTER TABLE `availability_window`
  MODIFY `window_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
  
  
  CREATE TABLE `tmp_product_debugging` (
  `entry_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED DEFAULT NULL,
  `price_plan_id` int(11) DEFAULT NULL,
  `string_name` varchar(256) DEFAULT NULL,
  `query_string` text DEFAULT NULL,
  `created_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `tmp_product_debugging`
  ADD PRIMARY KEY (`entry_id`);

ALTER TABLE `tmp_product_debugging`
  MODIFY `entry_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;



ALTER TABLE `product_price_plan` ADD `easel_price_band_ref` VARCHAR(255) NOT NULL AFTER `provider_id`;

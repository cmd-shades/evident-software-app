ALTER TABLE `product` ADD `airtime_market_ref` VARCHAR(255) NULL AFTER `product_description`;

CREATE TABLE `segment` (
  `segment_id` int(11) UNSIGNED NOT NULL,
  `segment_name` varchar(255) NOT NULL,
  `easel_reference_id` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `pin` int(11) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `active` tinyint(1) UNSIGNED DEFAULT 1,
  `archived` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `segment` ADD `product_id` INT(11) UNSIGNED NULL AFTER `segment_name`;

ALTER TABLE `segment`
  ADD PRIMARY KEY (`segment_id`);

ALTER TABLE `segment`
  MODIFY `segment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `product` ADD `airtime_segment_ref` VARCHAR(255) NULL AFTER `product_description`;

ALTER TABLE `segment` CHANGE `easel_reference_id` `airtime_segment_ref` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

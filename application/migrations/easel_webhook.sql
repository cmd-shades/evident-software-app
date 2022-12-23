-- tmp_easel_webhook_debugging
CREATE TABLE `tmp_easel_webhook_debugging` (
  `request_id` int(11) UNSIGNED NOT NULL,
  `request_type` varchar(255) DEFAULT NULL,
  `request_data` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `tmp_easel_webhook_debugging`
  ADD PRIMARY KEY (`request_id`);

ALTER TABLE `tmp_easel_webhook_debugging`
  MODIFY `request_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
-- tmp_easel_webhook_debugging

-- content_film
ALTER TABLE `content_film` ADD `airtime_trailer_file_id` INT(11) UNSIGNED NULL COMMENT 'The ID of the file from decoded files table which is linked with the Airtime Product as a trailer (non feature)' AFTER `airtime_feature_file_id`;

-- content_decoded_file
ALTER TABLE `content_decoded_file` ADD `is_airtime_encoded` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'This is coming from Easel webhook' AFTER `airtime_reference`, ADD `airtime_encoded_status` VARCHAR(255) NULL COMMENT 'This is coming from Easel webhook' AFTER `is_airtime_encoded`, ADD `airtime_encoded_update_date` DATETIME NULL COMMENT 'This is coming from Easel webhook' AFTER `airtime_encoded_status`;
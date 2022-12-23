ALTER TABLE `content_decoded_file` ADD `airtime_reference_updating_date` DATETIME NULL COMMENT 'Date/time when the Coggins 2nd webhook returned the AT file reference and it has been correctly actioned' AFTER `airtime_reference`;


ALTER TABLE `aws_bundle`
  DROP `aws_service`,
  DROP `aws_state_uploaded`,
  DROP `aws_state_start`,
  DROP `aws_state_end`,
  DROP `aws_uid`;

ALTER TABLE `aws_bundle` ADD `aws_state_destination` VARCHAR(255) NULL AFTER `coggins_api_session_id`;

ALTER TABLE `aws_bundle` ADD `aws_state_timestamp` BIGINT UNSIGNED NULL AFTER `aws_state_progress`;

ALTER TABLE `aws_bundle` CHANGE `created_by` `created_by` INT(11) UNSIGNED NULL;

ALTER TABLE `aws_bundle` CHANGE `aws_status` `aws_status` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `aws_state_progress`, CHANGE `created_by` `created_by` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `aws_status`

ALTER TABLE `aws_bundle` CHANGE `aws_state_timestamp` `aws_state_timestamp` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `aws_status`

ALTER TABLE `aws_bundle` ADD `aws_state_size` BIGINT UNSIGNED NULL AFTER `aws_state_progress`;

INSERT INTO `aws_bundle_content` (`aws_content_id`, `account_id`, `aws_bundle_id`, `asset_code`, `provider_id`, `content_id`, `file_cacti_id`, `file_name`, `file_type`, `aws_state_errors`, `aws_state_progress`, `aws_state_uploaded`, `aws_status`, `last_aws_update`, `created_by`, `created_date`, `modified_by`, `last_modified_date`, `active`, `archived`) VALUES (NULL, '1', '452', 'crawl', '2', '81', '572', 'crawl_zh.vtt', 'subtitles', NULL, NULL, NULL, NULL, NULL, '4', '2021-11-02 12:18:44', NULL, NULL, '1', '0'), (NULL, '1', '452', 'crawl', '2', '18', '566', 'Crawl-Standard.jpg', 'standard', NULL, NULL, NULL, NULL, NULL, '4', '2021-11-02 12:18:44', NULL, NULL, '1', '0'), (NULL, '1', '452', 'crawl', '2', '18', '565', 'Crawl-Hero.jpg', 'hero', NULL, NULL, NULL, NULL, NULL, '4', '2021-11-02 12:18:44', NULL, NULL, '1', '0'), (NULL, '1', '452', 'crawl', '2', '18', '131', 'crawl_en_fr_de_it_es_ru_ars_nls_tus_pts_zhs_pls_ts.mpg', 'movie', NULL, NULL, NULL, NULL, NULL, '4', '2021-11-02 12:18:44', NULL, NULL, '1', '0')


ALTER TABLE `content_decoded_file` CHANGE `is_on_aws` `is_on_aws` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'This value comes from AWS Webhook (Coggins)', CHANGE `aws_status` `aws_status` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'This value comes from AWS Webhook (Coggins)', CHANGE `aws_uploading_date` `aws_uploading_date` DATETIME NULL DEFAULT NULL COMMENT 'This value comes from AWS Webhook (Coggins)';
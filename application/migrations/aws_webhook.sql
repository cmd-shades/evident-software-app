-- The database reset for webhooks:
update `aws_bundle_content` set aws_state_errors = NULL, aws_state_progress = null, aws_state_uploaded = NULL, aws_status = NULL WHERE `content_id` = 18
update aws_bundle set aws_service = NULL, aws_state_errors = NULL, aws_state_uploaded = NULL, aws_state_progress = NULL, aws_state_start = NULL, aws_state_end = NULL, aws_status = NULL, aws_uid = NULL
update `content_document_uploads` set is_on_aws = 0, aws_status = NULL WHERE `content_id` = 18
update `content_decoded_file` set is_on_aws = 0, aws_status = NULL WHERE `content_id` = 18
TRUNCATE TABLE `tmp_aws_debugging`




-- tables:

CREATE TABLE `tmp_aws_debugging` (
  `entry_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `aws_bundle_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `provided_data` text DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `tmp_aws_debugging`
  ADD PRIMARY KEY (`entry_id`);

ALTER TABLE `tmp_aws_debugging`
  MODIFY `entry_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


-----------------------------------


CREATE TABLE `aws_bundle_content` (
  `aws_content_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` int(11) UNSIGNED NOT NULL,
  `aws_bundle_id` int(11) UNSIGNED NOT NULL,
  `asset_code` varchar(255) DEFAULT NULL,
  `provider_id` int(11) UNSIGNED NOT NULL,
  `content_id` int(11) UNSIGNED NOT NULL,
  `file_cacti_id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `aws_state_errors` int(11) UNSIGNED DEFAULT NULL,
  `aws_state_progress` varchar(255) DEFAULT NULL,
  `aws_state_uploaded` varchar(255) DEFAULT NULL,
  `aws_status` varchar(255) DEFAULT NULL,
  `last_aws_update` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `last_modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `aws_bundle_content`
  ADD PRIMARY KEY (`aws_content_id`);

ALTER TABLE `aws_bundle_content`
  MODIFY `aws_content_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO `aws_bundle_content` (`aws_content_id`, `account_id`, `aws_bundle_id`, `asset_code`, `provider_id`, `content_id`, `file_cacti_id`, `file_name`, `file_type`, `aws_state_errors`, `aws_state_progress`, `aws_state_uploaded`, `aws_status`, `last_aws_update`, `created_by`, `created_date`, `modified_by`, `last_modified_date`, `active`, `archived`) VALUES
(NULL, 1, 17, 'therhythmsection', 2, 18, 30, '\\\\Ldtvdc1\\ldtv\\IT\\Websites\\Techlive\\CDS_PICKUP\\uip\\therhythmsection\\therhythmsection_en_fr_it_es_ru_ars_nls_tus_pts_zhs_pls_ts.mp4', 'movie', NULL, NULL, NULL, NULL, '2021-10-11 15:25:44', 4, '2021-09-29 15:05:53', NULL, '2021-10-11 14:47:53', 1, 0),
(NULL, 1, 17, 'therhythmsection', 2, 18, 4783, 'http://cacti.techlive.tv/techlive/_account_assets/accounts/1/content/18/therhythmsection-hero.jpg', 'hero', NULL, NULL, NULL, NULL, '2021-10-11 15:25:44', 4, '2021-09-29 15:05:53', NULL, '2021-10-11 14:47:53', 1, 0),
(NULL, 1, 17, 'therhythmsection', 2, 18, 269, 'http://cacti.techlive.tv/techlive/_account_assets/accounts/1/content/18/therhythmsection-standard.jpg', 'standard', NULL, NULL, NULL, NULL, '2021-10-11 15:25:44', 4, '2021-09-29 15:05:53', NULL, '2021-10-11 14:47:53', 1, 0),
(NULL, 1, 17, 'therhythmsection', 2, 18, 267, 'http://cacti.techlive.tv/techlive/_account_assets/accounts/1/content/18/therhythmsection_zh.vtt', 'subtitles', NULL, NULL, NULL, NULL, '2021-10-11 15:25:44', 4, '2021-09-29 15:05:53', NULL, '2021-10-11 14:47:53', 1, 0),
(NULL, 1, 17, 'therhythmsection', 2, 18, 266, 'http://cacti.techlive.tv/techlive/_account_assets/accounts/1/content/18/therhythmsection_tk.vtt', 'subtitles', NULL, NULL, NULL, NULL, '2021-10-11 15:25:44', 4, '2021-09-29 15:05:53', NULL, '2021-10-11 14:47:53', 1, 0),
(NULL, 1, 17, 'therhythmsection', 2, 18, 265, 'http://cacti.techlive.tv/techlive/_account_assets/accounts/1/content/18/therhythmsection_pt.vtt', 'subtitles', NULL, NULL, NULL, NULL, '2021-10-11 15:25:44', 4, '2021-09-29 15:05:53', NULL, '2021-10-11 14:47:53', 1, 0),
(NULL, 1, 17, 'therhythmsection', 2, 18, 264, 'http://cacti.techlive.tv/techlive/_account_assets/accounts/1/content/18/therhythmsection_pl.vtt', 'subtitles', NULL, NULL, NULL, NULL, '2021-10-11 15:25:44', 4, '2021-09-29 15:05:53', NULL, '2021-10-11 14:47:53', 1, 0),
(NULL, 1, 17, 'therhythmsection', 2, 18, 263, 'http://cacti.techlive.tv/techlive/_account_assets/accounts/1/content/18/therhythmsection_nl.vtt', 'subtitles', NULL, NULL, NULL, NULL, '2021-10-11 15:25:44', 4, '2021-09-29 15:05:53', NULL, '2021-10-11 14:47:53', 1, 0);


---------------------------

CREATE TABLE `aws_bundle` (
  `bundle_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `bundle_name` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `bucket_name` varchar(255) DEFAULT NULL,
  `coggins_api_message` varchar(255) DEFAULT NULL,
  `coggins_api_session_id` varchar(255) DEFAULT NULL,
  `aws_service` varchar(255) DEFAULT NULL,
  `aws_state_errors` int(11) DEFAULT NULL,
  `aws_state_progress` varchar(255) DEFAULT NULL,
  `aws_state_uploaded` varchar(255) DEFAULT NULL,
  `aws_state_start` datetime DEFAULT NULL,
  `aws_state_end` datetime DEFAULT NULL,
  `aws_status` varchar(256) DEFAULT NULL,
  `aws_uid` varchar(256) DEFAULT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `aws_bundle` (`bundle_id`, `account_id`, `bundle_name`, `status`, `bucket_name`, `coggins_api_message`, `coggins_api_session_id`, `aws_service`, `aws_state_errors`, `aws_state_progress`, `aws_state_uploaded`, `aws_state_start`, `aws_state_end`, `aws_status`, `aws_uid`, `created_by`, `created_date`, `modified_by`, `modified_date`, `active`, `archived`) VALUES
(17, 1, 'aws_bundle_20210929_160553', '', 'basilica', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, '2021-09-29 16:05:53', NULL, '2021-10-11 14:47:53', 1, 0);

ALTER TABLE `aws_bundle`
  ADD PRIMARY KEY (`bundle_id`);

ALTER TABLE `aws_bundle`
  MODIFY `bundle_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
  
  
  
----- other tables:
ALTER TABLE `content_document_uploads` ADD `aws_status` VARCHAR(255) NULL AFTER `is_on_aws`;
ALTER TABLE `content_decoded_file` ADD `aws_status` VARCHAR(255) NULL AFTER `is_on_aws`;


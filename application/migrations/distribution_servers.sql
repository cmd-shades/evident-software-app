CREATE TABLE `distribution_server` (
  `server_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `server_name` varchar(255) NOT NULL,
  `coggins_id` int(11) UNSIGNED NOT NULL,
  `coggins_type` varchar(255) DEFAULT NULL,
  `coggins_licence` varchar(255) DEFAULT NULL,
  `coggins_status` varchar(255) DEFAULT NULL,
  `coggins_created` varchar(255) DEFAULT NULL,
  `coggins_unlocked` varchar(255) DEFAULT NULL,
  `coggins_externalAccess` varchar(255) DEFAULT NULL,
  `coggins_running` varchar(255) DEFAULT NULL,
  `coggins_lastPollSeconds` bigint(13) UNSIGNED NOT NULL,
  `coggins_time` bigint(13) UNSIGNED NOT NULL,
  `coggins_units` varchar(255) DEFAULT NULL,
  `last_refreshed` datetime DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `is_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `distribution_server` (`server_id`, `account_id`, `server_name`, `coggins_id`, `coggins_type`, `coggins_licence`, `coggins_status`, `coggins_created`, `coggins_unlocked`, `coggins_externalAccess`, `coggins_running`, `coggins_lastPollSeconds`, `coggins_time`, `coggins_units`, `last_refreshed`, `description`, `is_active`, `created_date`, `created_by`, `archived`) VALUES
(1, 1, 'CentOS 5.7 Test', 293, 'user', '1924944c-8bf2-1031-8347-002618343157', 'deleted', '2013-10-21T22:41:25.000Z', 'no', 'no', 'offline', 228523311, 2645, 'days', '2021-05-10 00:00:00', 'Test obsoleted Server', 1, '2021-05-10 13:42:20', 4, 0),
(2, 1, 'CentOS 5.7 Test v2', 294, 'user', '1924944c-8bf2-1031-8347-002618343157-test', 'deleted', '2013-10-21T22:41:25.000Z', 'no', 'no', 'offline', 228523311, 2645, 'days', '2021-05-10 00:00:00', 'Test obsoleted Server 22', 1, '2021-05-10 13:42:20', 4, 0);

ALTER TABLE `distribution_server`
  ADD PRIMARY KEY (`server_id`);

ALTER TABLE `distribution_server`
  MODIFY `server_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


--


CREATE TABLE `distribution_server_notification_point` (
  `point_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `server_id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_full_name` varchar(255) DEFAULT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `distribution_server_notification_point` (`point_id`, `account_id`, `server_id`, `email`, `contact_full_name`, `created_by`, `created_date`, `active`, `archived`) VALUES
(1, 1, 1, 'wojciechcupa@evidentsoftware.co.uk', 'Wojciech Cupa', 4, '2021-05-11 10:22:08', 1, 0),
(2, 1, 1, 'wojciechcupa@lovedigitaltv.co.uk', 'Wojciech CUpa', 4, '2021-05-11 10:22:08', 1, 0);

ALTER TABLE `distribution_server_notification_point`
  ADD PRIMARY KEY (`point_id`);

ALTER TABLE `distribution_server_notification_point`
  MODIFY `point_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;



-- Adding User and Company parts
ALTER TABLE `distribution_server` ADD `coggins_companyId` INT UNSIGNED NULL AFTER `coggins_units`, ADD `coggins_companyName` VARCHAR(255) NULL AFTER `coggins_companyId`, ADD `coggins_userId` INT UNSIGNED NULL AFTER `coggins_companyName`, ADD `coggins_userName` VARCHAR(255) NULL AFTER `coggins_userId`;


ALTER TABLE `distribution_server` ADD `last_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `is_active`, ADD `modified_by` INT(11) UNSIGNED NULL AFTER `last_modified`;
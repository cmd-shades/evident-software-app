ALTER TABLE `premises_attributes`   
  CHANGE `premise_id` `premises_id` BIGINT(14) UNSIGNED NOT NULL;

ALTER TABLE `audit`   
  ADD COLUMN `premises_id` BIGINT(14) NULL AFTER `vehicle_reg`;

INSERT INTO `user_modules` (`module_name`, `module_controller`, `module_url_link`, `module_ranking`, `category_id`, `app_img_url`) VALUES ('Premises', 'premises', 'premises/premises', '23', '4', '\\assets\\images\\app-logos\\premises-manager.png'); 
INSERT INTO `user_module_items` (`module_item_id`, `module_item_tab`, `module_item_name`, `module_item_url_link`, `module_item_desc`, `module_item_icon_class`, `module_item_sort`, `is_active`, `module_id`, `mobile_visible`, `show_in_sidebar`) VALUES (NULL, 'details', 'Premises Details', '', 'Access to a premises details', '', '1', '1', '23', '1', '0'); 
UPDATE `user_module_items` SET `module_item_tab` = 'premises' , `module_item_name` = 'Premise' , `module_item_desc` = 'Access to the Site premises records', `is_active` = '0' WHERE `module_item_id` = '50'; 

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `premises` */

DROP TABLE IF EXISTS `premises`;

CREATE TABLE `premises` (
  `premises_id` bigint(14) unsigned NOT NULL AUTO_INCREMENT,
  `premises_type_id` int(11) NOT NULL,
  `premises_ref` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `premises_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `premises_notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `address_line1` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_line2` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_line3` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_town` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_county` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_postcode` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `archived` tinyint(1) DEFAULT '0',
  `site_id` bigint(14) unsigned DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `address_id` bigint(14) unsigned DEFAULT NULL,
  `account_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`premises_id`),
  KEY `site_id` (`site_id`),
  KEY `premises_address` (`address_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `premises`   
  ADD COLUMN `audit_result_status_id` INT(11) NULL AFTER `archived`;

/*Table structure for table `premises_attributes` */

DROP TABLE IF EXISTS `premises_attributes`;

CREATE TABLE `premises_attributes` (
  `id` bigint(14) unsigned NOT NULL AUTO_INCREMENT,
  `premises_id` bigint(14) unsigned NOT NULL,
  `attribute_id` int(11) unsigned NOT NULL,
  `attribute_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `attribute_value` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `premise_id` (`premises_id`),
  KEY `pattr_id` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Table structure for table `premises_document_uploads` */

DROP TABLE IF EXISTS `premises_document_uploads`;

CREATE TABLE `premises_document_uploads` (
  `document_id` bigint(14) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `doc_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `doc_reference` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `documnet_extension` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `upload_segment` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `job_id` bigint(14) DEFAULT NULL,
  `audit_id` bigint(14) DEFAULT NULL,
  `asset_id` bigint(14) DEFAULT NULL,
  `site_id` bigint(14) DEFAULT NULL,
  `premises_id` bigint(14) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_modified_by` int(11) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`document_id`),
  KEY `ACCOUNT_ID` (`account_id`),
  KEY `SITE_ID` (`site_id`),
  KEY `PANEL_ID` (`premises_id`),
  KEY `DEVICE_ID` (`asset_id`),
  KEY `AUDIT_ID` (`audit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


/*Table structure for table `premises_type_attributes` */

DROP TABLE IF EXISTS `premises_type_attributes`;

CREATE TABLE `premises_type_attributes` (
  `attribute_id` bigint(14) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `attribute_ref` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `response_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `response_type_alt` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `response_options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `response_group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `accepted_file_types` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_mandatory` tinyint(1) DEFAULT '1',
  `is_mobile_visible` tinyint(1) DEFAULT '1',
  `photo_required` tinyint(1) DEFAULT '0',
  `max_length` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_modified_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `premises_type_id` int(11) unsigned DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  PRIMARY KEY (`attribute_id`),
  KEY `paccount_id` (`account_id`),
  KEY `asset_type_id` (`premises_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Table structure for table `premises_types` */

DROP TABLE IF EXISTS `premises_types`;

CREATE TABLE `premises_types` (
  `premises_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned DEFAULT NULL,
  `premises_type` varchar(450) DEFAULT NULL,
  `premises_type_ref` varchar(450) DEFAULT NULL,
  `premises_group` varchar(75) DEFAULT NULL,
  `premises_type_desc` varchar(255) DEFAULT NULL,
  `is_subaddress_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `primary_attribute_id` int(11) unsigned DEFAULT NULL,
  `discipline_id` int(11) unsigned DEFAULT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_modified_by` int(11) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `archived` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`premises_type_id`),
  KEY `accountId` (`account_id`),
  KEY `isActive` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `audit_responses_premises` (
  `id` bigint(14) unsigned NOT NULL AUTO_INCREMENT,
  `audit_id` bigint(14) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question` varchar(255) DEFAULT NULL,
  `response` varchar(512) DEFAULT NULL,
  `response_extra` varchar(255) DEFAULT NULL,
  `response_has_defects` text,
  `response_defects_details` text,
  `raise_reactive_job` enum('Yes','No','N/A') DEFAULT 'No',
  `section` varchar(255) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `segment` varchar(150) DEFAULT NULL,
  `pass_value` varchar(50) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_id` (`audit_id`),
  KEY `questionId` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `premises_types`   
  ADD COLUMN `is_subaddress_required` TINYINT(1) DEFAULT 0  NULL AFTER `premises_type_desc`;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



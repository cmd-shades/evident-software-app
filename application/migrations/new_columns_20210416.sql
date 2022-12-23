ALTER TABLE `site` ADD `is_signed` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `operating_company_id`;

ALTER TABLE `integrator` ADD `is_signed` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `integrator_status`;
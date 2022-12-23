ALTER TABLE `distribution_bundles` CHANGE `send_status` `send_status` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'Planned';

ALTER TABLE `distribution_bundles` ADD `coggins_progress` VARCHAR(255) NULL AFTER `coggins_state`, ADD `coggins_errors` VARCHAR(255) NULL AFTER `coggins_progress`;
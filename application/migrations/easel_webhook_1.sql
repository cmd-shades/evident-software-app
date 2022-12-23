ALTER TABLE `content_decoded_file` CHANGE `airtime_reference` `airtime_reference` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'Easel reference when movie has been added to Easel (not meaning linked to the product)';

ALTER TABLE `content_decoded_file` ADD `airtime_product_reference` VARCHAR(255) NULL AFTER `airtime_reference`;

ALTER TABLE `content_decoded_file` ADD `airtime_product_linking_status` VARCHAR(255) NULL COMMENT 'Status of the linking movie, after encoding, with the (Easel) product.' AFTER `airtime_product_reference`, ADD `airtime_product_linking_date` DATETIME NULL AFTER `airtime_product_linking_status`;
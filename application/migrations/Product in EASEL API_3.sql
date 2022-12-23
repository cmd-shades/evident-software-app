ALTER TABLE `availability_window` ADD `content_id` INT(11) UNSIGNED NULL AFTER `product_id`;

ALTER TABLE `availability_window` ADD `easel_marketId` VARCHAR(255) NULL AFTER `easel_priceBandId`;

ALTER TABLE `availability_window` ADD `territory_id` INT(11) UNSIGNED NULL AFTER `content_id`;
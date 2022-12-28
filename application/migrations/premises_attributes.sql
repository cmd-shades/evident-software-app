CREATE TABLE `premises_attributes` (
`id` bigint(14) unsigned NOT NULL AUTO_INCREMENT,
`premise_id` bigint(14) unsigned NOT NULL,
`attribute_id` int(11) unsigned NOT NULL,
`attribute_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
`attribute_value` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
`ordering` int(11) DEFAULT NULL,
`date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`created_by` int(11) DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `premise_id` (`premise_id`),
KEY `pattr_id` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

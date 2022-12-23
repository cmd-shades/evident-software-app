INSERT INTO `user_module_categories` (`category_id`, `category_name`, `category_url_link`, `category_icon_class`, `category_color`, `is_active`, `description`) VALUES (NULL, 'Marketing', NULL, 'fas fa-cog', '#008b8b', '1', NULL);

INSERT INTO `user_modules` (`module_id`, `module_name`, `module_controller`, `module_url_link`, `module_ranking`, `module_price`, `description`, `is_active`, `category_id`, `app_icon_link`, `create_new_item_link`, `app_uuid`, `app_name`, `app_img_url`) VALUES (NULL, 'Marketing', 'marketing', '/marketing', '13', '10.00', NULL, '1', '13', NULL, '', NULL, NULL, '\\assets\\images\\app-logos\\marketing-manager.png');

INSERT INTO `account_modules` (`id`, `account_id`, `module_id`, `standard_price`, `adjusted_price`, `license_valid_from`, `license_valid_to`, `license_type`, `created_on`, `last_modified`, `last_modified_by`) VALUES (NULL, '1', '13', '10.00', '10.00', NULL, NULL, NULL, CURRENT_TIME(), NULL, NULL);

INSERT INTO `user_module_items` (`module_item_id`, `module_item_tab`, `module_item_name`, `module_item_url_link`, `module_item_desc`, `module_item_icon_class`, `module_item_sort`, `is_active`, `module_id`, `show_in_sidebar`) VALUES (NULL, 'details', 'details', '', 'Access Marketing details', '', '1', '1', '13', '0');

CREATE TABLE `marketing_modules` (
  `module_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT 'NULL',
  `module_url_link` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL DEFAULT 'NULL',
  `module_order` int(3) UNSIGNED NOT NULL,
  `is_active` int(1) UNSIGNED NOT NULL,
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `marketing_modules` (`module_id`, `account_id`, `module_name`, `description`, `module_url_link`, `image_url`, `module_order`, `is_active`, `archived`) VALUES
(1, 1, 'Current Titles', 'Marketing materials related to Current Titles', 'current_titles', 'assets\\images\\app-logos\\marketing-current-titles.png', 1, 1, 0);

ALTER TABLE `marketing_modules`
  ADD PRIMARY KEY (`module_id`);

ALTER TABLE `marketing_modules`
  MODIFY `module_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
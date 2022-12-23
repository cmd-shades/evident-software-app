## every entry needs to be adjusted to particular server values !!!

## New module
INSERT INTO `user_modules` (`module_id`, `module_name`, `module_controller`, `module_url_link`, `module_ranking`, `module_price`, `description`, `is_active`, `category_id`, `app_icon_link`, `create_new_item_link`, `app_uuid`, `app_name`, `app_img_url`) VALUES
(12, 'Report Manager', 'report', '/report', 12, '10.00', NULL, 1, 12, NULL, '', NULL, NULL, '\\assets\\images\\app-logos\\report-manager.png');

## Access to the new module for Admin and for Richard
INSERT INTO `user_module_access` (`id`, `account_id`, `user_id`, `module_id`, `has_access`, `is_module_admin`) VALUES (NULL, '1', '1', '12', '1', '1'), (NULL, '1', '4', '12', '1', '1');

## Needed for Permission tab
INSERT INTO `user_module_categories` (`category_id`, `category_name`, `category_url_link`, `category_icon_class`, `category_color`, `is_active`, `description`) VALUES (NULL, 'Report', NULL, 'fas fa-cog', '#008b8b', '1', NULL);

INSERT INTO `user_module_items` (`module_item_id`, `module_item_tab`, `module_item_name`, `module_item_url_link`, `module_item_desc`, `module_item_icon_class`, `module_item_sort`, `is_active`, `module_id`, `show_in_sidebar`) VALUES (NULL, 'details', 'details', '', 'Access Report details', '', '1', '1', '12', '0')

INSERT INTO `user_module_item_permissions` (`id`, `user_id`, `account_id`, `module_id`, `module_item_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `is_admin`, `item_permissions`, `last_modified`, `last_modified_by`) VALUES (NULL, '1', '1', '12', '63', '1', '1', '1', '1', '1', NULL, '2020-06-26 10:09:31', NULL), (NULL, '4', '1', '12', '63', '1', '1', '1', '1', '1', NULL, '2020-06-26 10:09:06', NULL);

## Added a category for the Report module
INSERT INTO `account_modules` (`id`, `account_id`, `module_id`, `standard_price`, `adjusted_price`, `license_valid_from`, `license_valid_to`, `license_type`, `created_on`, `last_modified`, `last_modified_by`) VALUES (NULL, '1', '12', '10.00', '10.00', NULL, NULL, NULL, CURRENT_TIMESTAMP, NULL, NULL);


## Added a category for the Channel module
INSERT INTO `account_modules` (`id`, `account_id`, `module_id`, `standard_price`, `adjusted_price`, `license_valid_from`, `license_valid_to`, `license_type`, `created_on`, `last_modified`, `last_modified_by`) VALUES (NULL, '1', '11', '10.00', '10.00', NULL, NULL, NULL, '2020-08-13 11:53:08', NULL, NULL);
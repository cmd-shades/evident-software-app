ALTER TABLE `asset`   
  ADD COLUMN `external_asset_ref` VARCHAR(255) NULL AFTER `last_modified_by`,
  ADD COLUMN `external_asset_created_on` DATETIME NULL AFTER `external_asset_ref`,
  ADD COLUMN `external_asset_updated_on` DATETIME NULL AFTER `external_asset_created_on`;
  
ALTER TABLE `asset_types`   
  ADD COLUMN `external_asset_types_ref` VARCHAR(255) NULL AFTER `primary_attribute_id`,
  ADD COLUMN `external_asset_types_created_on` DATETIME NULL AFTER `external_asset_types_ref`,
  ADD COLUMN `external_asset_types_updated_on` DATETIME NULL AFTER `external_asset_types_created_on`;

ALTER TABLE `site`   
  ADD COLUMN `external_site_ref` VARCHAR(255) NULL AFTER `archived`,
  ADD COLUMN `external_site_created_on` DATETIME NULL AFTER `external_site_ref`,
  ADD COLUMN `external_site_updated_on` DATETIME NULL AFTER `external_site_created_on`;  

ALTER TABLE `job`   
  ADD COLUMN `external_job_ref` VARCHAR(255) NULL AFTER `archived_by`,
  ADD COLUMN `external_job_created_on` DATETIME NULL AFTER `external_job_ref`,
  ADD COLUMN `external_job_updated_on` DATETIME NULL AFTER `external_job_created_on`; 
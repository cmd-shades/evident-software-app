ALTER TABLE `asset_document_uploads`   
  ADD COLUMN `approval_status` ENUM('Approved','Declined','Pending') DEFAULT 'Pending'  NULL AFTER `vehicle_reg`,
  ADD COLUMN `approval_date` DATETIME NULL AFTER `approval_status`,
  ADD COLUMN `approval_action_by` INT(11) NULL AFTER `approval_date`;
  
ALTER TABLE `customer_document_uploads`   
  ADD COLUMN `approval_status` ENUM('Approved','Declined','Pending') DEFAULT 'Pending'  NULL AFTER `vehicle_reg`,
  ADD COLUMN `approval_date` DATETIME NULL AFTER `approval_status`,
  ADD COLUMN `approval_action_by` INT(11) NULL AFTER `approval_date`;
  
ALTER TABLE `fleet_document_uploads`   
  ADD COLUMN `approval_status` ENUM('Approved','Declined','Pending') DEFAULT 'Pending'  NULL AFTER `vehicle_reg`,
  ADD COLUMN `approval_date` DATETIME NULL AFTER `approval_status`,
  ADD COLUMN `approval_action_by` INT(11) NULL AFTER `approval_date`;
  
ALTER TABLE `job_document_uploads`   
  ADD COLUMN `approval_status` ENUM('Approved','Declined','Pending') DEFAULT 'Pending'  NULL AFTER `vehicle_reg`,
  ADD COLUMN `approval_date` DATETIME NULL AFTER `approval_status`,
  ADD COLUMN `approval_action_by` INT(11) NULL AFTER `approval_date`;
  
ALTER TABLE `people_document_uploads`   
  ADD COLUMN `approval_status` ENUM('Approved','Declined','Pending') DEFAULT 'Pending'  NULL AFTER `vehicle_reg`,
  ADD COLUMN `approval_date` DATETIME NULL AFTER `approval_status`,
  ADD COLUMN `approval_action_by` INT(11) NULL AFTER `approval_date`;
  
ALTER TABLE `premises_document_uploads`   
  ADD COLUMN `approval_status` ENUM('Approved','Declined','Pending') DEFAULT 'Pending'  NULL AFTER `premises_id`,
  ADD COLUMN `approval_date` DATETIME NULL AFTER `approval_status`,
  ADD COLUMN `approval_action_by` INT(11) NULL AFTER `approval_date`;
  
ALTER TABLE `site_document_uploads`   
  ADD COLUMN `approval_status` ENUM('Approved','Declined','Pending') DEFAULT 'Pending'  NULL AFTER `vehicle_reg`,
  ADD COLUMN `approval_date` DATETIME NULL AFTER `approval_status`,
  ADD COLUMN `approval_action_by` INT(11) NULL AFTER `approval_date`;
  

ALTER TABLE `asset_document_uploads`
  CHANGE `approval_date` `approval_action_date` DATETIME NULL;
  
ALTER TABLE `customer_document_uploads`
  CHANGE `approval_date` `approval_action_date` DATETIME NULL;
  
ALTER TABLE `fleet_document_uploads`
  CHANGE `approval_date` `approval_action_date` DATETIME NULL;
  
ALTER TABLE `job_document_uploads`
  CHANGE `approval_date` `approval_action_date` DATETIME NULL;
  
ALTER TABLE `people_document_uploads`
  CHANGE `approval_date` `approval_action_date` DATETIME NULL;
  
ALTER TABLE `premises_document_uploads`
  CHANGE `approval_date` `approval_action_date` DATETIME NULL;
  
ALTER TABLE `site_document_uploads`
  CHANGE `approval_date` `approval_action_date` DATETIME NULL;

ALTER TABLE `content_document_uploads` ADD `airtime_status` VARCHAR(255) NULL AFTER `airtime_reference`, ADD `airtime_status_update_date` DATETIME NULL AFTER `airtime_status`;
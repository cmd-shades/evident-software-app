ALTER TABLE `site` ADD `invoice_to` ENUM('integrator','site') NOT NULL DEFAULT 'integrator' AFTER `invoice_currency_id`;
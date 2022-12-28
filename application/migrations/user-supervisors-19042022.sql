ALTER TABLE `user`   
  ADD COLUMN `is_supervisor` TINYINT(1) DEFAULT 0  NULL AFTER `can_be_assigned_jobs`,
  ADD COLUMN `supervisor_id` INT(11) NULL AFTER `is_supervisor`,
  COLLATE=utf8mb4_general_ci;
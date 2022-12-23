ALTER TABLE `content_decoded_file` ADD `aws_uploading_date` DATETIME NULL AFTER `aws_status`;


CREATE TABLE `coggins_debugging` (
  `request_id` int(11) NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `request_name` varchar(255) NOT NULL,
  `request_data` text NOT NULL,
  `full_response` text NOT NULL,
  `api-status` varchar(255) NOT NULL,
  `api-code` varchar(255) NOT NULL,
  `api-message` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `requested_by` int(11) UNSIGNED NOT NULL,
  `requested_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `coggins_debugging`
  ADD PRIMARY KEY (`request_id`);

ALTER TABLE `coggins_debugging`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;
  
  
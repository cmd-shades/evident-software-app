CREATE TABLE `genre_type` (
  `type_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `type_name` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `easel_id` varchar(255) DEFAULT NULL,
  `easel_name` varchar(255) DEFAULT NULL,
  `easel_exclusive` tinyint(1) DEFAULT 0,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED NOT NULL,
  `last_modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `archived` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `genre_type` (`type_id`, `account_id`, `type_name`, `alt_text`, `easel_id`, `easel_name`, `easel_exclusive`, `created_date`, `created_by`, `last_modified_date`, `modified_by`, `archived`, `active`) VALUES
(1, 1, 'Feature Film Genres', 'Feature Film Genres', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', 'Feature Film Genres', 0, '2021-08-10 15:09:45', 4, NULL, NULL, 0, 1),
(2, 1, 'TV Genres', 'TV Genres', '90825bf8-403e-a7bd-a67d-ac2f62b2c5fb', 'TV Genres', 0, '2021-08-10 15:11:46', 4, NULL, NULL, 0, 1),
(3, 1, 'Adult Genres', 'Adult Genres', '110529ab-743c-1d4b-a1df-543f703e74f7', 'Adult Genres', 0, '2021-08-10 15:12:00', 4, NULL, NULL, 0, 1);

ALTER TABLE `genre_type`
  ADD PRIMARY KEY (`type_id`);

ALTER TABLE `genre_type`
  MODIFY `type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- table `genre`
--

CREATE TABLE `genre` (
  `genre_id` int(11) UNSIGNED NOT NULL,
  `account_id` int(11) UNSIGNED NOT NULL,
  `genre_name` varchar(255) DEFAULT NULL,
  `genre_type_id` int(11) UNSIGNED DEFAULT NULL,
  `easel_id` varchar(255) DEFAULT NULL,
  `easel_name` varchar(255) DEFAULT NULL,
  `easel_categoryTypeId` varchar(255) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `last_modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `modified_by` int(11) UNSIGNED DEFAULT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `genre` (`genre_id`, `account_id`, `genre_name`, `genre_type_id`, `easel_id`, `easel_name`, `easel_categoryTypeId`, `created_date`, `created_by`, `last_modified_date`, `modified_by`, `archived`, `active`) VALUES
(1, 1, 'Genre Test Wojciech', 1, '1e235b32-dc7b-c2f5-83c6-0355a4edd3eb', 'Genre Test Wojciech', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-10 15:58:31', 4, NULL, NULL, 0, 1);

ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre_id`);

ALTER TABLE `genre`
  MODIFY `genre_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

CREATE TABLE `genre` (
  `genre_id` int(11) NOT NULL,
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
(1, 1, 'Action', 1, '75b2d3c0-6674-f451-92e5-ac397a89cbff', 'Action', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 12:51:53', 4, NULL, NULL, 0, 1),
(2, 1, 'Classic', 1, '4f6abccb-5aa8-ff2d-47f8-68b46da622bf', 'Classic', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 12:52:52', 4, NULL, NULL, 0, 1),
(3, 1, 'Drama', 1, '7992a632-81ca-d792-5e26-e0a6bbeb250b', 'Drama', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 12:53:32', 4, NULL, NULL, 0, 1),
(4, 1, 'Comedy', 1, '0aa7ddbb-3190-7dc6-efb5-b31e25a4ffb0', 'Comedy', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 12:53:45', 4, NULL, NULL, 0, 1),
(5, 1, 'Amateur', 3, 'f7c3357e-2836-be76-7c97-547820f7079f', 'Amateur', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:54:09', 4, NULL, NULL, 0, 1),
(6, 1, 'Asian', 3, 'd6061a99-6ab8-9a2c-ab33-215e6356d7f9', 'Asian', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:54:22', 4, NULL, NULL, 0, 1),
(7, 1, 'Breasts', 3, '4a309fe2-dc0b-7385-0af1-819d3cefba83', 'Breasts', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:54:33', 4, NULL, NULL, 0, 1),
(8, 1, 'Clothed', 3, 'fc91e21a-ba8e-734c-b34d-a65f6391bdc6', 'Clothed', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:55:00', 4, NULL, NULL, 0, 1),
(9, 1, 'Explicit', 3, 'd9ece9b8-8bc5-02ba-567e-b510c6671e85', 'Explicit', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:55:14', 4, NULL, NULL, 0, 1),
(10, 1, 'Feature', 3, 'e9958376-3e85-352d-dbd6-f8294dbf9043', 'Feature', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:55:27', 4, NULL, NULL, 0, 1),
(11, 1, 'Fetish', 3, '4f287f5e-5596-9d24-f0f6-d010bf6de393', 'Fetish', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:55:48', 4, NULL, NULL, 0, 1),
(12, 1, 'Lesbian', 3, '215c4436-e710-bad6-cf89-90229e3296c0', 'Lesbian', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:56:02', 4, NULL, NULL, 0, 1),
(13, 1, 'MILF', 3, '3fa2efb4-2b2c-a438-eb77-61da551be0a2', 'MILF', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:56:14', 4, NULL, NULL, 0, 1),
(14, 1, 'Soft', 3, 'f1db4aed-e7a4-8556-6d7c-6713b32802b1', 'Soft', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:56:27', 4, NULL, NULL, 0, 1),
(15, 1, 'Teen', 3, '51402666-fe2f-5237-9b4f-cddc7b8a5f48', 'Teen', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:56:47', 4, NULL, NULL, 0, 1),
(16, 1, 'Fantasy', 3, '30295c67-be4a-19c3-a475-f489882ddc92', 'Fantasy', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:56:58', 4, NULL, NULL, 0, 1),
(17, 1, 'Anal', 3, '5d86745a-f5ea-dd08-232e-c8e89f261e09', 'Anal', '110529ab-743c-1d4b-a1df-543f703e74f7', '2021-08-16 12:57:11', 4, NULL, NULL, 0, 1),
(18, 1, 'Documentary', 2, 'ee4e2802-68cc-108a-d291-1c88d136c036', 'Documentary', '90825bf8-403e-a7bd-a67d-ac2f62b2c5fb', '2021-08-16 13:05:57', 4, NULL, NULL, 0, 1),
(19, 1, 'Drama Series', 2, '56c48790-bcf0-d82a-5607-b30986412f3c', 'Drama Series', '90825bf8-403e-a7bd-a67d-ac2f62b2c5fb', '2021-08-16 13:06:12', 4, NULL, NULL, 0, 1),
(20, 1, 'Nature', 2, 'd51a2b29-0715-e17a-299d-b39eab54052b', 'Nature', '90825bf8-403e-a7bd-a67d-ac2f62b2c5fb', '2021-08-16 13:06:24', 4, NULL, NULL, 0, 1),
(21, 1, 'Entertainment', 2, '5ad5592f-d08a-18dc-3e2a-baef2605ced8', 'Entertainment', '90825bf8-403e-a7bd-a67d-ac2f62b2c5fb', '2021-08-16 13:06:36', 4, NULL, NULL, 0, 1),
(22, 1, 'Wellness', 2, '56f7d29c-9357-7d9e-a84c-cff45edba337', 'Wellness', '90825bf8-403e-a7bd-a67d-ac2f62b2c5fb', '2021-08-16 13:06:49', 4, NULL, NULL, 0, 1),
(23, 1, 'Family', 1, '0f82260d-3af5-496f-f00a-ec5327717299', 'Family', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 13:07:31', 4, NULL, NULL, 0, 1),
(24, 1, 'Horror', 1, 'ddb083f8-46b4-53fa-bcc2-f086c236ca7d', 'Horror', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 13:07:45', 4, NULL, NULL, 0, 1),
(25, 1, 'Thriller', 1, 'f0eec176-b05c-cd19-b137-88ca88b8f72e', 'Thriller', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 13:07:58', 4, NULL, NULL, 0, 1),
(26, 1, 'Adult', 1, '861ae7a2-27df-bf84-5f48-917ff596f4c0', 'Adult', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 13:08:15', 4, NULL, NULL, 0, 1),
(27, 1, 'Disney', 1, 'af59cf56-daad-919d-6f89-89d6070fe132', 'Disney', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 13:08:30', 4, NULL, NULL, 0, 1),
(28, 1, 'Romance', 1, '2d4daa64-ffb4-1c81-fff4-b4eb99da5584', 'Romance', 'b0e2f197-8724-fd5d-7624-accc0ffa6d51', '2021-08-16 13:08:49', 4, NULL, NULL, 0, 1);


ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre_id`);

ALTER TABLE `genre`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;


--
-- `genre_type`
--

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
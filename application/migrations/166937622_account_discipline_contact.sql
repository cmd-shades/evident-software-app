CREATE TABLE account_discipline_contact (
    `id`            INT AUTO_INCREMENT,
    `discipline_id` INT UNSIGNED                        NOT NULL,
    `user_id`       INT UNSIGNED                        NOT NULL,
    `account_id`    INT UNSIGNED                        NOT NULL,
    `email`         VARCHAR(100)                        NULL,
    `number`        VARCHAR(20)                        NULL,
    `is_active`     TINYINT(1) DEFAULT 1               NULL,
    `archived`     TINYINT(1) DEFAULT 0               NULL,
    `date_created`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    `last_modified` TIMESTAMP DEFAULT NULL              NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT discipline_users_pk primary key (`id`),
    CONSTRAINT discipline_users_account___fk
        FOREIGN KEY (`account_id`) REFERENCES account (account_id),
    CONSTRAINT discipline_users_discipline___fk
        FOREIGN KEY (`discipline_id`) REFERENCES discipline (discipline_id),
    CONSTRAINT discipline_users_user___fk
        FOREIGN KEY (`user_id`) REFERENCES user (id)
) COLLATE = utf8mb4_general_ci;

CREATE INDEX account_discipline_contact_account_id_index
    ON account_discipline_contact (`account_id`);
CREATE INDEX account_discipline_contact_discipline_id_index
    ON account_discipline_contact (`discipline_id`);
CREATE INDEX account_discipline_contact_user_id_index
    ON account_discipline_contact (`user_id`);
CREATE INDEX account_discipline_contact_is_active_index
    ON account_discipline_contact (`is_active`);
CREATE INDEX account_discipline_contact_archived_index
    ON account_discipline_contact (`archived`);

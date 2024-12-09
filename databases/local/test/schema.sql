SET default_storage_engine = INNODB;

CREATE DATABASE `%( DB_NAME )%`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci
;

USE `%( DB_NAME )%`;






CREATE TABLE `customer`
(
    `id`                                 BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `name`                               VARCHAR(255)                                                 NULL,

    `birth.name`                         VARCHAR(255)                                                 NULL,
    `birth.surname`                      VARCHAR(255)                                                 NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,



    PRIMARY KEY (`id`),

    UNIQUE  KEY (`name`)
)
;

CREATE TABLE `card`
(
    `id`                                 BIGINT UNSIGNED AUTO_INCREMENT                           NOT NULL,

    `customer`                           BIGINT UNSIGNED                                          NOT NULL,

    `name`                               VARCHAR(255)                                             NOT NULL,

    `sn`                                 VARCHAR(255)                                             NOT NULL,

    `datetime.insert`                    TIMESTAMP                                                NOT NULL,



    PRIMARY KEY (`id`),

    UNIQUE  KEY (`customer`,`name`),

    FOREIGN KEY (`customer`)
    REFERENCES `customer` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
;